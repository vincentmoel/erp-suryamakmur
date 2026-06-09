<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Http\Controllers\ReceiptController;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        Auth::loginUsingId($user->id);

        $invByDate = function (string $customerName, string $date): ?Invoice {
            $customer = Customer::where('name', $customerName)->first();
            if (! $customer) return null;

            return Invoice::where('customer_id', $customer->id)
                ->where('invoice_date', $date)
                ->first();
        };

        $receipts = [

            // ── RCP 1 ─ Budi Santoso ─ Bayar invoice Apr-01 lunas + Apr-15 sebagian
            [
                'customer_name'    => 'Budi Santoso',
                'receipt_date'     => '2026-05-01',
                'payment_method'   => PaymentMethod::BANK_TRANSFER->value,
                'reference_number' => 'TRF-20260501-001',
                'notes'            => 'Transfer BCA pembayaran April batch 1.',
                'allocations'      => [
                    ['date' => '2026-04-01', 'amount' => 500000],   // lunas
                    ['date' => '2026-04-15', 'amount' => 375000],   // 50%
                ],
            ],

            // ── RCP 2 ─ Budi Santoso ─ Lunasi Apr-15 + sebagian Mei
            [
                'customer_name'    => 'Budi Santoso',
                'receipt_date'     => '2026-05-20',
                'payment_method'   => PaymentMethod::CASH->value,
                'reference_number' => null,
                'notes'            => 'Pelunasan April + DP Mei.',
                'allocations'      => [
                    ['date' => '2026-04-15', 'amount' => 375000],   // sisa Apr-15
                    ['date' => '2026-05-01', 'amount' => 500000],   // 50% Mei
                ],
            ],

        ];

        foreach ($receipts as $data) {
            $customer = Customer::where('name', $data['customer_name'])->first();
            if (! $customer) {
                $this->command->warn("Customer [{$data['customer_name']}] tidak ditemukan, dilewati.");
                continue;
            }

            $allocations = [];
            $valid = true;
            foreach ($data['allocations'] as $alloc) {
                $invoice = $invByDate($data['customer_name'], $alloc['date']);
                if (! $invoice) {
                    $this->command->warn("Invoice {$data['customer_name']} tanggal {$alloc['date']} tidak ditemukan, dilewati.");
                    $valid = false;
                    break;
                }
                $allocations[$invoice->id] = [
                    'invoice_id' => $invoice->id,
                    'amount'     => $alloc['amount'],
                ];
            }

            if (! $valid) continue;

            $request = Request::create('/receipts', 'POST', [
                'customer_id'      => $customer->id,
                'receipt_date'     => $data['receipt_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'],
                'notes'            => $data['notes'],
                'allocations'      => $allocations,
            ]);

            app()->instance('request', $request);
            app()->call([new ReceiptController, 'store'], ['request' => $request]);
        }

        Auth::logout();
    }
}
