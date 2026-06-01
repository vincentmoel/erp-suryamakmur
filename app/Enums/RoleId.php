<?php 

namespace App\Enums;

use App\Traits\EnumToArray;

enum RoleId: string {
    
    use EnumToArray;

    case Developer = "1";
}