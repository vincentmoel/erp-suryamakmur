<?php

namespace App\Http\Controllers;

use App\DataTables\BillDataTable;
use App\Enums\Module;
use App\Enums\BillStatus;
use App\Enums\InventorySource;
use App\Helpers\Encryption;
use App\Http\Requests\BillRequest;
use App\Models\Product;
use App\Models\Bill;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Bill::class,
            'bills',
            'Bill',
            'bills',
            Module::Bill->name,
            BillRequest::class,
            BillDataTable::class,
        );
    }

    private function vendorOptions(): array
    {
        return Vendor::orderBy('name')->get(['id', 'name', 'contact_person'])
            ->map(fn($v) => [
                'value' => $v->id,
                'label' => $v->contact_person
                    ? "{$v->name} — {$v->contact_person}"
                    : $v->name,
            ])
            ->toArray();
    }

    private function vendors()
    {
        return Vendor::orderBy('name')->get([
            'id', 'name', 'type', 'tax_number', 'phone', 'email',
            'contact_person', 'address', 'city', 'notes',
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

    public function create()
    {
        return view('bills.create', [
            'title'          => $this->title,
            'route'          => $this->route,
            'vendors'        => $this->vendors(),
            'vendorOptions'  => $this->vendorOptions(),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(BillRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($data) {
            $subtotal       = collect($data['details'])->sum('subtotal');
            $discountPct    = isset($data['discount_percent']) && $data['discount_percent'] !== '' ? (float) $data['discount_percent'] : null;
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxPct         = isset($data['tax_percent']) && $data['tax_percent'] !== '' ? (float) $data['tax_percent'] : null;
            $taxAmount      = (int) ($data['tax_amount'] ?? 0);
            $grandTotal     = $subtotal - $discountAmount + $taxAmount;

            $bill = Bill::create([
                'vendor_id'        => $data['vendor_id'],
                'invoice_number'   => $data['invoice_number'] ?? null,
                'bill_date'    => $data['bill_date'],
                'subtotal'         => $subtotal,
                'discount_percent' => $discountPct,
                'discount_amount'  => $discountAmount,
                'tax_percent'      => $taxPct ?? 0,
                'tax_amount'       => $taxAmount,
                'grand_total'      => $grandTotal,
                'status'           => $data['status'],
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                $discPct = isset($detail['discount_percent']) && $detail['discount_percent'] !== '' ? (float) $detail['discount_percent'] : null;
                $bill->details()->create([
                    'product_id'       => $detail['product_id'],
                    'quantity'         => $detail['quantity'],
                    'unit_price'       => $detail['unit_price'],
                    'discount_percent' => $discPct,
                    'discount_amount'  => (int) ($detail['discount_amount'] ?? 0),
                    'tax_percent'      => 0,
                    'tax_amount'       => 0,
                    'subtotal'         => $detail['subtotal'],
                ]);
            }
        });

        return redirect()->route('bills.index')->with([
            'success' => ['title' => 'Bill Created', 'message' => 'Bill has been saved.'],
        ]);
    }

    public function show($encryptedId)
    {
        $bill = Bill::with('vendor', 'details.product', 'user_created_by', 'user_updated_by')
            ->findOrFail(Encryption::decrypt($encryptedId));

        return view('bills.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'data'        => $bill,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $bill = Bill::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $bill->status->canEdit()) {
            return redirect()->route('bills.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Bill with status "' . $bill->status->label() . '" cannot be edited.'],
            ]);
        }

        return view('bills.edit', [
            'title'          => $this->title,
            'route'          => $this->route,
            'data'           => $bill,
            'encryptedId'    => $encryptedId,
            'vendors'        => $this->vendors(),
            'vendorOptions'  => $this->vendorOptions(),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $bill = Bill::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $bill->status->canEdit()) {
            return redirect()->route('bills.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Bill with status "' . $bill->status->label() . '" cannot be edited.'],
            ]);
        }

        $formRequest = app()->make(BillRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($bill, $data) {
            $subtotal       = collect($data['details'])->sum('subtotal');
            $discountPct    = isset($data['discount_percent']) && $data['discount_percent'] !== '' ? (float) $data['discount_percent'] : null;
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxPct         = isset($data['tax_percent']) && $data['tax_percent'] !== '' ? (float) $data['tax_percent'] : null;
            $taxAmount      = (int) ($data['tax_amount'] ?? 0);
            $grandTotal     = $subtotal - $discountAmount + $taxAmount;

            $bill->update([
                'vendor_id'        => $data['vendor_id'],
                'invoice_number'   => $data['invoice_number'] ?? null,
                'bill_date'    => $data['bill_date'],
                'subtotal'         => $subtotal,
                'discount_percent' => $discountPct,
                'discount_amount'  => $discountAmount,
                'tax_percent'      => $taxPct ?? 0,
                'tax_amount'       => $taxAmount,
                'grand_total'      => $grandTotal,
                'status'           => $data['status'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $bill->details()->delete();

            foreach ($data['details'] as $detail) {
                $discPct = isset($detail['discount_percent']) && $detail['discount_percent'] !== '' ? (float) $detail['discount_percent'] : null;
                $bill->details()->create([
                    'product_id'       => $detail['product_id'],
                    'quantity'         => $detail['quantity'],
                    'unit_price'       => $detail['unit_price'],
                    'discount_percent' => $discPct,
                    'discount_amount'  => (int) ($detail['discount_amount'] ?? 0),
                    'tax_percent'      => 0,
                    'tax_amount'       => 0,
                    'subtotal'         => $detail['subtotal'],
                ]);
            }
        });

        return redirect()->route('bills.show', $encryptedId)->with([
            'success' => ['title' => 'Bill Updated', 'message' => 'Bill has been updated.'],
        ]);
    }

    public function receive($encryptedId)
    {
        $bill = Bill::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $bill->status->canReceive()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Receive', 'message' => 'Bill with status "' . $bill->status->label() . '" cannot be received.'],
            ]);
        }

        DB::transaction(function () use ($bill) {
            foreach ($bill->details as $detail) {
                if (! $detail->product_id) {
                    continue;
                }

                $unitCost = $detail->quantity > 0
                    ? (int) round(($detail->quantity * $detail->unit_price - $detail->discount_amount) / $detail->quantity)
                    : $detail->unit_price;

                InventoryService::addStock(
                    productId:   $detail->product_id,
                    unitCost:    $unitCost,
                    quantity:    $detail->quantity,
                    receivedAt:  $bill->bill_date,
                    source:      InventorySource::PURCHASE,
                    referenceId: $bill->id,
                    notes:       'Bill #' . $bill->code,
                );
            }

            $bill->update(['status' => BillStatus::RECEIVED]);
        });

        return redirect()->back()->with([
            'success' => ['title' => 'Bill Received', 'message' => 'Stock has been updated and bill marked as received.'],
        ]);
    }

    public function cancel($encryptedId)
    {
        $bill = Bill::findOrFail(Encryption::decrypt($encryptedId));

        if (! $bill->status->canCancel()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Cancel', 'message' => 'Bill with status "' . $bill->status->label() . '" cannot be cancelled.'],
            ]);
        }

        $bill->update(['status' => BillStatus::CANCELLED]);

        return redirect()->back()->with([
            'success' => ['title' => 'Bill Cancelled', 'message' => 'Bill has been cancelled.'],
        ]);
    }
}
