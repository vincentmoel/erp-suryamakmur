<?php 

namespace App\Enums;

use App\Traits\EnumToArray;

enum Module : string{
    use EnumToArray;

    case User = "User";
    case Role = "Role";
    case Developer = "Developer";
    case Dashboard = "Dashboard";
    case Config = "Config";
    case Category = "Category";
    case Unit = "Unit";
    case Customer = "Customer";
}