<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Services\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function __construct(private ImageStorageService $images) {}

    public function index(Request $request): View
    {
        $listings = $request->user()->listings()
            ->with('category')
            ->latest('updated_at')
            ->paginate(15);

        return view('farmer.listings.index', compact('listings'));
    }

    public function create(): View
    {
        return view('farmer.listings.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreListingRequest $request): RedirectResponse
    {
        $listing = $request->user()->listings()->create($request->validated());

        foreach ($request->file('photos', []) as $photo) {
            $listing->images()->create(['path' => $this->images->storeResized($photo)]);
        }

        return redirect()->route('farmer.listings.index')
            ->with('status', "Listing \"{$listing->title}\" saved. Buyers see it once your account is verified.");
    }

    public function edit(Request $request, Listing $listing): View
    {
        $this->authorize('update', $listing);

        return view('farmer.listings.edit', [
            'listing' => $listing->load('images'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $listing->update($request->safe()->except(['photos', 'remove_images']));

        foreach ($request->file('photos', []) as $photo) {
            $listing->images()->create(['path' => $this->images->storeResized($photo)]);
        }

        foreach ($request->input('remove_images', []) as $imageId) {
            $image = ListingImage::where('listing_id', $listing->id)->find($imageId);
            if ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        return redirect()->route('farmer.listings.index')->with('status', 'Listing updated.');
    }

    public function updateStock(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'quantity_available' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        $listing->update($validated + [
            'status' => $listing->status === Listing::STATUS_HIDDEN
                ? Listing::STATUS_HIDDEN
                : ((int) $validated['quantity_available'] > 0 ? Listing::STATUS_ACTIVE : Listing::STATUS_SOLD_OUT),
        ]);

        return back()->with('status', 'Price and quantity updated.');
    }

    public function destroy(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        $listing->delete();

        return redirect()->route('farmer.listings.index')->with('status', 'Listing removed.');
    }
}
