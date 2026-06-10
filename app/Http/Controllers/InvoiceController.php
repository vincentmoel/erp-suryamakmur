<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceDataTable;
use App\Enums\InventorySource;
use App\Enums\InvoiceStatus;
use App\Enums\Module;
use App\Helpers\CodeGenerator;
use App\Helpers\Encryption;
use App\Helpers\Response;
use App\Http\Requests\InvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Invoice::class,
            'invoices',
            'Invoice',
            'invoices',
            Module::Invoice->name,
            InvoiceRequest::class,
            InvoiceDataTable::class,
        );
    }

    public function index()
    {
        $dataTable = new InvoiceDataTable(false);

        return $dataTable->render('invoices.index', [
            'title'        => $this->title,
            'route'        => $this->route,
            'module'       => $this->module,
            'customers'    => Customer::orderBy('name')->get(['id', 'name']),
            'salespersons' => User::orderBy('name')->get(['id', 'name']),
            'statuses'     => InvoiceStatus::cases(),
        ]);
    }

    private function productOptions(): array
    {
        return Product::orderBy('name')->get(['id', 'name', 'sku'])
            ->map(fn($p) => [
                'value' => $p->id,
                'label' => $p->name . ($p->sku ? ' (' . $p->sku . ')' : ''),
            ])->toArray();
    }

    private function customers()
    {
        return Customer::orderBy('name')->get(['id', 'name', 'type', 'company_name', 'tax_number', 'email', 'phone', 'mobile', 'notes']);
    }

    public function create()
    {
        return view('invoices.create', [
            'title'          => $this->title,
            'route'          => $this->route,
            'customers'      => $this->customers(),
            'salespersons'   => User::orderBy('name')->get(['id', 'name']),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(InvoiceRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($data) {
            $customer      = Customer::find($data['customer_id']);
            $subtotal      = collect($data['details'])->sum('amount');
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxAmount      = (int) ($data['tax_amount'] ?? 0);
            $grandTotal    = $subtotal - $discountAmount + $taxAmount;

            $invoice = Invoice::create([
                'code'             => CodeGenerator::invoice(),
                'customer_id'      => $data['customer_id'],
                'customer_snapshot' => [
                    'id'           => $customer->id,
                    'name'         => $customer->name,
                    'type'         => $customer->type->value,
                    'company_name' => $customer->company_name,
                    'tax_number'   => $customer->tax_number,
                    'email'        => $customer->email,
                    'phone'        => $customer->phone,
                    'mobile'       => $customer->mobile,
                    'address'      => $customer->address ?? null,
                ],
                'salesperson_id'   => $data['salesperson_id'],
                'invoice_date'     => $data['invoice_date'],
                'due_date'         => $data['due_date'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? null,
                'discount_amount'  => $discountAmount ?: null,
                'tax_percent'      => $data['tax_percent'] ?? null,
                'tax_amount'       => $taxAmount ?: null,
                'subtotal'         => $subtotal,
                'grand_total'      => $grandTotal,
                'paid_amount'      => 0,
                'notes'            => $data['notes'] ?? null,
                'status'           => $data['status'],
            ]);

            foreach ($data['details'] as $detail) {
                $product = Product::with('category', 'unit')->find($detail['product_id']);

                $invoiceDetail = $invoice->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'discount_percent' => $detail['discount_percent'] ?? null,
                    'discount_amount'  => $detail['discount_amount'] ?? null,
                    'tax_percent'      => $detail['tax_percent'] ?? null,
                    'tax_amount'       => $detail['tax_amount'] ?? null,
                    'subtotal'         => $detail['subtotal_amount'] ?? ($detail['quantity'] * $detail['unit_price']),
                    'amount'           => $detail['amount'],
                    'product_snapshot' => [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'unit'     => $product->unit?->name,
                        'category' => $product->category?->name,
                    ],
                ]);

                $allocations = InventoryService::deductStock(
                    productId:   $detail['product_id'],
                    quantity:    $detail['quantity'],
                    source:      InventorySource::SALE,
                    referenceId: $invoice->id,
                    notes:       'Invoice #' . $invoice->code,
                );

                foreach ($allocations as $allocation) {
                    $invoiceDetail->batches()->create($allocation);
                }
            }
        });

        return redirect()->route('invoices.index')->with([
            'success' => ['title' => 'Invoice Created', 'message' => 'Invoice has been saved.'],
        ]);
    }

    public function show($encryptedId)
    {
        $invoice = Invoice::with('customer', 'salesperson', 'details.product', 'user_created_by', 'user_updated_by')
            ->findOrFail(Encryption::decrypt($encryptedId));

        return view('invoices.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'data'        => $invoice,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $invoice = Invoice::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $invoice->status->canEdit()) {
            return redirect()->route('invoices.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Invoice with status "' . $invoice->status->label() . '" cannot be edited.'],
            ]);
        }

        return view('invoices.edit', [
            'title'          => $this->title,
            'route'          => $this->route,
            'data'           => $invoice,
            'encryptedId'    => $encryptedId,
            'customers'      => $this->customers(),
            'salespersons'   => User::orderBy('name')->get(['id', 'name']),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $invoice = Invoice::with('invoiceDetailBatches')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $invoice->status->canEdit()) {
            return redirect()->route('invoices.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Invoice with status "' . $invoice->status->label() . '" cannot be edited.'],
            ]);
        }

        $formRequest = app()->make(InvoiceRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($invoice, $data) {
            // Reverse any prior FIFO deductions before rebuilding details
            foreach ($invoice->invoiceDetailBatches as $batch) {
                InventoryService::restoreStockBatch(
                    inventoryDetailId: $batch->inventory_detail_id,
                    quantity:          $batch->quantity,
                    source:            InventorySource::SALE,
                    referenceId:       $invoice->id,
                    notes:             'Edit reversal Invoice #' . $invoice->code,
                );
            }

            $customer       = Customer::find($data['customer_id']);
            $subtotal       = collect($data['details'])->sum('amount');
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxAmount      = (int) ($data['tax_amount'] ?? 0);
            $grandTotal     = $subtotal - $discountAmount + $taxAmount;

            $invoice->update([
                'customer_id'      => $data['customer_id'],
                'customer_snapshot' => [
                    'id'           => $customer->id,
                    'name'         => $customer->name,
                    'type'         => $customer->type->value,
                    'company_name' => $customer->company_name,
                    'tax_number'   => $customer->tax_number,
                    'email'        => $customer->email,
                    'phone'        => $customer->phone,
                    'mobile'       => $customer->mobile,
                    'address'      => $customer->address ?? null,
                ],
                'salesperson_id'   => $data['salesperson_id'],
                'invoice_date'     => $data['invoice_date'],
                'due_date'         => $data['due_date'] ?? null,
                'discount_percent' => $data['discount_percent'] ?? null,
                'discount_amount'  => $discountAmount ?: null,
                'tax_percent'      => $data['tax_percent'] ?? null,
                'tax_amount'       => $taxAmount ?: null,
                'subtotal'         => $subtotal,
                'grand_total'      => $grandTotal,
                'notes'            => $data['notes'] ?? null,
                'status'           => $data['status'],
            ]);

            // Cascades to invoice_detail_batches via DB constraint
            $invoice->details()->delete();

            foreach ($data['details'] as $detail) {
                $product = Product::with('category', 'unit')->find($detail['product_id']);

                $invoiceDetail = $invoice->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'discount_percent' => $detail['discount_percent'] ?? null,
                    'discount_amount'  => $detail['discount_amount'] ?? null,
                    'tax_percent'      => $detail['tax_percent'] ?? null,
                    'tax_amount'       => $detail['tax_amount'] ?? null,
                    'subtotal'         => $detail['subtotal_amount'] ?? ($detail['quantity'] * $detail['unit_price']),
                    'amount'           => $detail['amount'],
                    'product_snapshot' => [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'unit'     => $product->unit?->name,
                        'category' => $product->category?->name,
                    ],
                ]);

                $allocations = InventoryService::deductStock(
                    productId:   $detail['product_id'],
                    quantity:    $detail['quantity'],
                    source:      InventorySource::SALE,
                    referenceId: $invoice->id,
                    notes:       'Invoice #' . $invoice->code,
                );

                foreach ($allocations as $allocation) {
                    $invoiceDetail->batches()->create($allocation);
                }
            }
        });

        return redirect()->route('invoices.show', $encryptedId)->with([
            'success' => ['title' => 'Invoice Updated', 'message' => 'Invoice has been updated.'],
        ]);
    }

    public function destroy($encryptedId)
    {
        $invoice = Invoice::with('invoiceDetailBatches')->findOrFail(Encryption::decrypt($encryptedId));

        $hasActiveReceipt = $invoice->receiptDetails()->whereHas('receipt')->exists();

        if ($hasActiveReceipt) {
            return response()->json([
                'code'    => 422,
                'message' => 'Error',
                'error'   => [
                    'title'   => 'Cannot Delete',
                    'message' => 'Invoice cannot be deleted because it has associated receipts.',
                ],
            ], 422);
        }

        if (! $invoice->status->canDelete()) {
            return response()->json([
                'code'    => 422,
                'message' => 'Error',
                'error'   => [
                    'title'   => 'Cannot Delete',
                    'message' => 'Invoice with status "' . $invoice->status->label() . '" cannot be deleted.',
                ],
            ], 422);
        }

        DB::transaction(function () use ($invoice) {
            foreach ($invoice->invoiceDetailBatches as $batch) {
                InventoryService::restoreStockBatch(
                    inventoryDetailId: $batch->inventory_detail_id,
                    quantity:          $batch->quantity,
                    source:            InventorySource::SALE,
                    referenceId:       $invoice->id,
                    notes:             'Delete Invoice #' . $invoice->code,
                );
            }

            $invoice->delete();
        });

        return Response::build(200, 'Success', [
            'title'   => 'Invoice Deleted',
            'message' => 'Invoice has been deleted.',
        ]);
    }

    public function cancel($encryptedId)
    {
        $invoice = Invoice::with('invoiceDetailBatches')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $invoice->status->canCancel()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Cancel', 'message' => 'Invoice with status "' . $invoice->status->label() . '" cannot be cancelled.'],
            ]);
        }

        DB::transaction(function () use ($invoice) {
            foreach ($invoice->invoiceDetailBatches as $batch) {
                InventoryService::restoreStockBatch(
                    inventoryDetailId: $batch->inventory_detail_id,
                    quantity:          $batch->quantity,
                    source:            InventorySource::SALE,
                    referenceId:       $invoice->id,
                    notes:             'Cancellation Invoice #' . $invoice->code,
                );
            }

            $invoice->update(['status' => InvoiceStatus::CANCELLED]);
        });

        return redirect()->back()->with([
            'success' => ['title' => 'Invoice Cancelled', 'message' => 'Invoice has been cancelled.'],
        ]);
    }
}
