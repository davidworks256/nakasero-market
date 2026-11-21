<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'location' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'string', 'in:newest,price_asc,price_desc'],
        ]);

        $listings = Listing::query()
            ->active()
            ->fromVerifiedFarmers()
            ->search($filters['search'] ?? null)
            ->inCategory($filters['category'] ?? null)
            ->inLocation($filters['location'] ?? null)
            ->priceBetween(
                isset($filters['min_price']) ? (int) $filters['min_price'] : null,
                isset($filters['max_price']) ? (int) $filters['max_price'] : null,
            )
            ->with(['category', 'farmer.farmerProfile', 'images'])
            ->when(
                ($filters['sort'] ?? 'newest') === 'price_asc',
                fn ($q) => $q->orderBy('price'),
                fn ($q) => $q->when(($filters['sort'] ?? '') === 'price_desc', fn ($qq) => $qq->orderByDesc('price'))
                    ->latest('updated_at'),
            )
            ->paginate(12)
            ->withQueryString();

        return view('listings.browse', [
            'listings' => $listings,
            'categories' => Category::orderBy('name')->get(),
            'locations' => Listing::active()->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    public function show(Request $request, Listing $listing): View
    {
        $this->authorize('view', $listing);

        $listing->load(['category', 'farmer.farmerProfile', 'images']);

        $contactUnlocked = (bool) $request->user();

        $related = Listing::active()
            ->fromVerifiedFarmers()
            ->inCategory($listing->category_id)
            ->where('id', '!=', $listing->id)
            ->with(['farmer.farmerProfile', 'images'])
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('listings.show', compact('listing', 'contactUnlocked', 'related'));
    }
}
