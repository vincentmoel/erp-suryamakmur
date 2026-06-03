<?php

namespace App\Enums;

use App\Traits\EnumLabel;
use App\Traits\EnumToArray;

enum VendorType: string
{
    use EnumToArray, EnumLabel;

    case INDIVIDUAL = "INDIVIDUAL";
    case COMPANY = "COMPANY";

    public function label(): string
    {
        return match ($this) {
            self::INDIVIDUAL => "Individual",
            self::COMPANY => "Company",
        };
    }
}
