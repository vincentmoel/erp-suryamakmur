<?php

namespace Database\Seeders;

use App\Enums\PurchaseStatus;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $vendor1 = Vendor::where('name', 'PT Sumber Makmur Pangan')->first();
        $vendor2 = Vendor::where('name', 'CV Dingin Abadi')->first();

        if (! $vendor1 || ! $vendor2) {
            return;
        }

        $sku = fn(string $sku) => Product::where('sku', $sku)->first();

        $purchases = [
            [
                'vendor'    => $vendor1,
                'status'    => PurchaseStatus::RECEIVED,
                'date'      => '2026-05-10',
                'notes'     => 'Pembelian rutin Mei 2026.',
                'details'   => [
                    ['product' => $sku('NGT-AYM-500'), 'qty' => 50, 'price' => 28000],
                    ['product' => $sku('SOS-SAP-375'), 'qty' => 40, 'price' => 25000],
                    ['product' => $sku('NGT-IKN-400'), 'qty' => 30, 'price' => 22000],
                ],
            ],
            [
                'vendor'    => $vendor2,
                'status'    => PurchaseStatus::RECEIVED,
                'date'      => '2026-05-15',
                'notes'     => 'Pembelian seafood beku.',
                'details'   => [
                    ['product' => $sku('SFD-UDG-1KG'), 'qty' => 20, 'price' => 78000],
                    ['product' => $sku('SFD-CMI-1KG'), 'qty' => 15, 'price' => 60000],
                    ['product' => $sku('SFD-DRI-500'), 'qty' => 25, 'price' => 35000],
                ],
            ],
            [
                'vendor'    => $vendor1,
                'status'    => PurchaseStatus::ORDERED,
                'date'      => '2026-06-03',
                'notes'     => 'Purchase order Juni 2026.',
                'details'   => [
                    ['product' => $sku('DIM-AYM-300'), 'qty' => 30, 'price' => 27000],
                    ['product' => $sku('SIM-UDG-300'), 'qty' => 25, 'price' => 33000],
                ],
            ],
        ];

        foreach ($purchases as $data) {
            $subtotal = collect($data['details'])->sum(fn($d) => $d['qty'] * $d['price']);

            $purchase = Purchase::firstOrCreate(
                [
                    'vendor_id'     => $data['vendor']->id,
                    'purchase_date' => $data['date'],
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

            if ($purchase->wasRecentlyCreated) {
                foreach ($data['details'] as $line) {
                    if (! $line['product']) {
                        continue;
                    }

                    $lineSubtotal = $line['qty'] * $line['price'];

                    $purchase->details()->create([
                        'product_id'      => $line['product']->id,
                        'quantity'        => $line['qty'],
                        'unit_price'      => $line['price'],
                        'discount_amount' => 0,
                        'tax_percent'     => 0,
                        'tax_amount'      => 0,
                        'subtotal'        => $lineSubtotal,
                    ]);
                }
            }
        }
    }
}
