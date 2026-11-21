<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    public $timestamps = false;

    protected $table = 'price_history';

    protected $fillable = ['listing_id', 'price', 'recorded_at'];

    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
