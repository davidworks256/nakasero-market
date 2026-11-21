<?php

namespace App\Observers;

use App\Models\Listing;
use App\Models\PriceHistory;

class ListingObserver
{
    public function updated(Listing $listing): void
    {
        if ($listing->isDirty('price')) {
            PriceHistory::create([
                'listing_id' => $listing->id,
                'price' => $listing->price,
                'recorded_at' => now(),
            ]);
        }
    }
}
