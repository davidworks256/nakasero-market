<?php

namespace App\Models;

use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SOLD_OUT = 'sold_out';

    public const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'farmer_id',
        'category_id',
        'title',
        'description',
        'unit',
        'price',
        'quantity_available',
        'location',
        'status',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('category', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"));
        }));
    }

    public function scopePriceBetween(Builder $query, ?int $min, ?int $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeInCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, fn (Builder $q) => $q->where('category_id', $categoryId));
    }

    public function scopeInLocation(Builder $query, ?string $location): Builder
    {
        return $query->when($location, fn (Builder $q) => $q->where('location', 'like', "%{$location}%"));
    }

    public function scopeFromVerifiedFarmers(Builder $query): Builder
    {
        return $query->whereHas('farmer.farmerProfile', fn (Builder $q) => $q->whereNotNull('verified_at'));
    }

    public function isSoldOut(): bool
    {
        return $this->quantity_available === 0 || $this->status === self::STATUS_SOLD_OUT;
    }

    public function formattedPrice(): string
    {
        return 'UGX '.number_format($this->price);
    }
}
