<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['listing_id', 'buyer_id', 'farmer_id'];

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

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('buyer_id', $user->id)
            ->orWhere('farmer_id', $user->id));
    }

    public function otherParticipant(User $user): User
    {
        return $user->id === $this->buyer_id ? $this->farmer : $this->buyer;
    }

    public function latestMessage(): ?Message
    {
        return $this->messages()->latest('id')->first();
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
