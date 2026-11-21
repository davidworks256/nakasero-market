<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        $produce = fake()->randomElement([
            'Matooke (bunch)', 'Ripe tomatoes', 'Irish potatoes', 'Onions', 'Spinach (sukuma wiki)',
            'Carrots', 'Cabbage', 'Sweet potatoes', 'Cassava', 'Maize (dry)', 'Beans', 'Green chillies',
            'Bananas (sweet)', 'Avocados', 'Passion fruit', 'Eggplant', 'Green paper', 'Dried cassava chips',
        ]);

        return [
            'farmer_id' => User::factory()->farmer(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'title' => $produce.' '.fake()->unique()->numerify('###'),
            'description' => 'Fresh '.Str::lower($produce).' from the farm, harvested this morning.',
            'unit' => fake()->randomElement(['kg', 'bunch', 'crate', 'sack']),
            'price' => fake()->numberBetween(500, 150000),
            'quantity_available' => fake()->numberBetween(5, 500),
            'location' => fake()->randomElement(['Nakasero Central', 'Nakasero Shed Area', 'Bugolobi', 'Kisenyi']),
            'status' => Listing::STATUS_ACTIVE,
        ];
    }

    public function soldOut(): static
    {
        return $this->state(fn () => ['status' => Listing::STATUS_SOLD_OUT, 'quantity_available' => 0]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['status' => Listing::STATUS_HIDDEN]);
    }
}
