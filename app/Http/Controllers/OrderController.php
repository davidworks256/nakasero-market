<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Listing;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function create(Request $request, Listing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', "max:{$listing->quantity_available}"],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($request->user()->isBuyer(), 403);
        abort_unless($listing->status === Listing::STATUS_ACTIVE, 404);

        $order = $this->orders->request($request->user(), $listing, (int) $validated['quantity'], $validated['note'] ?? null);

        return redirect()->route('orders.show', $order)
            ->with('status', 'Order request sent to the farmer. Agree the price and payment directly with them.');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = Order::query()
            ->with(['listing', 'buyer', 'farmer'])
            ->when($user->isBuyer(), fn ($q) => $q->where('buyer_id', $user->id))
            ->when($user->isFarmer(), fn ($q) => $q->where('farmer_id', $user->id))
            ->latest()
            ->paginate(15);

        return view('orders.index', [
            'orders' => $orders,
            'title' => $user->isFarmer() ? 'Sales (order requests)' : 'My orders',
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['listing.category', 'buyer.farmerProfile', 'farmer.farmerProfile']);

        return view('orders.show', compact('order'));
    }

    public function update(Request $request, Order $order, string $action): RedirectResponse
    {
        $this->authorize('update', $order);

        $target = match ($action) {
            'accept' => OrderStatus::Accepted,
            'decline' => OrderStatus::Declined,
            'complete' => OrderStatus::Completed,
            'cancel' => OrderStatus::Cancelled,
            default => abort(404),
        };

        $this->orders->transition($order, $request->user(), $target);

        return back()->with('status', "Order #{$order->id} is now {$target->value}.");
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user->isFarmer() || $user->isBuyer(), 403);

        $filename = ($user->isFarmer() ? 'sales' : 'purchases').'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order', 'Status', 'Quantity', 'Unit price (UGX)', 'Total (UGX)', 'Date', 'Listing', 'Buyer', 'Farmer']);

            Order::query()
                ->where($user->isFarmer() ? 'farmer_id' : 'buyer_id', $user->id)
                ->with(['listing', 'buyer', 'farmer'])
                ->chunk(200, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->id,
                            $order->status->value,
                            $order->quantity,
                            $order->unit_price,
                            $order->totalPrice(),
                            $order->created_at->toDateString(),
                            $order->listing?->title,
                            $order->buyer?->name,
                            $order->farmer?->name,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
