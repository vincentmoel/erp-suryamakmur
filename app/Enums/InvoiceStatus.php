<?php

namespace App\Enums;

use App\Traits\EnumLabel;
use App\Traits\EnumToArray;

enum InvoiceStatus: string
{
    use EnumToArray, EnumLabel;

    case DRAFT              = 'draft';
    case WAITING_FOR_PAYMENT = 'waiting_for_payment';
    case PAID               = 'paid';
    case PARTIALLY_PAID     = 'partially_paid';
    case CANCELLED          = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT               => 'Draft',
            self::WAITING_FOR_PAYMENT => 'Waiting for Payment',
            self::PAID                => 'Paid',
            self::PARTIALLY_PAID      => 'Partially Paid',
            self::CANCELLED           => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT               => 'badge-secondary',
            self::WAITING_FOR_PAYMENT => 'badge-warning',
            self::PAID                => 'badge-success',
            self::PARTIALLY_PAID      => 'badge-info',
            self::CANCELLED           => 'badge-destructive',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::WAITING_FOR_PAYMENT]);
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::DRAFT, self::WAITING_FOR_PAYMENT, self::PARTIALLY_PAID]);
    }
}
