<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function request(User $buyer, Listing $listing, int $quantity, ?string $note): Order
    {
        if ($quantity < 1 || $quantity > $listing->quantity_available) {
            throw ValidationException::withMessages([
                'quantity' => "You can request between 1 and {$listing->quantity_available} {$listing->unit}.",
            ]);
        }

        $order = Order::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'farmer_id' => $listing->farmer_id,
            'quantity' => $quantity,
            'unit_price' => $listing->price,
            'status' => OrderStatus::Requested,
            'note' => $note,
        ]);

        $order->farmer->notify(new OrderStatusChanged($order, 'requested'));

        return $order;
    }

    public function transition(Order $order, User $actor, OrderStatus $target): Order
    {
        $allowedForTarget = match ($target) {
            OrderStatus::Accepted, OrderStatus::Declined, OrderStatus::Completed => $actor->id === $order->farmer_id,
            OrderStatus::Cancelled => $actor->id === $order->buyer_id || $actor->id === $order->farmer_id,
            default => false,
        };

        abort_unless($allowedForTarget, 403);

        if (! $order->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "An order that is {$order->status->value} cannot be moved to {$target->value}.",
            ]);
        }

        DB::transaction(function () use ($order, $target) {
            if ($target === OrderStatus::Accepted && $order->listing) {
                $order->listing->decrement('quantity_available', min($order->quantity, $order->listing->quantity_available));

                if ($order->listing->fresh()->quantity_available === 0) {
                    $order->listing->update(['status' => Listing::STATUS_SOLD_OUT]);
                }
            }

            $order->update(['status' => $target]);
        });

        $recipient = $actor->id === $order->buyer_id ? $order->farmer : $order->buyer;
        $recipient?->notify(new OrderStatusChanged($order, $target->value));

        return $order->fresh();
    }
}
