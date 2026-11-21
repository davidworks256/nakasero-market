<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Requested = 'requested';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Requested => [self::Accepted, self::Declined, self::Cancelled],
            self::Accepted => [self::Completed, self::Cancelled],
            default => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Requested => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::Accepted => 'bg-blue-100 text-blue-800 border-blue-200',
            self::Completed => 'bg-green-100 text-green-800 border-green-200',
            self::Declined => 'bg-red-100 text-red-800 border-red-200',
            self::Cancelled => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }
}
