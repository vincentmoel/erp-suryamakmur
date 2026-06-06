<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Services\InvoicePaymentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptSeeder extends Seeder
{
    public function run(): void
    {
        $cust = fn(string $name) => Customer::where('name', $name)->first();

        /**
         * Find an invoice by customer + date.  Codes are generated at seed-time
         * (depends on which month migrate:fresh runs), so we never hardcode them.
         */
        $invByDate = function (string $customerName, string $date) use ($cust) {
            $customer = $cust($customerName);
            if (! $customer) {
                return null;
            }

            return Invoice::where('customer_id', $customer->id)
                ->where('invoice_date', $date)
                ->first();
        };

        /**
         * Situasi setelah InvoiceSeeder (stok awal 2026-05-10 & 2026-05-15):
         *   Budi Santoso  2026-05-20  Rp  271.000  WAITING_FOR_PAYMENT
         *   Ibu Dewi      2026-05-25  Rp  815.000  WAITING_FOR_PAYMENT
         *   Andi Wijaya   2026-06-01  Rp 1.020.000 WAITING_FOR_PAYMENT
         *   Rina Kusuma   2026-06-04  Rp  565.000  DRAFT
         *   Doni Prasetyo 2026-06-05  Rp 1.005.000 WAITING_FOR_PAYMENT
         *
         * Setelah seeder ini:
         *   Budi Santoso  → PAID           (Rp 271.000 lunas)
         *   Ibu Dewi      → PARTIALLY_PAID (Rp 400.000 dari Rp 815.000)
         *   Andi Wijaya   → PARTIALLY_PAID (Rp 510.000 dari Rp 1.020.000)
         */
        $receipts = [

            // ── RCP 1 ─ Budi Santoso ─ Lunas ─────────────────────────────
            [
                'customer'    => $cust('Budi Santoso'),
                'date'        => '2026-06-02',
                'method'      => PaymentMethod::CASH,
                'reference'   => null,
                'notes'       => 'Pembayaran tunai di tempat.',
                'allocations' => [
                    ['invoice' => $invByDate('Budi Santoso', '2026-05-20'), 'amount' => 271000],
                ],
            ],

            // ── RCP 2 ─ Ibu Dewi ─ Bayar sebagian ────────────────────────
            [
                'customer'    => $cust('Ibu Dewi'),
                'date'        => '2026-06-04',
                'method'      => PaymentMethod::BANK_TRANSFER,
                'reference'   => 'TRF-20260604-001',
                'notes'       => 'Transfer BCA, bukti terlampir.',
                'allocations' => [
                    ['invoice' => $invByDate('Ibu Dewi', '2026-05-25'), 'amount' => 400000],
                ],
            ],

            // ── RCP 3 ─ Andi Wijaya ─ DP 50% invoice distributor ─────────
            [
                'customer'    => $cust('Andi Wijaya'),
                'date'        => '2026-06-06',
                'method'      => PaymentMethod::BANK_TRANSFER,
                'reference'   => 'TRF-20260606-MBS',
                'notes'       => 'Down payment 50% distributor Jakarta.',
                'allocations' => [
                    ['invoice' => $invByDate('Andi Wijaya', '2026-06-01'), 'amount' => 510000],
                ],
            ],
        ];

        foreach ($receipts as $data) {
            if (! $data['customer']) {
                continue;
            }

            // Skip if any invoice in this receipt couldn't be resolved
            $valid = true;
            foreach ($data['allocations'] as $alloc) {
                if (! $alloc['invoice']) {
                    $valid = false;
                    break;
                }
            }
            if (! $valid) {
                continue;
            }

            DB::transaction(function () use ($data) {
                $receipt = Receipt::firstOrCreate(
                    [
                        'customer_id'    => $data['customer']->id,
                        'receipt_date'   => $data['date'],
                        'payment_method' => $data['method']->value,
                    ],
                    [
                        'reference_number' => $data['reference'],
                        'notes'            => $data['notes'],
                    ]
                );

                if (! $receipt->wasRecentlyCreated) {
                    return;
                }

                $invoiceIds = [];

                foreach ($data['allocations'] as $alloc) {
                    $receipt->details()->create([
                        'invoice_id' => $alloc['invoice']->id,
                        'amount'     => $alloc['amount'],
                    ]);
                    $invoiceIds[] = $alloc['invoice']->id;
                }

                InvoicePaymentService::recalculateMany($invoiceIds);
            });
        }
    }
}
