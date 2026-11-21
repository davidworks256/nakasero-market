<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function view(?User $user, Listing $listing): bool
    {
        if ($user && ($user->isAdmin() || $user->id === $listing->farmer_id)) {
            return true;
        }

        return $listing->status === Listing::STATUS_ACTIVE && $listing->farmer->isVerifiedFarmer();
    }

    public function create(User $user): bool
    {
        return $user->isFarmer();
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->id === $listing->farmer_id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->id === $listing->farmer_id;
    }
}
