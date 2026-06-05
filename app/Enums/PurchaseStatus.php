<?php

namespace App\Enums;

use App\Traits\EnumLabel;
use App\Traits\EnumToArray;

enum PurchaseStatus: string
{
    use EnumToArray, EnumLabel;

    case DRAFT     = 'draft';
    case ORDERED   = 'ordered';
    case RECEIVED  = 'received';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::ORDERED   => 'Ordered',
            self::RECEIVED  => 'Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT     => 'badge-secondary',
            self::ORDERED   => 'badge-warning',
            self::RECEIVED  => 'badge-success',
            self::CANCELLED => 'badge-destructive',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::ORDERED]);
    }

    public function canReceive(): bool
    {
        return in_array($this, [self::DRAFT, self::ORDERED]);
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::DRAFT, self::ORDERED]);
    }
}
