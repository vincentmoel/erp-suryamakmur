<?php

namespace App\Services;

use App\Models\InventoryDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Total available stock for a single product.
     */
    public static function getStock(int $productId): int
    {
        return (int) InventoryDetail::join('inventories', 'inventories.id', '=', 'inventory_details.inventory_id')
            ->where('inventories.product_id', $productId)
            ->sum('inventory_details.quantity');
    }

    /**
     * Total stock for multiple products in one query.
     * Returns a Collection keyed by product_id: ['product_id' => {product_id, stock}]
     *
     * Usage:
     *   $stocks = ProductService::getStockBulk([1, 2, 3]);
     *   $stocks->get(1)?->stock ?? 0
     */
    public static function getStockBulk(array $productIds): Collection
    {
        return InventoryDetail::join('inventories', 'inventories.id', '=', 'inventory_details.inventory_id')
            ->whereIn('inventories.product_id', $productIds)
            ->select('inventories.product_id', DB::raw('SUM(inventory_details.quantity) as stock'))
            ->groupBy('inventories.product_id')
            ->get()
            ->keyBy('product_id');
    }

    /**
     * Stock breakdown by unit_cost for a single product, excluding depleted batches.
     * Returns a Collection of {unit_cost, quantity}.
     */
    public static function getStockByUnitCost(int $productId): Collection
    {
        return InventoryDetail::join('inventories', 'inventories.id', '=', 'inventory_details.inventory_id')
            ->where('inventories.product_id', $productId)
            ->select('inventories.unit_cost', DB::raw('SUM(inventory_details.quantity) as quantity'))
            ->groupBy('inventories.unit_cost')
            ->orderBy('inventories.unit_cost')
            ->having('quantity', '>', 0)
            ->get();
    }
}
