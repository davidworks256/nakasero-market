<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function listing(int $qty = 100): Listing
    {
        return Listing::factory()->create(['quantity_available' => $qty, 'price' => 2000]);
    }

    private function requestOrder(User $buyer, Listing $listing, int $qty = 10): TestResponse
    {
        return $this->actingAs($buyer)->post(route('buyer.order.request', $listing), [
            'quantity' => $qty,
            'note' => 'Pick up Saturday',
        ]);
    }

    public function test_buyer_can_request_an_order(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();

        $response = $this->requestOrder($buyer, $listing);

        $order = Order::firstOrFail();
        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('orders.show', $order));

        $this->actingAs($buyer)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee("Order #{$order->id}");

        $this->assertSame(OrderStatus::Requested, $order->status);
        $this->assertSame(2000, $order->unit_price);
        $this->assertSame($listing->farmer_id, $order->farmer_id);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $listing->farmer_id,
            'type' => OrderStatusChanged::class,
        ]);
    }

    public function test_over_quantity_requests_fail_validation(): void
    {
        $listing = $this->listing(5);
        $buyer = User::factory()->buyer()->create();

        $this->requestOrder($buyer, $listing, 6)->assertSessionHasErrors('quantity');
        $this->assertSame(0, Order::count());
    }

    public function test_accepting_reduces_available_quantity(): void
    {
        $listing = $this->listing(30);
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing, 10)->assertRedirect();

        $order = Order::firstOrFail();
        $this->actingAs($listing->farmer)
            ->post(route('orders.update', [$order, 'accept']))
            ->assertRedirect();

        $this->assertSame(OrderStatus::Accepted, $order->fresh()->status);
        $this->assertSame(20, $listing->fresh()->quantity_available);
    }

    public function test_accepting_last_quantity_marks_listing_sold_out(): void
    {
        $listing = $this->listing(10);
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing, 10)->assertRedirect();

        $this->actingAs($listing->farmer)
            ->post(route('orders.update', [Order::firstOrFail(), 'accept']))
            ->assertRedirect();

        $this->assertSame(Listing::STATUS_SOLD_OUT, $listing->fresh()->status);
    }

    public function test_invalid_status_transitions_are_blocked(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing);
        $order = Order::firstOrFail();

        // requested -> completed is not allowed
        $this->actingAs($listing->farmer)
            ->post(route('orders.update', [$order, 'complete']))
            ->assertSessionHasErrors('status');
        $this->assertSame(OrderStatus::Requested, $order->fresh()->status);

        // buyer may not accept their own request
        $this->actingAs($buyer)
            ->post(route('orders.update', [$order, 'accept']))
            ->assertForbidden();

        // decline then try to complete
        $this->actingAs($listing->farmer)->post(route('orders.update', [$order, 'decline']));
        $this->assertSame(OrderStatus::Declined, $order->fresh()->status);
        $this->actingAs($listing->farmer)
            ->post(route('orders.update', [$order, 'accept']))
            ->assertSessionHasErrors('status');
    }

    public function test_either_side_can_cancel_before_completion(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing);
        $order = Order::firstOrFail();

        $this->actingAs($buyer)->post(route('orders.update', [$order, 'cancel']));
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    public function test_a_user_cannot_read_another_users_order(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing);
        $order = Order::firstOrFail();

        $stranger = User::factory()->buyer()->create();
        $this->actingAs($stranger)->get(route('orders.show', $order))->assertForbidden();

        $otherFarmer = User::factory()->farmer()->create();
        $this->actingAs($otherFarmer)->post(route('orders.update', [$order, 'accept']))->assertForbidden();
    }

    public function test_full_happy_path_to_completed(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing);
        $order = Order::firstOrFail();

        $this->actingAs($listing->farmer)->post(route('orders.update', [$order, 'accept']));
        $this->actingAs($listing->farmer)->post(route('orders.update', [$order, 'complete']));

        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);

        // no further actions allowed
        $this->actingAs($buyer)
            ->post(route('orders.update', [$order, 'cancel']))
            ->assertSessionHasErrors('status');
    }

    public function test_history_export_returns_csv(): void
    {
        $listing = $this->listing();
        $buyer = User::factory()->buyer()->create();
        $this->requestOrder($buyer, $listing);

        $response = $this->actingAs($buyer)->get(route('orders.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Order,Status', $response->streamedContent());
    }
}
