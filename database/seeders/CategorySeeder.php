<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Daging & Unggas',
            'Seafood',
            'Nugget & Sosis',
            'Dimsum & Siomay',
            'Bakso & Otak-otak',
            'Kentang & Sayuran Beku',
            'Kulit Lumpia & Pastry',
            'Bumbu & Marinasi',
            'Kemasan & Packaging',
            'Lain-lain',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
