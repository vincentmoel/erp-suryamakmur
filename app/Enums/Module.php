<?php 

namespace App\Enums;

use App\Traits\EnumToArray;

enum Module : string{
    use EnumToArray;

    case User = "User";
    case Role = "Role";
    case Developer = "Developer";
    case Dashboard = "Dashboard";
    case Customer = "Customer";
    case StationCategory = "StationCategory";
    case Duration = "Duration";
    case RentalStation = "RentalStation";
    case IpAddress = "IpAddress";
    case ItemCategory = "ItemCategory";
    case Item = "Item";
    case StockOpname = "StockOpname";
    case InventoryLog = "InventoryLog";
    case Order = "Order";
    case StationMonitoring = "StationMonitoring";
    case Invoice = "Invoice";
    case Discount = "Discount";
    case Config = "Config";
}