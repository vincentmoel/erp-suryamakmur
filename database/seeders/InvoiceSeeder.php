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
        // Seeder ini memanggil InvoiceController::store() secara langsung
        // agar logika FIFO inventory deduction, CodeGenerator, dan snapshot tetap konsisten.

        $salesperson = User::first();
        Auth::loginUsingId($salesperson->id);

        $customer = Customer::where('name', 'Budi Santoso')->firstOrFail();
        $product  = Product::where('sku', 'NGT-AYM-500')->firstOrFail();

        $qty        = 2;
        $unitPrice  = 35000;
        $amount     = $qty * $unitPrice;

        $request = Request::create('/invoices', 'POST', [
            'customer_id'    => $customer->id,
            'salesperson_id' => $salesperson->id,
            'invoice_date'   => '2026-06-09',
            'due_date'       => '2026-07-09',
            'status'         => InvoiceStatus::WAITING_FOR_PAYMENT->value,
            'notes'          => '',
            'details'        => [
                [
                    'product_id'      => $product->id,
                    'quantity'        => $qty,
                    'unit_price'      => $unitPrice,
                    'subtotal_amount' => $amount,
                    'amount'          => $amount,
                ],
            ],
        ]);

        // Bind request ke container agar InvoiceRequest bisa di-resolve dengan data di atas
        app()->instance('request', $request);

        app()->call([new InvoiceController, 'store'], ['request' => $request]);

        Auth::logout();
    }
}