<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        $user = User::first();
        Auth::setUser($user);
        
        $this->call([
            RoleSeeder::class,
            RoleUserSeeder::class,
            StationCategorySeeder::class,
            DurationSeeder::class,
            IpAddressSeeder::class,
            RentalStationSeeder::class,
            ItemCategorySeeder::class,
            ItemSeeder::class,
            CustomerSeeder::class,
            ConfigSeeder::class,
        ]);
    }
}
