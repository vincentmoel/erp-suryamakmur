<?php

namespace App\Http\Controllers;

use App\DataTables\ReceiptDataTable;
use App\Enums\InvoiceStatus;
use App\Enums\Module;
use App\Enums\PaymentMethod;
use App\Helpers\Encryption;
use App\Helpers\FileManager;
use App\Http\Requests\ReceiptRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Services\InvoicePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Receipt::class,
            'receipts',
            'Receipt',
            'receipts',
            Module::Receipt->name,
            ReceiptRequest::class,
            ReceiptDataTable::class,
        );
    }

    private function customers()
    {
        return Customer::orderBy('name')->get(['id', 'name', 'company_name', 'type', 'tax_number', 'email', 'phone', 'mobile', 'notes']);
    }

    public function create()
    {
        return view('receipts.create', [
            'title'          => $this->title,
            'route'          => $this->route,
            'customers'      => $this->customers(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(ReceiptRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        $allocations = array_values(array_filter(
            $data['allocations'],
            fn($a) => !empty($a['amount']) && (int) $a['amount'] > 0
        ));

        if (empty($allocations)) {
            return redirect()->back()->withErrors(['allocations' => __('general.allocations_required')])->withInput();
        }

        $error = $this->validateAllocations($allocations);
        if ($error) {
            return redirect()->back()->withErrors(['allocations' => $error])->withInput();
        }

        DB::transaction(function () use ($allocations, $data, $request) {
            $imagePath = $request->hasFile('image')
                ? FileManager::store($request->file('image'), 'receipts')
                : null;

            $receipt = Receipt::create([
                'customer_id'      => $data['customer_id'],
                'receipt_date'     => $data['receipt_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'image'            => $imagePath,
            ]);

            foreach ($allocations as $allocation) {
                $receipt->details()->create([
                    'invoice_id' => $allocation['invoice_id'],
                    'amount'     => $allocation['amount'],
                ]);
            }

            InvoicePaymentService::recalculateMany(
                array_column($allocations, 'invoice_id')
            );
        });

        return redirect()->route('receipts.index')->with([
            'success' => ['title' => 'Receipt Created', 'message' => 'Payment receipt has been saved.'],
        ]);
    }

    public function show($encryptedId)
    {
        $receipt = Receipt::with('customer', 'details.invoice', 'user_created_by', 'user_updated_by')
            ->findOrFail(Encryption::decrypt($encryptedId));

        return view('receipts.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'data'        => $receipt,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $receipt = Receipt::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        return view('receipts.edit', [
            'title'          => $this->title,
            'route'          => $this->route,
            'data'           => $receipt,
            'encryptedId'    => $encryptedId,
            'customers'      => $this->customers(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $receipt = Receipt::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        $formRequest = app()->make(ReceiptRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        $allocations = array_values(array_filter(
            $data['allocations'],
            fn($a) => !empty($a['amount']) && (int) $a['amount'] > 0
        ));

        if (empty($allocations)) {
            return redirect()->back()->withErrors(['allocations' => __('general.allocations_required')])->withInput();
        }

        $error = $this->validateAllocations($allocations, $receipt->id);
        if ($error) {
            return redirect()->back()->withErrors(['allocations' => $error])->withInput();
        }

        DB::transaction(function () use ($receipt, $allocations, $data, $request) {
            $oldInvoiceIds = $receipt->details->pluck('invoice_id')->all();

            $imagePath = $receipt->image;
            if ($request->hasFile('image')) {
                $imagePath = FileManager::store($request->file('image'), 'receipts');
            }

            $receipt->update([
                'customer_id'      => $data['customer_id'],
                'receipt_date'     => $data['receipt_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'image'            => $imagePath,
            ]);

            $receipt->details()->delete();

            foreach ($allocations as $allocation) {
                $receipt->details()->create([
                    'invoice_id' => $allocation['invoice_id'],
                    'amount'     => $allocation['amount'],
                ]);
            }

            $newInvoiceIds  = array_column($data['allocations'], 'invoice_id');
            $allInvoiceIds  = array_merge($oldInvoiceIds, $newInvoiceIds);
            InvoicePaymentService::recalculateMany($allInvoiceIds);
        });

        return redirect()->route('receipts.show', $encryptedId)->with([
            'success' => ['title' => 'Receipt Updated', 'message' => 'Payment receipt has been updated.'],
        ]);
    }

    public function destroy($encryptedId)
    {
        $receipt = Receipt::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        DB::transaction(function () use ($receipt) {
            $invoiceIds = $receipt->details->pluck('invoice_id')->all();

            $receipt->delete();

            InvoicePaymentService::recalculateMany($invoiceIds);
        });

        return \App\Helpers\Response::build(200, 'Success', [
            'title'   => __('general.success_delete'),
            'message' => __('general.success_delete_message'),
        ]);
    }

    /**
     * AJAX: open invoices for a customer, used by the receipt create/edit form.
     * Pass ?receipt_id=X to also include invoices already allocated in that receipt.
     */
    public function ajaxCustomerInvoices(Request $request, int $id)
    {
        $payableStatuses = [
            InvoiceStatus::WAITING_FOR_PAYMENT->value,
            InvoiceStatus::PARTIALLY_PAID->value,
        ];

        $query = Invoice::where('customer_id', $id)
            ->whereIn('status', $payableStatuses)
            ->orderBy('invoice_date');

        // On edit, also include invoices already allocated in this receipt
        if ($receiptId = $request->integer('receipt_id')) {
            $alreadyIds = \App\Models\ReceiptDetail::whereHas(
                'receipt',
                fn($q) => $q->where('id', $receiptId)->whereNull('deleted_at')
            )->pluck('invoice_id')->all();

            if ($alreadyIds) {
                $query = Invoice::where('customer_id', $id)
                    ->where(fn($q) => $q
                        ->whereIn('status', $payableStatuses)
                        ->orWhereIn('id', $alreadyIds)
                    )
                    ->orderBy('invoice_date');
            }
        }

        $invoices = $query->get()->map(function (Invoice $inv) use ($request, $receiptId) {
            $alreadyPaid = 0;
            if ($receiptId) {
                $alreadyPaid = (int) $inv->receiptDetails()
                    ->whereHas('receipt', fn($q) => $q->where('id', $receiptId))
                    ->sum('amount');
            }

            return [
                'id'               => $inv->id,
                'code'             => $inv->code,
                'invoice_date'     => $inv->invoice_date->translatedFormat('d F Y'),
                'due_date'         => $inv->due_date?->translatedFormat('d F Y'),
                'due_date_ts'      => $inv->due_date?->timestamp,
                'grand_total'      => $inv->amount,
                'paid_amount'      => $inv->paid_amount,
                'remaining_amount' => $inv->amount - $inv->paid_amount + $alreadyPaid,
                'allocated_amount' => $alreadyPaid,
            ];
        });

        return response()->json($invoices);
    }

    /**
     * Validate that each allocation amount does not exceed the invoice's remaining balance.
     * Pass $excludeReceiptId when editing so the current receipt's own amounts are excluded.
     */
    private function validateAllocations(array $allocations, ?int $excludeReceiptId = null): ?string
    {
        $payableStatuses = [InvoiceStatus::WAITING_FOR_PAYMENT, InvoiceStatus::PARTIALLY_PAID];

        // Group allocations by invoice to handle duplicate entries
        $grouped = [];
        foreach ($allocations as $alloc) {
            $id = (int) $alloc['invoice_id'];
            $grouped[$id] = ($grouped[$id] ?? 0) + (int) $alloc['amount'];
        }

        foreach ($grouped as $invoiceId => $totalAllocated) {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice || !in_array($invoice->status, $payableStatuses)) {
                return "Invoice #{$invoice?->code} is not in a payable status.";
            }

            // Remaining = grand_total - paid_amount + what this receipt already paid (on edit)
            $alreadyPaid = 0;
            if ($excludeReceiptId) {
                $alreadyPaid = (int) $invoice->receiptDetails()
                    ->whereHas('receipt', fn($q) => $q->where('id', $excludeReceiptId))
                    ->sum('amount');
            }

            $remaining = $invoice->amount - $invoice->paid_amount + $alreadyPaid;

            if ($totalAllocated > $remaining) {
                return "Allocation for Invoice #{$invoice->code} (Rp " .
                    number_format($totalAllocated, 0, ',', '.') .
                    ") exceeds the remaining balance (Rp " .
                    number_format($remaining, 0, ',', '.') . ").";
            }
        }

        return null;
    }
}
