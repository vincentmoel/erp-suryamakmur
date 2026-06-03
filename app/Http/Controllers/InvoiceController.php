<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceDataTable;
use App\Enums\InvoiceStatus;
use App\Enums\Module;
use App\Helpers\CodeGenerator;
use App\Helpers\Encryption;
use App\Http\Requests\InvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\User;
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
            $subtotalAmount = collect($data['details'])->sum('amount');
            $discountAmount = $data['discount_amount'] ?? null;
            $taxAmount      = $data['tax_amount'] ?? null;

            $amount = $subtotalAmount
                - ($discountAmount ?? 0)
                + ($taxAmount ?? 0);

            $invoice = Invoice::create([
                'code'             => CodeGenerator::invoice(),
                'customer_id'      => $data['customer_id'],
                'salesperson_id'   => $data['salesperson_id'],
                'invoice_date'     => $data['invoice_date'],
                'due_date'         => $data['due_date'] ?? null,
                'subtotal_amount'  => $subtotalAmount,
                'discount_amount'  => $discountAmount,
                'tax_amount'       => $taxAmount,
                'amount'           => $amount,
                'paid_amount'      => 0,
                'status'           => $data['status'],
            ]);

            foreach ($data['details'] as $detail) {
                $product = Product::with('category', 'unit')->find($detail['product_id']);

                $invoice->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'subtotal_amount' => $detail['subtotal_amount'],
                    'discount_amount' => $detail['discount_amount'] ?? null,
                    'tax_amount'      => $detail['tax_amount'] ?? null,
                    'amount'          => $detail['amount'],
                    'product_snapshot' => [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'unit'     => $product->unit?->name,
                        'category' => $product->category?->name,
                    ],
                ]);
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
        $invoice = Invoice::with('details')->findOrFail(Encryption::decrypt($encryptedId));

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
            $subtotalAmount = collect($data['details'])->sum('amount');
            $discountAmount = $data['discount_amount'] ?? null;
            $taxAmount      = $data['tax_amount'] ?? null;

            $amount = $subtotalAmount
                - ($discountAmount ?? 0)
                + ($taxAmount ?? 0);

            $invoice->update([
                'customer_id'     => $data['customer_id'],
                'salesperson_id'  => $data['salesperson_id'],
                'invoice_date'    => $data['invoice_date'],
                'due_date'        => $data['due_date'] ?? null,
                'subtotal_amount' => $subtotalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'amount'          => $amount,
                'status'          => $data['status'],
            ]);

            $invoice->details()->delete();

            foreach ($data['details'] as $detail) {
                $product = Product::with('category', 'unit')->find($detail['product_id']);

                $invoice->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'subtotal_amount' => $detail['subtotal_amount'],
                    'discount_amount' => $detail['discount_amount'] ?? null,
                    'tax_amount'      => $detail['tax_amount'] ?? null,
                    'amount'          => $detail['amount'],
                    'product_snapshot' => [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'unit'     => $product->unit?->name,
                        'category' => $product->category?->name,
                    ],
                ]);
            }
        });

        return redirect()->route('invoices.show', $encryptedId)->with([
            'success' => ['title' => 'Invoice Updated', 'message' => 'Invoice has been updated.'],
        ]);
    }

    public function cancel($encryptedId)
    {
        $invoice = Invoice::findOrFail(Encryption::decrypt($encryptedId));

        if (! $invoice->status->canCancel()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Cancel', 'message' => 'Invoice with status "' . $invoice->status->label() . '" cannot be cancelled.'],
            ]);
        }

        $invoice->update(['status' => InvoiceStatus::CANCELLED]);

        return redirect()->back()->with([
            'success' => ['title' => 'Invoice Cancelled', 'message' => 'Invoice has been cancelled.'],
        ]);
    }
}
