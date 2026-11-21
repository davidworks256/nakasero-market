<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_contact_a_farmer_about_a_listing(): void
    {
        $listing = Listing::factory()->create();
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->post(route('buyer.contact', $listing), [
            'body' => 'Is this available on Saturday?',
        ]);

        $conversation = Conversation::firstOrFail();
        $this->assertSame($listing->farmer_id, $conversation->farmer_id);
        $this->assertSame($buyer->id, $conversation->buyer_id);
        $this->assertSame('Is this available on Saturday?', $conversation->messages->first()->body);
        $response->assertRedirect(route('inbox.show', $conversation));
    }

    public function test_contacting_twice_reuses_the_same_conversation(): void
    {
        $listing = Listing::factory()->create();
        $buyer = User::factory()->buyer()->create();

        $this->actingAs($buyer)->post(route('buyer.contact', $listing), ['body' => 'First']);
        $this->actingAs($buyer)->post(route('buyer.contact', $listing), ['body' => 'Second']);

        $this->assertSame(1, Conversation::count());
        $this->assertSame(2, Conversation::first()->messages()->count());
    }

    public function test_outsiders_cannot_read_or_reply_to_a_conversation(): void
    {
        $listing = Listing::factory()->create();
        $buyer = User::factory()->buyer()->create();
        $this->actingAs($buyer)->post(route('buyer.contact', $listing), ['body' => 'Secret deal']);

        $conversation = Conversation::firstOrFail();
        $stranger = User::factory()->buyer()->create();

        $this->actingAs($stranger)->get(route('inbox.show', $conversation))->assertForbidden();
        $this->actingAs($stranger)->post(route('inbox.reply', $conversation), ['body' => 'hi'])->assertForbidden();
    }

    public function test_both_participants_can_read_the_thread(): void
    {
        $listing = Listing::factory()->create();
        $buyer = User::factory()->buyer()->create();
        $this->actingAs($buyer)->post(route('buyer.contact', $listing), ['body' => 'Hello farmer']);

        $conversation = Conversation::firstOrFail();

        $this->actingAs($buyer)->get(route('inbox.show', $conversation))->assertOk();
        $this->actingAs($listing->farmer)->get(route('inbox.show', $conversation))->assertOk()->assertSee('Hello farmer');
    }

    public function test_farmers_cannot_use_buyer_contact_route(): void
    {
        $listing = Listing::factory()->create();
        $otherFarmer = User::factory()->farmer()->create();

        $this->actingAs($otherFarmer)->post(route('buyer.contact', $listing), ['body' => 'yo'])->assertForbidden();
    }
}
