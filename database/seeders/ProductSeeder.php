<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve category & unit IDs by name
        $cat  = fn(string $name) => Category::where('name', $name)->value('id');
        $unit = fn(string $abbr) => Unit::where('abbreviation', $abbr)->value('id');

        $products = [
            // --- Nugget & Sosis ---
            [
                'category_id'     => $cat('Nugget & Sosis'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Nugget Ayam 500g',
                'sku'             => 'NGT-AYM-500',
                'description'     => 'Nugget ayam crispy, kemasan 500 gram.',
                'stock_minimum'   => 20,
            ],
            [
                'category_id'     => $cat('Nugget & Sosis'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Sosis Sapi 375g',
                'sku'             => 'SOS-SAP-375',
                'description'     => 'Sosis sapi premium, kemasan 375 gram.',
                'stock_minimum'   => 20,
            ],
            [
                'category_id'     => $cat('Nugget & Sosis'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Nugget Ikan 400g',
                'sku'             => 'NGT-IKN-400',
                'description'     => 'Nugget ikan, kemasan 400 gram.',
                'stock_minimum'   => 15,
            ],
            [
                'category_id'     => $cat('Nugget & Sosis'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Chicken Strip 500g',
                'sku'             => 'CHK-STP-500',
                'description'     => 'Potongan ayam tepung crispy, kemasan 500 gram.',
                'stock_minimum'   => 15,
            ],

            // --- Dimsum & Siomay ---
            [
                'category_id'     => $cat('Dimsum & Siomay'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Siomay Udang 300g',
                'sku'             => 'SIM-UDG-300',
                'description'     => 'Siomay udang premium, isi 20 pcs per pak.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Dimsum & Siomay'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Hakau Udang 250g',
                'sku'             => 'HKU-UDG-250',
                'description'     => 'Hakau udang kulit tipis, isi 10 pcs per pak.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Dimsum & Siomay'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Dimsum Ayam Jamur 300g',
                'sku'             => 'DIM-AYM-300',
                'description'     => 'Dimsum ayam dengan jamur shiitake.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Dimsum & Siomay'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Ceker Dimsum 250g',
                'sku'             => 'DIM-CEK-250',
                'description'     => 'Ceker ayam kukus bumbu dimsum.',
                'stock_minimum'   => 10,
            ],

            // --- Bakso & Otak-otak ---
            [
                'category_id'     => $cat('Bakso & Otak-otak'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Bakso Sapi Jumbo 500g',
                'sku'             => 'BKS-SAP-500',
                'description'     => 'Bakso sapi ukuran jumbo, isi 10 biji per pak.',
                'stock_minimum'   => 20,
            ],
            [
                'category_id'     => $cat('Bakso & Otak-otak'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Bakso Ikan 400g',
                'sku'             => 'BKS-IKN-400',
                'description'     => 'Bakso ikan tenggiri, kemasan 400 gram.',
                'stock_minimum'   => 15,
            ],
            [
                'category_id'     => $cat('Bakso & Otak-otak'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Otak-otak Ikan 200g',
                'sku'             => 'OTK-IKN-200',
                'description'     => 'Otak-otak ikan tenggiri, isi 10 pcs per pak.',
                'stock_minimum'   => 15,
            ],

            // --- Seafood ---
            [
                'category_id'     => $cat('Seafood'),
                'unit_id'         => $unit('Kg'),
                'name'            => 'Udang Kupas Beku 1 Kg',
                'sku'             => 'SFD-UDG-1KG',
                'description'     => 'Udang kupas beku ukuran 51/60, kemasan 1 Kg.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Seafood'),
                'unit_id'         => $unit('Kg'),
                'name'            => 'Cumi Beku 1 Kg',
                'sku'             => 'SFD-CMI-1KG',
                'description'     => 'Cumi-cumi segar beku, kemasan 1 Kg.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Seafood'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Fillet Dori 500g',
                'sku'             => 'SFD-DRI-500',
                'description'     => 'Fillet ikan dori beku, kemasan 500 gram.',
                'stock_minimum'   => 10,
            ],

            // --- Daging & Unggas ---
            [
                'category_id'     => $cat('Daging & Unggas'),
                'unit_id'         => $unit('Kg'),
                'name'            => 'Daging Sapi Giling 500g',
                'sku'             => 'DAG-SAP-500',
                'description'     => 'Daging sapi giling beku, kemasan 500 gram.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Daging & Unggas'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Ayam Potong Beku 1 Kg',
                'sku'             => 'AYM-PTG-1KG',
                'description'     => 'Ayam potong beku, 1 ekor per pak.',
                'stock_minimum'   => 15,
            ],

            // --- Kentang & Sayuran Beku ---
            [
                'category_id'     => $cat('Kentang & Sayuran Beku'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'French Fries 1 Kg',
                'sku'             => 'KNT-FRF-1KG',
                'description'     => 'Kentang goreng potong panjang beku, 1 Kg.',
                'stock_minimum'   => 20,
            ],
            [
                'category_id'     => $cat('Kentang & Sayuran Beku'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Edamame Beku 500g',
                'sku'             => 'SYR-EDM-500',
                'description'     => 'Kedelai jepang rebus beku, kemasan 500 gram.',
                'stock_minimum'   => 10,
            ],
            [
                'category_id'     => $cat('Kentang & Sayuran Beku'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Mixed Vegetable Beku 500g',
                'sku'             => 'SYR-MIX-500',
                'description'     => 'Campuran wortel, buncis, jagung, dan kacang polong beku.',
                'stock_minimum'   => 10,
            ],

            // --- Kulit Lumpia & Pastry ---
            [
                'category_id'     => $cat('Kulit Lumpia & Pastry'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Kulit Lumpia 25x25 cm',
                'sku'             => 'KLT-LMP-25',
                'description'     => 'Kulit lumpia tipis ukuran 25x25 cm, isi 50 lembar.',
                'stock_minimum'   => 15,
            ],
            [
                'category_id'     => $cat('Kulit Lumpia & Pastry'),
                'unit_id'         => $unit('Pak'),
                'name'            => 'Kulit Pastry 400g',
                'sku'             => 'KLT-PST-400',
                'description'     => 'Kulit pastry puff siap pakai, kemasan 400 gram.',
                'stock_minimum'   => 10,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['sku' => $data['sku']],
                array_merge($data, [
                    'stock_available' => 0,
                    'stock_reserved'  => 0,
                ])
            );
        }
    }
}
