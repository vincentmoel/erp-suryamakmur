<?php

namespace Database\Seeders;

use App\Enums\InventorySource;
use App\Enums\InvoiceStatus;
use App\Helpers\CodeGenerator;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $salesperson = User::first();

        $cust  = fn(string $name) => Customer::where('name', $name)->first();
        $prod  = fn(string $sku)  => Product::with('unit', 'category')->where('sku', $sku)->first();

        /**
         * Inventory tersedia setelah PurchaseSeeder:
         *   NGT-AYM-500 : 50 pcs @ Rp 28.000
         *   SOS-SAP-375 : 40 pcs @ Rp 25.000
         *   NGT-IKN-400 : 30 pcs @ Rp 22.000
         *   SFD-UDG-1KG : 20 kg  @ Rp 78.000
         *   SFD-CMI-1KG : 15 kg  @ Rp 60.000
         *   SFD-DRI-500 : 25 pcs @ Rp 35.000
         */
        $invoices = [

            // ── INV 1 ─ Budi Santoso (Retail) ─ WAITING_FOR_PAYMENT ─────
            // → akan menjadi PAID setelah ReceiptSeeder
            [
                'customer'     => $cust('Budi Santoso'),
                'date'         => '2026-05-20',
                'due_date'     => '2026-06-20',
                'status'       => InvoiceStatus::WAITING_FOR_PAYMENT,
                'notes'        => null,
                'details'      => [
                    ['sku' => 'NGT-AYM-500', 'qty' => 5,  'price' => 35000],
                    ['sku' => 'SOS-SAP-375', 'qty' => 3,  'price' => 32000],
                ],
            ],

            // ── INV 2 ─ Ibu Dewi (Warung) ─ WAITING_FOR_PAYMENT ─────────
            // → akan menjadi PARTIALLY_PAID setelah ReceiptSeeder
            [
                'customer'     => $cust('Ibu Dewi'),
                'date'         => '2026-05-25',
                'due_date'     => '2026-06-25',
                'status'       => InvoiceStatus::WAITING_FOR_PAYMENT,
                'notes'        => 'Order mingguan warung Bu Dewi.',
                'details'      => [
                    ['sku' => 'NGT-AYM-500', 'qty' => 10, 'price' => 35000],
                    ['sku' => 'NGT-IKN-400', 'qty' => 8,  'price' => 30000],
                    ['sku' => 'SFD-DRI-500', 'qty' => 5,  'price' => 45000],
                ],
            ],

            // ── INV 3 ─ PT Maju Bersama ─ WAITING_FOR_PAYMENT ───────────
            [
                'customer'     => $cust('Andi Wijaya'),
                'date'         => '2026-06-01',
                'due_date'     => '2026-06-30',
                'status'       => InvoiceStatus::WAITING_FOR_PAYMENT,
                'notes'        => 'Bulk order distributor Jakarta.',
                'details'      => [
                    ['sku' => 'SFD-UDG-1KG', 'qty' => 5,  'price' => 95000],
                    ['sku' => 'SFD-CMI-1KG', 'qty' => 3,  'price' => 75000],
                    ['sku' => 'SOS-SAP-375', 'qty' => 10, 'price' => 32000],
                ],
            ],

            // ── INV 4 ─ CV Sumber Rezeki ─ DRAFT (stok TIDAK dikurangi) ─
            [
                'customer'     => $cust('Rina Kusuma'),
                'date'         => '2026-06-04',
                'due_date'     => null,
                'status'       => InvoiceStatus::DRAFT,
                'notes'        => 'Draft — menunggu konfirmasi harga.',
                'details'      => [
                    ['sku' => 'SFD-UDG-1KG', 'qty' => 3,  'price' => 95000],
                    ['sku' => 'NGT-AYM-500', 'qty' => 8,  'price' => 35000],
                ],
            ],

            // ── INV 5 ─ PT Dingin Segar ─ WAITING_FOR_PAYMENT ───────────
            [
                'customer'     => $cust('Doni Prasetyo'),
                'date'         => '2026-06-05',
                'due_date'     => '2026-07-05',
                'status'       => InvoiceStatus::WAITING_FOR_PAYMENT,
                'notes'        => 'Order cold-chain Surabaya.',
                'details'      => [
                    ['sku' => 'SFD-CMI-1KG', 'qty' => 5,  'price' => 75000],
                    ['sku' => 'SFD-DRI-500', 'qty' => 10, 'price' => 45000],
                    ['sku' => 'NGT-IKN-400', 'qty' => 6,  'price' => 30000],
                ],
            ],
        ];

        foreach ($invoices as $data) {
            if (! $data['customer']) {
                continue;
            }

            // Build detail rows + calculate totals
            $detailRows = [];
            $subtotal   = 0;

            foreach ($data['details'] as $line) {
                $product = $prod($line['sku']);
                if (! $product) {
                    continue;
                }

                $lineAmount = $line['qty'] * $line['price'];
                $subtotal  += $lineAmount;

                $detailRows[] = [
                    'product'  => $product,
                    'qty'      => $line['qty'],
                    'price'    => $line['price'],
                    'amount'   => $lineAmount,
                ];
            }

            $grandTotal = $subtotal;

            DB::transaction(function () use ($data, $detailRows, $subtotal, $grandTotal, $salesperson) {
                $invoice = Invoice::firstOrCreate(
                    [
                        'customer_id'  => $data['customer']->id,
                        'invoice_date' => $data['date'],
                        'grand_total'  => $grandTotal,
                    ],
                    [
                        'code'            => CodeGenerator::invoice(),
                        'salesperson_id'  => $salesperson->id,
                        'due_date'        => $data['due_date'],
                        'subtotal'        => $subtotal,
                        'discount_amount' => null,
                        'tax_percent'     => 0,
                        'tax_amount'      => null,
                        'grand_total'     => $grandTotal,
                        'paid_amount'     => 0,
                        'status'          => $data['status']->value,
                        'notes'           => $data['notes'],
                    ]
                );

                if (! $invoice->wasRecentlyCreated) {
                    return;
                }

                $deductStock = $data['status'] !== InvoiceStatus::DRAFT;

                foreach ($detailRows as $row) {
                    $invoiceDetail = $invoice->details()->create([
                        'product_id'      => $row['product']->id,
                        'quantity'        => $row['qty'],
                        'unit_price'      => $row['price'],
                        'discount_amount' => null,
                        'tax_percent'     => 0,
                        'tax_amount'      => null,
                        'subtotal'        => $row['qty'] * $row['price'],
                        'amount'          => $row['amount'],
                        'product_snapshot' => [
                            'id'       => $row['product']->id,
                            'name'     => $row['product']->name,
                            'sku'      => $row['product']->sku,
                            'unit'     => $row['product']->unit?->name,
                            'category' => $row['product']->category?->name,
                        ],
                    ]);

                    if ($deductStock) {
                        $allocations = InventoryService::deductStock(
                            productId:   $row['product']->id,
                            quantity:    $row['qty'],
                            source:      InventorySource::SALE,
                            referenceId: $invoice->id,
                            notes:       'Invoice #' . $invoice->code,
                        );

                        foreach ($allocations as $allocation) {
                            $invoiceDetail->batches()->create($allocation);
                        }
                    }
                }
            });
        }
    }
}
