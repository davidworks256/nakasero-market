<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        if ($request->has('ids')) {
            $ids = collect($request->validate([
                'ids' => ['required', 'array', 'min:2', 'max:3'],
                'ids.*' => ['integer'],
            ])['ids'])->map(fn ($id) => (int) $id);

            $listings = Listing::query()
                ->active()
                ->fromVerifiedFarmers()
                ->with(['category', 'farmer.farmerProfile', 'images'])
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn (Listing $l) => $ids->search($l->id))
                ->values();

            abort_if($listings->count() < 2, 400, 'Pick at least two active listings to compare.');

            return view('listings.compare', [
                'listings' => $listings,
                'availableListings' => collect(),
                'categories' => $categories,
            ]);
        }

        $selectedCategory = $request->query('category');

        $availableListings = Listing::query()
            ->active()
            ->fromVerifiedFarmers()
            ->when($selectedCategory, fn ($q) => $q->where('category_id', $selectedCategory))
            ->with(['category', 'farmer.farmerProfile', 'images'])
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('listings.compare', [
            'listings' => collect(),
            'availableListings' => $availableListings,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
