<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('07########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'buyer',
            'status' => 'active',
            'consent_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'farmer'])
            ->afterCreating(function (User $user) {
                $user->farmerProfile()->create([
                    'stall' => 'Shed '.fake()->numberBetween(1, 8),
                    'location' => fake()->randomElement(['Nakasero Central', 'Nakasero Shed Area', 'Bugolobi', 'Kisenyi']),
                    'verified_at' => now(),
                ]);
            });
    }

    public function unverifiedFarmer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'farmer'])
            ->afterCreating(function (User $user) {
                $user->farmerProfile()->create([
                    'stall' => 'Shed '.fake()->numberBetween(1, 8),
                    'location' => 'Nakasero Central',
                    'verified_at' => null,
                ]);
            });
    }

    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'buyer']);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'suspended']);
    }
}
