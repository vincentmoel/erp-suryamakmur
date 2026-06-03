<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Berat
            ['name' => 'Kilogram',   'abbreviation' => 'Kg'],
            ['name' => 'Gram',       'abbreviation' => 'g'],
            ['name' => 'Ton',        'abbreviation' => 'Ton'],
            // Satuan jual
            ['name' => 'Piece',      'abbreviation' => 'Pcs'],
            ['name' => 'Pack',       'abbreviation' => 'Pak'],
            ['name' => 'Box',        'abbreviation' => 'Box'],
            ['name' => 'Carton',     'abbreviation' => 'Ctn'],
            ['name' => 'Sachet',     'abbreviation' => 'Sct'],
            ['name' => 'Porsi',      'abbreviation' => 'Prs'],
            // Kemasan khusus frozen
            ['name' => 'Tray',       'abbreviation' => 'Tray'],
            ['name' => 'Pouch',      'abbreviation' => 'Pouch'],
            ['name' => 'Plastik',    'abbreviation' => 'Plas'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['name' => $unit['name']],
                ['abbreviation' => $unit['abbreviation']]
            );
        }
    }
}
