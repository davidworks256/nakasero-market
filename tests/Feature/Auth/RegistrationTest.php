<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_buyers_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Buyer',
            'email' => 'test@example.com',
            'phone' => '0701111111',
            'role' => 'buyer',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $response->assertRedirect(route('buyer.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'buyer']);
    }

    public function test_new_farmers_register_with_stall_details(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Farmer',
            'email' => 'farmer@example.com',
            'phone' => '0702222222',
            'role' => 'farmer',
            'stall' => 'Shed 2, Stall 9',
            'location' => 'Nakasero Central',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => 1,
        ]);

        $response->assertRedirect(route('farmer.dashboard'));

        $farmer = User::where('email', 'farmer@example.com')->firstOrFail();

        $this->assertDatabaseHas('farmer_profiles', [
            'user_id' => $farmer->id,
            'stall' => 'Shed 2, Stall 9',
            'verified_at' => null,
        ]);
    }

    public function test_registration_requires_consent(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Consent',
            'email' => 'nc@example.com',
            'role' => 'buyer',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('consent');
        $this->assertGuest();
    }

    public function test_admin_role_cannot_be_self_selected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => 1,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
