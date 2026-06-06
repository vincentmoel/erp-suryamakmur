<?php

namespace App\Services;

use App\Enums\InventorySource;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class InventoryService
{
    /**
     * Add stock to inventory (PURCHASE, SALES_RETURN, STOCK_OPNAME incoming).
     */
    public static function addStock(
        int $productId,
        int $unitCost,
        int $quantity,
        \DateTimeInterface|string $receivedAt,
        InventorySource $source,
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
            'source'              => $source->value,
            'reference_id'        => $referenceId,
            'quantity'            => $quantity,
            'notes'               => $notes,
            'context'             => self::buildContext(),
            'user_snapshot'       => self::buildUserSnapshot(),
        ]);

        return $inventoryDetail;
    }

    /**
     * Add multiple stock batches efficiently (M + N + 1 queries vs 3N for individual calls).
     *
     * Each item in $batches must have: product_id, unit_cost, quantity, received_at.
     * All batches share the same source, referenceId, and notes.
     *
     * Query breakdown:
     *   - M queries for firstOrCreate (M = unique product_id+unit_cost pairs, often 1)
     *   - N queries for insertGetId on inventory_details (needed to get reliable IDs)
     *   - 1 bulk insert for inventory_logs
     */
    public static function addStockBulk(
        array $batches,
        InventorySource $source,
        int $referenceId,
        ?string $notes = null,
    ): void {
        if (empty($batches)) {
            return;
        }

        $context      = self::buildContext();
        $userSnapshot = self::buildUserSnapshot();
        $now          = now()->toDateTimeString();

        // Resolve unique (product_id, unit_cost) pairs → inventory id (deduplicates)
        $inventoryMap = [];
        foreach ($batches as $batch) {
            $key = $batch['product_id'] . '_' . $batch['unit_cost'];
            if (! isset($inventoryMap[$key])) {
                $inventory = Inventory::firstOrCreate([
                    'product_id' => $batch['product_id'],
                    'unit_cost'  => $batch['unit_cost'],
                ]);
                $inventoryMap[$key] = $inventory->id;
            }
        }

        // Insert each detail individually to get reliable auto-increment IDs,
        // then bulk insert all logs in one query.
        $logRows = [];
        foreach ($batches as $batch) {
            $key      = $batch['product_id'] . '_' . $batch['unit_cost'];
            $detailId = DB::table('inventory_details')->insertGetId([
                'inventory_id' => $inventoryMap[$key],
                'quantity'     => $batch['quantity'],
                'received_at'  => $batch['received_at'],
            ]);

            $logRows[] = [
                'inventory_detail_id' => $detailId,
                'source'              => $source->value,
                'reference_id'        => $referenceId,
                'quantity'            => $batch['quantity'],
                'notes'               => $notes,
                'context'             => json_encode($context),
                'user_snapshot'       => json_encode($userSnapshot),
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        InventoryLog::insert($logRows);
    }

    /**
     * Deduct stock using FIFO order (SALE, STOCK_OPNAME outgoing).
     *
     * Returns an array of batch allocations: [inventory_detail_id, quantity, unit_cost].
     * Throws RuntimeException if stock is insufficient.
     */
    public static function deductStock(
        int $productId,
        int $quantity,
        InventorySource $source,
        int $referenceId,
        ?string $notes = null,
    ): array {
        $remaining    = $quantity;
        $allocations  = [];
        $context      = self::buildContext();
        $userSnapshot = self::buildUserSnapshot();

        $batches = InventoryDetail::whereHas('inventory', fn($q) => $q->where('product_id', $productId))
            ->where('quantity', '>', 0)
            ->orderBy('received_at')
            ->with('inventory')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $deduct = min($batch->quantity, $remaining);
            $batch->decrement('quantity', $deduct);

            InventoryLog::create([
                'inventory_detail_id' => $batch->id,
                'source'              => $source->value,
                'reference_id'        => $referenceId,
                'quantity'            => -$deduct,
                'notes'               => $notes,
                'context'             => $context,
                'user_snapshot'       => $userSnapshot,
            ]);

            $allocations[] = [
                'inventory_detail_id' => $batch->id,
                'quantity'            => $deduct,
                'unit_cost'           => $batch->inventory->unit_cost,
            ];

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new \RuntimeException("Insufficient stock for product ID {$productId}. Short by {$remaining} units.");
        }

        return $allocations;
    }

    /**
     * Restore a specific inventory batch (used when reversing a SALE on cancel/re-post).
     */
    public static function restoreStockBatch(
        int $inventoryDetailId,
        int $quantity,
        InventorySource $source,
        int $referenceId,
        ?string $notes = null,
    ): void {
        $batch = InventoryDetail::lockForUpdate()->findOrFail($inventoryDetailId);
        $batch->increment('quantity', $quantity);

        InventoryLog::create([
            'inventory_detail_id' => $inventoryDetailId,
            'source'              => $source->value,
            'reference_id'        => $referenceId,
            'quantity'            => $quantity,
            'notes'               => $notes,
            'context'             => self::buildContext(),
            'user_snapshot'       => self::buildUserSnapshot(),
        ]);
    }

    /**
     * Total available stock across all batches for a product.
     */
    public static function getStock(int $productId): int
    {
        return (int) InventoryDetail::whereHas('inventory', fn($q) => $q->where('product_id', $productId))
            ->sum('quantity');
    }

    private static function buildContext(): array
    {
        $request = Request::instance();
        $route   = $request->route();

        return [
            'controller'  => $route?->getActionName(),
            'method'      => $route?->getActionMethod(),
            'url'         => $request->fullUrl(),
            'http_method' => $request->method(),
            'ip'          => $request->ip(),
        ];
    }

    private static function buildUserSnapshot(): ?array
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'roles'    => $user->roles->pluck('name')->toArray(),
        ];
    }
}
