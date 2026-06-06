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
            ConfigSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            VendorSeeder::class,
            // PurchaseSeeder::class,   // adds inventory for RECEIVED purchases
            // InvoiceSeeder::class,    // creates invoices + FIFO deductions
            // ReceiptSeeder::class,    // creates receipts + recalculates invoice status
        ]);
    }
}
