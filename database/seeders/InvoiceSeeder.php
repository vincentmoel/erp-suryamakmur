<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\InvoiceController;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $salesperson = User::first();
        Auth::loginUsingId($salesperson->id);

        $customer = Customer::where('name', 'Budi Santoso')->firstOrFail();
        $product  = Product::where('sku', 'NGT-AYM-500')->firstOrFail();

        $invoices = [
            ['date' => '2026-04-01', 'qty' => 10, 'unit_price' => 50000],  // Rp 500.000
            ['date' => '2026-04-15', 'qty' => 15, 'unit_price' => 50000],  // Rp 750.000
            ['date' => '2026-05-01', 'qty' => 20, 'unit_price' => 50000],  // Rp 1.000.000
        ];

        foreach ($invoices as $inv) {
            $amount  = $inv['qty'] * $inv['unit_price'];
            $request = Request::create('/invoices', 'POST', [
                'customer_id'    => $customer->id,
                'salesperson_id' => $salesperson->id,
                'invoice_date'   => $inv['date'],
                'due_date'       => null,
                'status'         => InvoiceStatus::WAITING_FOR_PAYMENT->value,
                'notes'          => '',
                'details'        => [
                    [
                        'product_id'      => $product->id,
                        'quantity'        => $inv['qty'],
                        'unit_price'      => $inv['unit_price'],
                        'subtotal_amount' => $amount,
                        'amount'          => $amount,
                    ],
                ],
            ]);

            app()->instance('request', $request);
            app()->call([new InvoiceController, 'store'], ['request' => $request]);
        }

        Auth::logout();
    }
}