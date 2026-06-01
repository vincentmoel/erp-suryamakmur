<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::create([
            'name'          => 'Member Discount',
            'key'           => 'member_discount',
            'value'         => 50,
            'created_by'    => 1,
            'updated_by'    => 1,
        ]);

        Config::create([
            'name'          => 'Stock Alert',
            'key'           => 'stock_alert',
            'value'         => 10,
            'created_by'    => 1,
            'updated_by'    => 1,

        ]);
    }
}
