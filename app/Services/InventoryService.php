<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryLog;

class InventoryService
{
    /**
     * Add stock to inventory (PURCHASE, SALES_RETURN, STOCK_OPNAME incoming).
     *
     * Finds or creates the Inventory record keyed by (product_id, unit_cost),
     * then creates a new InventoryDetail batch and an audit InventoryLog.
     */
    public static function addStock(
        int $productId,
        int $unitCost,
        int $quantity,
        \DateTimeInterface|string $receivedAt,
        string $source,
        int $referenceId,
        ?string $notes = null,
    ): InventoryDetail {
        $inventory = Inventory::firstOrCreate([
            'product_id' => $productId,
            'unit_cost'  => $unitCost,
        ]);

        $inventoryDetail = InventoryDetail::create([
            'inventory_id' => $inventory->id,
            'quantity'     => $quantity,
            'received_at'  => $receivedAt,
        ]);

        InventoryLog::create([
            'inventory_detail_id' => $inventoryDetail->id,
            'source'              => $source,
            'reference_id'        => $referenceId,
            'quantity'            => $quantity,
            'balance_after'       => $quantity,
            'notes'               => $notes,
        ]);

        return $inventoryDetail;
    }

    /**
     * Deduct stock using FIFO order (SALE, STOCK_OPNAME outgoing).
     *
     * Iterates inventory_details ordered by received_at ASC (oldest first)
     * and deducts quantity until fulfilled. Throws if insufficient stock.
     */
    public static function deductStock(
        int $productId,
        int $quantity,
        string $source,
        int $referenceId,
        ?string $notes = null,
    ): void {
        $remaining = $quantity;

        $batches = InventoryDetail::whereHas('inventory', fn($q) => $q->where('product_id', $productId))
            ->where('quantity', '>', 0)
            ->orderBy('received_at')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $deduct = min($batch->quantity, $remaining);
            $batch->decrement('quantity', $deduct);

            InventoryLog::create([
                'inventory_detail_id' => $batch->id,
                'source'              => $source,
                'reference_id'        => $referenceId,
                'quantity'            => -$deduct,
                'balance_after'       => $batch->quantity,
                'notes'               => $notes,
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new \RuntimeException("Insufficient stock for product ID {$productId}. Short by {$remaining} units.");
        }
    }

    /**
     * Total available stock across all batches for a product.
     */
    public static function getStock(int $productId): int
    {
        return (int) InventoryDetail::whereHas('inventory', fn($q) => $q->where('product_id', $productId))
            ->sum('quantity');
    }
}
