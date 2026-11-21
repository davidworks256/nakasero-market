<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    protected $fillable = ['listing_id', 'path'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function url(): string
    {
        $basename = basename($this->path);
        if (file_exists(public_path('images/produce/'.$basename))) {
            return asset('images/produce/'.$basename);
        }

        return asset('storage/'.$this->path);
    }
}
