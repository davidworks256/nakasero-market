<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'buyer_id',
        'farmer_id',
        'quantity',
        'unit_price',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return ['status' => OrderStatus::class];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function totalPrice(): int
    {
        return $this->quantity * $this->unit_price;
    }

    public function isParticipant(User $user): bool
    {
        return in_array($user->id, [$this->buyer_id, $this->farmer_id], true);
    }
}
