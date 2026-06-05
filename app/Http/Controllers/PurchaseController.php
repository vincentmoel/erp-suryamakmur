<?php

namespace App\Http\Controllers;

use App\DataTables\PurchaseDataTable;
use App\Enums\Module;
use App\Enums\PurchaseStatus;
use App\Enums\InventorySource;
use App\Helpers\Encryption;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Purchase::class,
            'purchases',
            'Purchase',
            'purchases',
            Module::Purchase->name,
            PurchaseRequest::class,
            PurchaseDataTable::class,
        );
    }

    private function vendorOptions(): array
    {
        return Vendor::orderBy('name')->get(['id', 'name'])
            ->map(fn($v) => ['value' => $v->id, 'label' => $v->name])
            ->toArray();
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
        return view('purchases.create', [
            'title'          => $this->title,
            'route'          => $this->route,
            'vendorOptions'  => $this->vendorOptions(),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(PurchaseRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($data) {
            $subtotal       = collect($data['details'])->sum('subtotal');
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxPercent     = (float) ($data['tax_percent'] ?? 0);
            $taxAmount      = (int) round($subtotal * $taxPercent / 100);
            $grandTotal     = $subtotal - $discountAmount + $taxAmount;

            $purchase = Purchase::create([
                'vendor_id'       => $data['vendor_id'],
                'invoice_number'  => $data['invoice_number'] ?? null,
                'purchase_date'   => $data['purchase_date'],
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_percent'     => $taxPercent,
                'tax_amount'      => $taxAmount,
                'grand_total'     => $grandTotal,
                'status'          => $data['status'],
                'notes'           => $data['notes'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                $purchase->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'discount_amount' => (int) ($detail['discount_amount'] ?? 0),
                    'tax_percent'     => 0,
                    'tax_amount'      => 0,
                    'subtotal'        => $detail['subtotal'],
                ]);
            }
        });

        return redirect()->route('purchases.index')->with([
            'success' => ['title' => 'Purchase Created', 'message' => 'Purchase order has been saved.'],
        ]);
    }

    public function show($encryptedId)
    {
        $purchase = Purchase::with('vendor', 'details.product', 'user_created_by', 'user_updated_by')
            ->findOrFail(Encryption::decrypt($encryptedId));

        return view('purchases.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'data'        => $purchase,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $purchase = Purchase::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $purchase->status->canEdit()) {
            return redirect()->route('purchases.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Purchase with status "' . $purchase->status->label() . '" cannot be edited.'],
            ]);
        }

        return view('purchases.edit', [
            'title'          => $this->title,
            'route'          => $this->route,
            'data'           => $purchase,
            'encryptedId'    => $encryptedId,
            'vendorOptions'  => $this->vendorOptions(),
            'productOptions' => $this->productOptions(),
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $purchase = Purchase::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $purchase->status->canEdit()) {
            return redirect()->route('purchases.show', $encryptedId)->with([
                'error' => ['title' => 'Cannot Edit', 'message' => 'Purchase with status "' . $purchase->status->label() . '" cannot be edited.'],
            ]);
        }

        $formRequest = app()->make(PurchaseRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($purchase, $data) {
            $subtotal       = collect($data['details'])->sum('subtotal');
            $discountAmount = (int) ($data['discount_amount'] ?? 0);
            $taxPercent     = (float) ($data['tax_percent'] ?? 0);
            $taxAmount      = (int) round($subtotal * $taxPercent / 100);
            $grandTotal     = $subtotal - $discountAmount + $taxAmount;

            $purchase->update([
                'vendor_id'       => $data['vendor_id'],
                'invoice_number'  => $data['invoice_number'] ?? null,
                'purchase_date'   => $data['purchase_date'],
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_percent'     => $taxPercent,
                'tax_amount'      => $taxAmount,
                'grand_total'     => $grandTotal,
                'status'          => $data['status'],
                'notes'           => $data['notes'] ?? null,
            ]);

            $purchase->details()->delete();

            foreach ($data['details'] as $detail) {
                $purchase->details()->create([
                    'product_id'      => $detail['product_id'],
                    'quantity'        => $detail['quantity'],
                    'unit_price'      => $detail['unit_price'],
                    'discount_amount' => (int) ($detail['discount_amount'] ?? 0),
                    'tax_percent'     => 0,
                    'tax_amount'      => 0,
                    'subtotal'        => $detail['subtotal'],
                ]);
            }
        });

        return redirect()->route('purchases.show', $encryptedId)->with([
            'success' => ['title' => 'Purchase Updated', 'message' => 'Purchase order has been updated.'],
        ]);
    }

    public function receive($encryptedId)
    {
        $purchase = Purchase::with('details')->findOrFail(Encryption::decrypt($encryptedId));

        if (! $purchase->status->canReceive()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Receive', 'message' => 'Purchase with status "' . $purchase->status->label() . '" cannot be received.'],
            ]);
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->details as $detail) {
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
                    receivedAt:  $purchase->purchase_date,
                    source:      InventorySource::PURCHASE,
                    referenceId: $purchase->id,
                    notes:       'Purchase #' . $purchase->code,
                );
            }

            $purchase->update(['status' => PurchaseStatus::RECEIVED]);
        });

        return redirect()->back()->with([
            'success' => ['title' => 'Purchase Received', 'message' => 'Stock has been updated and purchase marked as received.'],
        ]);
    }

    public function cancel($encryptedId)
    {
        $purchase = Purchase::findOrFail(Encryption::decrypt($encryptedId));

        if (! $purchase->status->canCancel()) {
            return redirect()->back()->with([
                'error' => ['title' => 'Cannot Cancel', 'message' => 'Purchase with status "' . $purchase->status->label() . '" cannot be cancelled.'],
            ]);
        }

        $purchase->update(['status' => PurchaseStatus::CANCELLED]);

        return redirect()->back()->with([
            'success' => ['title' => 'Purchase Cancelled', 'message' => 'Purchase order has been cancelled.'],
        ]);
    }
}
