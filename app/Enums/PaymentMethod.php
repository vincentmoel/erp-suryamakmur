<?php

namespace App\Enums;

use App\Traits\EnumLabel;
use App\Traits\EnumToArray;

enum PaymentMethod: string
{
    use EnumToArray, EnumLabel;

    case CASH          = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK         = 'check';

    public function label(): string
    {
        return match ($this) {
            self::CASH          => 'Tunai',
            self::BANK_TRANSFER => 'Transfer Bank',
            self::CHECK         => 'Cek / Giro',
        };
    }
}
