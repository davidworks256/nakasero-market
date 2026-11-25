<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = (string) env('ADMIN_PASSWORD', 'Nakasero@Admin2026!');
        $demoPassword = (string) env('DEMO_USER_PASSWORD', 'Nakasero@Demo2026!');

        User::updateOrCreate(
            ['email' => 'admin@nakasero.test'],
            [
                'name' => 'Market Admin',
                'phone' => '0700000001',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]
        );

        $categories = collect([
            'Vegetables',
            'Fruits',
            'Grains',
            'Tubers & Roots',
            'Herbs & Spices',
            'Poultry & Eggs',
            'Fish & Seafood',
            'Legumes & Pulses',
            'Dairy & Honey',
        ])->map(fn (string $name) => Category::firstOrCreate(['name' => $name]));

        if (! User::where('email', 'demo-farmer@nakasero.test')->exists()) {
            $farmer = User::create([
                'name' => 'DEMO Farmer (Sarah)',
                'email' => 'demo-farmer@nakasero.test',
                'phone' => '0700000002',
                'password' => Hash::make($demoPassword),
                'role' => 'farmer',
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]);
            $farmer->farmerProfile()->create([
                'stall' => 'Shed 3, Stall 12',
                'location' => 'Nakasero Central',
                'verified_at' => now(),
            ]);

            $unverified = User::create([
                'name' => 'DEMO Farmer (pending verification)',
                'email' => 'demo-farmer2@nakasero.test',
                'phone' => '0700000003',
                'password' => Hash::make($demoPassword),
                'role' => 'farmer',
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]);
            $unverified->farmerProfile()->create([
                'stall' => 'Shed 7, Stall 4',
                'location' => 'Nakasero Shed Area',
                'verified_at' => null,
            ]);
        }

        $verifiedFarmer = User::where('email', 'demo-farmer@nakasero.test')->first();

        $samples = [
            ['Matooke (bunch)', 'Vegetables', 'bunch', 8000, 120, 'Nakasero Central', 'matooke'],
            ['Ripe tomatoes', 'Vegetables', 'kg', 2500, 300, 'Nakasero Central', 'tomatoes'],
            ['Irish potatoes', 'Vegetables', 'kg', 1800, 500, 'Bugolobi', 'irish-potatoes'],
            ['Onions', 'Vegetables', 'kg', 3000, 200, 'Nakasero Shed Area', 'onions'],
            ['Sukuma wiki', 'Vegetables', 'bunch', 1000, 400, 'Nakasero Central', 'sukuma-wiki'],
            ['Maize (dry)', 'Grains', 'kg', 1500, 800, 'Kisenyi', 'maize'],
            ['Beans', 'Grains', 'kg', 4500, 350, 'Nakasero Shed Area', 'beans'],
            ['Sweet bananas', 'Fruits', 'bunch', 6000, 90, 'Nakasero Central', 'sweet-bananas'],
            ['Avocados', 'Fruits', 'kg', 2000, 150, 'Bugolobi', 'avocados'],
            ['Passion fruit', 'Fruits', 'crate', 35000, 40, 'Nakasero Central', 'passion-fruit'],
            ['Cabbage', 'Vegetables', 'head', 2500, 180, 'Nakasero Shed Area', 'cabbage'],
            ['Carrots', 'Vegetables', 'kg', 2200, 220, 'Bugolobi', 'carrots'],
            ['Fresh ginger', 'Herbs & Spices', 'kg', 5000, 150, 'Nakasero Central', 'onions'],
            ['Sweet potatoes', 'Tubers & Roots', 'kg', 2000, 320, 'Nakasero Shed Area', 'carrots'],
            ['Local farm eggs', 'Poultry & Eggs', 'tray', 12000, 75, 'Nakasero Central', 'avocados'],
            ['Fresh Tilapia', 'Fish & Seafood', 'kg', 14000, 50, 'Bugolobi', 'matooke'],
            ['Red Beauty groundnuts', 'Legumes & Pulses', 'kg', 5500, 260, 'Kisenyi', 'beans'],
            ['Pure organic honey', 'Dairy & Honey', 'kg', 18000, 45, 'Nakasero Central', 'sweet-bananas'],
        ];

        foreach ($samples as [$title, $category, $unit, $price, $qty, $location, $slug]) {
            $listing = Listing::updateOrCreate(
                ['title' => $title],
                [
                    'farmer_id' => $verifiedFarmer->id,
                    'category_id' => $categories->firstWhere('name', $category)->id,
                    'description' => "Fresh {$title}, harvested this morning at the farm.",
                    'unit' => $unit,
                    'price' => $price,
                    'quantity_available' => $qty,
                    'location' => $location,
                    'status' => Listing::STATUS_ACTIVE,
                ]
            );

            $source = base_path('resources/demo-images/produce/'.$slug.'.jpg');
            if (is_file($source)) {
                $public = Storage::disk('public');
                if (! $public->exists('listings/'.$slug.'.jpg')) {
                    $public->put('listings/'.$slug.'.jpg', file_get_contents($source));
                }
                $listing->images()->firstOrCreate(['path' => 'listings/'.$slug.'.jpg']);
            }
        }

        if (! User::where('email', 'demo-buyer@nakasero.test')->exists()) {
            User::create([
                'name' => 'DEMO Buyer (James)',
                'email' => 'demo-buyer@nakasero.test',
                'phone' => '0700000004',
                'password' => Hash::make($demoPassword),
                'role' => 'buyer',
                'email_verified_at' => now(),
                'consent_at' => now(),
            ]);
        }
    }
}
