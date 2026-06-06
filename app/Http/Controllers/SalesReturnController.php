<?php

namespace App\Http\Controllers;

use App\DataTables\SalesReturnDataTable;
use App\Enums\InventorySource;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Helpers\CodeGenerator;
use App\Http\Requests\SalesReturnRequest;
use App\Models\Invoice;
use App\Models\InvoiceDetailBatch;
use App\Models\SalesReturn;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            SalesReturn::class,
            'sales-returns',
            'Sales Return',
            'sales-returns',
            Module::SalesReturn->name,
            SalesReturnRequest::class,
            SalesReturnDataTable::class,
        );
    }

    public function create()
    {
        $invoiceOptions = Invoice::orderByDesc('invoice_date')
            ->get(['id', 'code'])
            ->map(fn($i) => [
                'value'        => $i->id,
                'label'        => $i->code,
                'encrypted_id' => Encryption::encrypt($i->id),
            ])
            ->toArray();

        return view('sales-returns.create', [
            'title'          => $this->title,
            'route'          => $this->route,
            'invoiceOptions' => $invoiceOptions,
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(SalesReturnRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $data = $formRequest->validated();

        DB::transaction(function () use ($data) {
            $salesReturn = SalesReturn::create([
                'invoice_id'  => $data['invoice_id'],
                'return_date' => $data['return_date'],
                'notes'       => $data['notes'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                $batch = InvoiceDetailBatch::with('inventoryDetail.inventory', 'salesReturnDetails')
                    ->lockForUpdate()
                    ->findOrFail($detail['invoice_detail_batch_id']);

                $alreadyReturned = $batch->salesReturnDetails->sum('quantity');
                $returnable      = $batch->quantity - $alreadyReturned;

                if ($detail['quantity'] > $returnable) {
                    throw new \RuntimeException(
                        "Return quantity {$detail['quantity']} exceeds returnable stock {$returnable} for batch ID {$batch->id}."
                    );
                }

                $salesReturn->details()->create([
                    'invoice_detail_batch_id' => $batch->id,
                    'quantity'                => $detail['quantity'],
                ]);

                InventoryService::restoreStockBatch(
                    inventoryDetailId: $batch->inventory_detail_id,
                    quantity:          $detail['quantity'],
                    source:            InventorySource::SALES_RETURN,
                    referenceId:       $salesReturn->id,
                    notes:             'Sales Return #' . $salesReturn->code,
                );
            }
        });

        return redirect()->route('sales-returns.index')->with([
            'success' => ['title' => 'Sales Return Created', 'message' => 'Sales return has been saved.'],
        ]);
    }

    public function show($encryptedId)
    {
        $salesReturn = SalesReturn::with([
            'invoice',
            'details.invoiceDetailBatch.inventoryDetail.inventory',
            'details.invoiceDetailBatch.invoiceDetail',
            'user_created_by',
        ])->findOrFail(Encryption::decrypt($encryptedId));

        return view('sales-returns.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'data'        => $salesReturn,
            'encryptedId' => $encryptedId,
        ]);
    }

    /**
     * Returns returnable batches for a given invoice (AJAX).
     */
    public function ajaxReturnableBatches($encryptedId)
    {
        $invoice = Invoice::with([
            'details.batches.inventoryDetail.inventory',
            'details.batches.salesReturnDetails',
        ])->findOrFail(Encryption::decrypt($encryptedId));

        $batches = [];

        foreach ($invoice->details as $detail) {
            foreach ($detail->batches as $batch) {
                $alreadyReturned = $batch->salesReturnDetails->sum('quantity');
                $returnable      = $batch->quantity - $alreadyReturned;

                if ($returnable <= 0) {
                    continue;
                }

                $batches[] = [
                    'invoice_detail_batch_id' => $batch->id,
                    'product_name'            => $detail->product_snapshot['name'] ?? '-',
                    'unit_cost'               => $batch->unit_cost,
                    'unit_cost_formatted'     => 'Rp ' . number_format($batch->unit_cost, 0, ',', '.'),
                    'original_qty'            => $batch->quantity,
                    'already_returned'        => $alreadyReturned,
                    'returnable_qty'          => $returnable,
                ];
            }
        }

        return response()->json($batches);
    }
}
