<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admins_cannot_reach_admin_pages(): void
    {
        $this->get(route('admin.overview'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->buyer()->create())->get(route('admin.overview'))->assertForbidden();
        $this->actingAs(User::factory()->farmer()->create())->get(route('admin.overview'))->assertForbidden();
    }

    public function test_admin_can_verify_a_farmer(): void
    {
        $farmer = User::factory()->unverifiedFarmer()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.users.verify', $farmer))
            ->assertRedirect();

        $this->assertNotNull($farmer->fresh()->farmerProfile->verified_at);
    }

    public function test_admin_can_suspend_a_user_who_is_signed_in(): void
    {
        $farmer = User::factory()->farmer()->create();

        $this->actingAs($farmer)->get(route('farmer.dashboard'))->assertOk();

        $this->actingAs(User::factory()->admin()->create())->post(route('admin.users.suspend', $farmer));

        $this->actingAs($farmer->fresh())->get(route('farmer.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_hide_and_restore_listings(): void
    {
        $admin = User::factory()->admin()->create();
        $listing = Listing::factory()->create();

        $this->actingAs($admin)->post(route('admin.listings.hide', $listing));
        $this->assertSame(Listing::STATUS_HIDDEN, $listing->fresh()->status);

        $this->actingAs($admin)->post(route('admin.listings.relist', $listing));
        $this->assertSame(Listing::STATUS_ACTIVE, $listing->fresh()->status);
    }

    public function test_admin_cannot_edit_prices_or_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $listing = Listing::factory()->create(['price' => 5000]);

        $this->actingAs($admin)
            ->put(route('farmer.listings.update', $listing), [
                'title' => $listing->title,
                'category_id' => $listing->category_id,
                'unit' => 'kg',
                'price' => 1,
                'quantity_available' => 1,
                'location' => 'X',
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->assertSame(5000, $listing->fresh()->price);
    }

    public function test_admin_manages_categories(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.categories.store'), ['name' => 'Fish'])->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Fish']);

        $category = Category::where('name', 'Fish')->firstOrFail();
        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
