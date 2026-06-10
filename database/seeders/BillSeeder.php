<?php

namespace Database\Seeders;

use App\Enums\BillStatus;
use App\Enums\InventorySource;
use App\Models\Bill;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $vendor1 = Vendor::where('name', 'PT Sumber Makmur Pangan')->first();
        $vendor2 = Vendor::where('name', 'CV Dingin Abadi')->first();

        if (! $vendor1 || ! $vendor2) {
            return;
        }

        Auth::loginUsingId(User::first()->id);

        $sku = fn(string $sku) => Product::where('sku', $sku)->first();

        $bills = [
            // ── Bill 1: Mei 2026 — NGT/SOS/Ikan (RECEIVED) ───────────────
            [
                'vendor'  => $vendor1,
                'status'  => BillStatus::RECEIVED,
                'date'    => '2026-05-10',
                'notes'   => 'Pembelian rutin Mei 2026.',
                'details' => [
                    ['product' => $sku('NGT-AYM-500'), 'qty' => 50, 'price' => 28000],
                    ['product' => $sku('SOS-SAP-375'), 'qty' => 40, 'price' => 25000],
                    ['product' => $sku('NGT-IKN-400'), 'qty' => 30, 'price' => 22000],
                ],
            ],
            // ── Bill 2: Mei 2026 — Seafood (RECEIVED) ────────────────────
            [
                'vendor'  => $vendor2,
                'status'  => BillStatus::RECEIVED,
                'date'    => '2026-05-15',
                'notes'   => 'Pembelian seafood beku.',
                'details' => [
                    ['product' => $sku('SFD-UDG-1KG'), 'qty' => 20, 'price' => 78000],
                    ['product' => $sku('SFD-CMI-1KG'), 'qty' => 15, 'price' => 60000],
                    ['product' => $sku('SFD-DRI-500'), 'qty' => 25, 'price' => 35000],
                ],
            ],
            // ── Bill 3: Jun 2026 — Dimsum (ORDERED, belum diterima) ──────
            [
                'vendor'  => $vendor1,
                'status'  => BillStatus::ORDERED,
                'date'    => '2026-06-03',
                'notes'   => 'Order Juni 2026.',
                'details' => [
                    ['product' => $sku('DIM-AYM-300'), 'qty' => 30, 'price' => 27000],
                    ['product' => $sku('SIM-UDG-300'), 'qty' => 25, 'price' => 33000],
                ],
            ],
        ];

        foreach ($bills as $data) {
            $subtotal = collect($data['details'])->sum(fn($d) => $d['qty'] * $d['price']);

            DB::transaction(function () use ($data, $subtotal) {
                $bill = Bill::firstOrCreate(
                    [
                        'vendor_id' => $data['vendor']->id,
                        'bill_date' => $data['date'],
                    ],
                    [
                        'status'          => $data['status']->value,
                        'notes'           => $data['notes'],
                        'subtotal'        => $subtotal,
                        'discount_amount' => 0,
                        'tax_percent'     => 0,
                        'tax_amount'      => 0,
                        'grand_total'     => $subtotal,
                    ]
                );

                if (! $bill->wasRecentlyCreated) {
                    return;
                }

                foreach ($data['details'] as $line) {
                    if (! $line['product']) {
                        continue;
                    }

                    $bill->details()->create([
                        'product_id'      => $line['product']->id,
                        'quantity'        => $line['qty'],
                        'unit_price'      => $line['price'],
                        'discount_amount' => 0,
                        'tax_percent'     => 0,
                        'tax_amount'      => 0,
                        'subtotal'        => $line['qty'] * $line['price'],
                    ]);

                    if ($data['status'] === BillStatus::RECEIVED) {
                        InventoryService::addStock(
                            productId:   $line['product']->id,
                            unitCost:    $line['price'],
                            quantity:    $line['qty'],
                            receivedAt:  $data['date'],
                            source:      InventorySource::PURCHASE,
                            referenceId: $bill->id,
                            notes:       'Bill #' . $bill->code,
                        );
                    }
                }
            });
        }

        Auth::logout();
    }
}
