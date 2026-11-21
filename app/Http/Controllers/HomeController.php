<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'categories' => Category::withCount(['listings' => fn ($q) => $q->active()->fromVerifiedFarmers()])
                ->orderBy('name')
                ->get(),
            'fresh' => Listing::active()
                ->fromVerifiedFarmers()
                ->with(['category', 'farmer.farmerProfile', 'images'])
                ->latest('updated_at')
                ->take(8)
                ->get(),
        ]);
    }
}
