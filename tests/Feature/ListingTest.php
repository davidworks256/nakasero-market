<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\PriceHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    private function farmer(): User
    {
        return User::factory()->farmer()->create();
    }

    public function test_farmer_can_create_a_listing(): void
    {
        Storage::fake('public');
        $farmer = $this->farmer();
        $category = Category::create(['name' => 'Vegetables']);

        $response = $this->actingAs($farmer)->post(route('farmer.listings.store'), [
            'title' => 'Ripe tomatoes',
            'category_id' => $category->id,
            'unit' => 'kg',
            'price' => 2500,
            'quantity_available' => 100,
            'location' => 'Nakasero Central',
            'status' => 'active',
            'photos' => [UploadedFile::fake()->image('tomato.jpg', 1600, 1600)],
        ]);

        $response->assertRedirect(route('farmer.listings.index'));

        $listing = Listing::firstOrFail();
        $this->assertSame($farmer->id, $listing->farmer_id);
        $this->assertCount(1, $listing->images);
        Storage::disk('public')->assertExists($listing->images->first()->path);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $farmer = $this->farmer();
        $category = Category::create(['name' => 'Vegetables']);

        $response = $this->actingAs($farmer)->post(route('farmer.listings.store'), [
            'title' => 'Bad upload',
            'category_id' => $category->id,
            'unit' => 'kg',
            'price' => 100,
            'quantity_available' => 1,
            'location' => 'Nakasero',
            'status' => 'active',
            'photos' => [UploadedFile::fake()->create('virus.php', 100)],
        ]);

        $response->assertSessionHasErrors('photos.0');
        $this->assertSame(0, Listing::count());
    }

    public function test_a_farmer_cannot_change_another_farmers_listing(): void
    {
        $listing = Listing::factory()->create();
        $intruder = $this->farmer();

        $this->actingAs($intruder)
            ->put(route('farmer.listings.update', $listing), [
                'title' => 'Hijacked',
                'category_id' => $listing->category_id,
                'unit' => 'kg',
                'price' => 1,
                'quantity_available' => 1,
                'location' => 'X',
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->patch(route('farmer.listings.stock', $listing), ['price' => 1, 'quantity_available' => 1])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('farmer.listings.destroy', $listing))
            ->assertForbidden();

        $this->assertNotSame('Hijacked', $listing->fresh()->title);
    }

    public function test_buyers_cannot_create_listings(): void
    {
        $buyer = User::factory()->buyer()->create();

        $this->actingAs($buyer)->get(route('farmer.listings.create'))->assertForbidden();
    }

    public function test_price_change_writes_history(): void
    {
        $listing = Listing::factory()->create(['price' => 1000]);

        $this->actingAs($listing->farmer)->patch(route('farmer.listings.stock', $listing), [
            'price' => 1500,
            'quantity_available' => $listing->quantity_available,
        ]);

        $this->assertSame(1500, $listing->fresh()->price);
        $this->assertDatabaseHas('price_history', ['listing_id' => $listing->id, 'price' => 1500]);
        $this->assertSame(1, PriceHistory::count());
    }

    public function test_quick_update_zero_quantity_marks_sold_out(): void
    {
        $listing = Listing::factory()->create(['quantity_available' => 5]);

        $this->actingAs($listing->farmer)->patch(route('farmer.listings.stock', $listing), [
            'price' => $listing->price,
            'quantity_available' => 0,
        ]);

        $this->assertSame(Listing::STATUS_SOLD_OUT, $listing->fresh()->status);
    }

    public function test_browse_only_shows_active_listings_from_verified_farmers(): void
    {
        $visible = Listing::factory()->create();
        $hidden = Listing::factory()->hidden()->create();
        $soldOut = Listing::factory()->soldOut()->create();
        $unverified = Listing::factory()->create(['farmer_id' => User::factory()->unverifiedFarmer()->create()->id]);

        $response = $this->get(route('listings.index'));

        $response->assertOk();
        $response->assertSee($visible->title);
        $response->assertDontSee($hidden->title);
        $response->assertDontSee($unverified->title);
    }

    public function test_guests_can_view_active_listing_details(): void
    {
        $listing = Listing::factory()->create();
        $hidden = Listing::factory()->hidden()->create();

        $this->get(route('listings.show', $listing))->assertOk();
        $this->get(route('listings.show', $hidden))->assertForbidden();
    }

    public function test_filters_return_the_right_rows(): void
    {
        $veg = Category::create(['name' => 'Vegetables']);
        $fruit = Category::create(['name' => 'Fruits']);

        $cheap = Listing::factory()->create(['title' => 'Cheap matooke', 'price' => 1000, 'category_id' => $veg->id, 'location' => 'Bugolobi']);
        $pricey = Listing::factory()->create(['title' => ' pricey mangoes', 'price' => 90000, 'category_id' => $fruit->id, 'location' => 'Kisenyi']);

        $this->get(route('listings.index', ['search' => 'matooke']))
            ->assertSee($cheap->title)->assertDontSee($pricey->title);

        $this->get(route('listings.index', ['category' => $fruit->id]))
            ->assertSee($pricey->title)->assertDontSee($cheap->title);

        $this->get(route('listings.index', ['max_price' => 5000]))
            ->assertSee($cheap->title)->assertDontSee($pricey->title);

        $this->get(route('listings.index', ['location' => 'Kisenyi']))
            ->assertSee($pricey->title)->assertDontSee($cheap->title);
    }

    public function test_comparison_requires_two_to_three_listings(): void
    {
        $a = Listing::factory()->create();
        $b = Listing::factory()->create();

        $this->get(route('compare', ['ids' => [$a->id]]))->assertSessionHasErrors('ids');
        $this->get(route('compare', ['ids' => [$a->id, $b->id]]))->assertOk();
        $c = Listing::factory()->create();
        $d = Listing::factory()->create();
        $this->get(route('compare', ['ids' => [$a->id, $b->id, $c->id, $d->id]]))->assertSessionHasErrors('ids');
    }
}
