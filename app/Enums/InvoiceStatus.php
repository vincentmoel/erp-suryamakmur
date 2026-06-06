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

    public function icon(): string
    {
        $svg = match ($this) {
            self::DRAFT               => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>',
            self::WAITING_FOR_PAYMENT => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
            self::PAID                => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
            self::PARTIALLY_PAID      => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>',
            self::CANCELLED           => '<path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
        };
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:0.85em;height:0.85em;flex-shrink:0;display:inline-block;vertical-align:middle;margin-right:0.3em;">' . $svg . '</svg>';
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
