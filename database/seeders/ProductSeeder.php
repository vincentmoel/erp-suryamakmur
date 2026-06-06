<?php

namespace Database\Seeders;

use App\Enums\InventorySource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (! auth()->check()) {
            \Illuminate\Support\Facades\Auth::setUser(\App\Models\User::first());
        }

        // Resolve category & unit IDs by name
        $cat  = fn(string $name) => Category::where('name', $name)->value('id');
        $unit = fn(string $abbr) => Unit::where('abbreviation', $abbr)->value('id');

        // Each product may include 'initial_stocks' — array of {unit_cost, quantity, received_at}.
        // This mirrors the ProductController::store() flow exactly.
        $products = [
            // --- Nugget & Sosis ---
            [
                'category_id'   => $cat('Nugget & Sosis'),
                'unit_id'       => $unit('Pak'),
                'name'          => 'Nugget Ayam 500g',
                'sku'           => 'NGT-AYM-500',
                'description'   => 'Nugget ayam crispy, kemasan 500 gram.',
                'selling_price' => 35000,
                'stock_minimum' => 20,
                'is_active'     => true,
                'initial_stocks' => [
                    ['unit_cost' => 28000, 'quantity' => 50, 'received_at' => '2026-01-10'],
                    ['unit_cost' => 29000, 'quantity' => 30, 'received_at' => '2026-03-05'],
                ],
            ],
        //     [
        //         'category_id'   => $cat('Nugget & Sosis'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Sosis Sapi 375g',
        //         'sku'           => 'SOS-SAP-375',
        //         'description'   => 'Sosis sapi premium, kemasan 375 gram.',
        //         'selling_price' => 32000,
        //         'stock_minimum' => 20,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 25000, 'quantity' => 40, 'received_at' => '2026-01-15'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Nugget & Sosis'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Nugget Ikan 400g',
        //         'sku'           => 'NGT-IKN-400',
        //         'description'   => 'Nugget ikan, kemasan 400 gram.',
        //         'selling_price' => 30000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 23000, 'quantity' => 35, 'received_at' => '2026-02-01'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Nugget & Sosis'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Chicken Strip 500g',
        //         'sku'           => 'CHK-STP-500',
        //         'description'   => 'Potongan ayam tepung crispy, kemasan 500 gram.',
        //         'selling_price' => 38000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 30000, 'quantity' => 25, 'received_at' => '2026-02-10'],
        //         ],
        //     ],

        //     // --- Dimsum & Siomay ---
        //     [
        //         'category_id'   => $cat('Dimsum & Siomay'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Siomay Udang 300g',
        //         'sku'           => 'SIM-UDG-300',
        //         'description'   => 'Siomay udang premium, isi 20 pcs per pak.',
        //         'selling_price' => 42000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 33000, 'quantity' => 20, 'received_at' => '2026-01-20'],
        //             ['unit_cost' => 34000, 'quantity' => 15, 'received_at' => '2026-04-01'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Dimsum & Siomay'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Hakau Udang 250g',
        //         'sku'           => 'HKU-UDG-250',
        //         'description'   => 'Hakau udang kulit tipis, isi 10 pcs per pak.',
        //         'selling_price' => 38000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 30000, 'quantity' => 20, 'received_at' => '2026-02-15'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Dimsum & Siomay'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Dimsum Ayam Jamur 300g',
        //         'sku'           => 'DIM-AYM-300',
        //         'description'   => 'Dimsum ayam dengan jamur shiitake.',
        //         'selling_price' => 35000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 27000, 'quantity' => 25, 'received_at' => '2026-03-01'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Dimsum & Siomay'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Ceker Dimsum 250g',
        //         'sku'           => 'DIM-CEK-250',
        //         'description'   => 'Ceker ayam kukus bumbu dimsum.',
        //         'selling_price' => 28000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 21000, 'quantity' => 20, 'received_at' => '2026-03-10'],
        //         ],
        //     ],

        //     // --- Bakso & Otak-otak ---
        //     [
        //         'category_id'   => $cat('Bakso & Otak-otak'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Bakso Sapi Jumbo 500g',
        //         'sku'           => 'BKS-SAP-500',
        //         'description'   => 'Bakso sapi ukuran jumbo, isi 10 biji per pak.',
        //         'selling_price' => 40000,
        //         'stock_minimum' => 20,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 32000, 'quantity' => 40, 'received_at' => '2026-01-05'],
        //             ['unit_cost' => 33000, 'quantity' => 20, 'received_at' => '2026-04-10'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Bakso & Otak-otak'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Bakso Ikan 400g',
        //         'sku'           => 'BKS-IKN-400',
        //         'description'   => 'Bakso ikan tenggiri, kemasan 400 gram.',
        //         'selling_price' => 33000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 25000, 'quantity' => 30, 'received_at' => '2026-02-20'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Bakso & Otak-otak'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Otak-otak Ikan 200g',
        //         'sku'           => 'OTK-IKN-200',
        //         'description'   => 'Otak-otak ikan tenggiri, isi 10 pcs per pak.',
        //         'selling_price' => 25000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 18000, 'quantity' => 30, 'received_at' => '2026-03-15'],
        //         ],
        //     ],

        //     // --- Seafood ---
        //     [
        //         'category_id'   => $cat('Seafood'),
        //         'unit_id'       => $unit('Kg'),
        //         'name'          => 'Udang Kupas Beku 1 Kg',
        //         'sku'           => 'SFD-UDG-1KG',
        //         'description'   => 'Udang kupas beku ukuran 51/60, kemasan 1 Kg.',
        //         'selling_price' => 95000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 75000, 'quantity' => 15, 'received_at' => '2026-01-25'],
        //             ['unit_cost' => 78000, 'quantity' => 10, 'received_at' => '2026-04-15'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Seafood'),
        //         'unit_id'       => $unit('Kg'),
        //         'name'          => 'Cumi Beku 1 Kg',
        //         'sku'           => 'SFD-CMI-1KG',
        //         'description'   => 'Cumi-cumi segar beku, kemasan 1 Kg.',
        //         'selling_price' => 75000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 58000, 'quantity' => 15, 'received_at' => '2026-02-05'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Seafood'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Fillet Dori 500g',
        //         'sku'           => 'SFD-DRI-500',
        //         'description'   => 'Fillet ikan dori beku, kemasan 500 gram.',
        //         'selling_price' => 45000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 35000, 'quantity' => 20, 'received_at' => '2026-02-25'],
        //         ],
        //     ],

        //     // --- Daging & Unggas ---
        //     [
        //         'category_id'   => $cat('Daging & Unggas'),
        //         'unit_id'       => $unit('Kg'),
        //         'name'          => 'Daging Sapi Giling 500g',
        //         'sku'           => 'DAG-SAP-500',
        //         'description'   => 'Daging sapi giling beku, kemasan 500 gram.',
        //         'selling_price' => 65000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 50000, 'quantity' => 20, 'received_at' => '2026-01-30'],
        //             ['unit_cost' => 52000, 'quantity' => 10, 'received_at' => '2026-04-20'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Daging & Unggas'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Ayam Potong Beku 1 Kg',
        //         'sku'           => 'AYM-PTG-1KG',
        //         'description'   => 'Ayam potong beku, 1 ekor per pak.',
        //         'selling_price' => 38000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 29000, 'quantity' => 30, 'received_at' => '2026-02-08'],
        //         ],
        //     ],

        //     // --- Kentang & Sayuran Beku ---
        //     [
        //         'category_id'   => $cat('Kentang & Sayuran Beku'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'French Fries 1 Kg',
        //         'sku'           => 'KNT-FRF-1KG',
        //         'description'   => 'Kentang goreng potong panjang beku, 1 Kg.',
        //         'selling_price' => 28000,
        //         'stock_minimum' => 20,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 20000, 'quantity' => 50, 'received_at' => '2026-01-12'],
        //             ['unit_cost' => 21000, 'quantity' => 30, 'received_at' => '2026-03-20'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Kentang & Sayuran Beku'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Edamame Beku 500g',
        //         'sku'           => 'SYR-EDM-500',
        //         'description'   => 'Kedelai jepang rebus beku, kemasan 500 gram.',
        //         'selling_price' => 22000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 16000, 'quantity' => 25, 'received_at' => '2026-02-18'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Kentang & Sayuran Beku'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Mixed Vegetable Beku 500g',
        //         'sku'           => 'SYR-MIX-500',
        //         'description'   => 'Campuran wortel, buncis, jagung, dan kacang polong beku.',
        //         'selling_price' => 20000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 14000, 'quantity' => 30, 'received_at' => '2026-03-01'],
        //         ],
        //     ],

        //     // --- Kulit Lumpia & Pastry ---
        //     [
        //         'category_id'   => $cat('Kulit Lumpia & Pastry'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Kulit Lumpia 25x25 cm',
        //         'sku'           => 'KLT-LMP-25',
        //         'description'   => 'Kulit lumpia tipis ukuran 25x25 cm, isi 50 lembar.',
        //         'selling_price' => 18000,
        //         'stock_minimum' => 15,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 13000, 'quantity' => 40, 'received_at' => '2026-01-18'],
        //         ],
        //     ],
        //     [
        //         'category_id'   => $cat('Kulit Lumpia & Pastry'),
        //         'unit_id'       => $unit('Pak'),
        //         'name'          => 'Kulit Pastry 400g',
        //         'sku'           => 'KLT-PST-400',
        //         'description'   => 'Kulit pastry puff siap pakai, kemasan 400 gram.',
        //         'selling_price' => 32000,
        //         'stock_minimum' => 10,
        //         'is_active'     => true,
        //         'initial_stocks' => [
        //             ['unit_cost' => 24000, 'quantity' => 20, 'received_at' => '2026-02-28'],
        //         ],
        //     ],
        ];

        foreach ($products as $data) {
            $batches        = $data['initial_stocks'] ?? [];
            $productData    = collect($data)->except('initial_stocks')->toArray();

            DB::transaction(function () use ($productData, $batches) {
                $product = Product::firstOrCreate(['sku' => $productData['sku']], $productData);

                if ($product->wasRecentlyCreated && count($batches) > 0) {
                    $stockBatches = array_map(fn($b) => [
                        'product_id'  => $product->id,
                        'unit_cost'   => (int) $b['unit_cost'],
                        'quantity'    => (int) $b['quantity'],
                        'received_at' => $b['received_at'],
                    ], $batches);

                    InventoryService::addStockBulk(
                        $stockBatches,
                        source:      InventorySource::INITIATE,
                        referenceId: $product->id,
                    );
                }
            });
        }
    }
}
