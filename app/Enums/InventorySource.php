<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum InventorySource: string
{
    use EnumToArray;

    case PURCHASE     = 'PURCHASE';
    case SALE         = 'SALE';
    case SALES_RETURN = 'SALES_RETURN';
    case STOCK_OPNAME = 'STOCK_OPNAME';
}
