<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Http\Controllers\Traits\IsActive;
use App\Helpers\FileManager;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends BaseController
{
    use IsActive;
    public function __construct()
    {
        parent::__construct(
            Product::class,
            'products',
            'Product',
            'products',
            Module::Product->name,
            ProductRequest::class,
            ProductDataTable::class,
        );
    }

    public function create()
    {
        return view('products.create', [
            'title'      => $this->title,
            'route'      => $this->route,
            'categories' => Category::orderBy('name')->get(),
            'units'      => Unit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(ProductRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        if ($request->hasFile('image')) {
            $validatedData['image'] = FileManager::store($request->file('image'), 'products');
        } else {
            unset($validatedData['image']);
        }

        $initialStockEnabled = (bool) ($validatedData['initial_stock_enabled'] ?? false);

        $batches = $validatedData['initial_stocks'] ?? [];

        $productData = collect($validatedData)->except([
            'initial_stock_enabled', 'initial_stocks',
        ])->toArray();

        DB::transaction(function () use ($productData, $batches, $initialStockEnabled) {
            $product = Product::create($productData);

            if ($initialStockEnabled && count($batches) > 0) {
                $lastBatch = end($batches);

                $inventory = Inventory::create([
                    'product_id' => $product->id,
                    'unit_cost'  => $lastBatch['unit_cost'] ?? 0,
                ]);

                $runningBalance = 0;
                foreach ($batches as $batch) {
                    $qty = (int) ($batch['quantity'] ?? 0);
                    $runningBalance += $qty;

                    $detail = InventoryDetail::create([
                        'inventory_id' => $inventory->id,
                        'quantity'     => $qty,
                        'received_at'  => $batch['received_at'],
                    ]);

                    InventoryLog::create([
                        'inventory_detail_id' => $detail->id,
                        'source'              => 'initiate',
                        'reference_id'        => $product->id,
                        'quantity'            => $qty,
                        'balance_after'       => $runningBalance,
                        'notes'               => null,
                    ]);
                }
            }
        });

        $flash = ['success' => ["title" => "Success Add", "message" => "Your data has been saved."]];

        if ($request->input('_action') === 'save_and_create') {
            return redirect()->route('products.create')->with($flash);
        }

        return redirect()->route('products.index')->with($flash);
    }

    public function edit($encryptedId)
    {
        $data = Product::with('inventory')->findOrFail(Encryption::decrypt($encryptedId));

        return view('products.edit', [
            'data'        => $data,
            'inventory'   => $data->inventory,
            'title'       => $this->title,
            'route'       => $this->route,
            'encryptedId' => $encryptedId,
            'categories'  => Category::orderBy('name')->get(),
            'units'       => Unit::orderBy('name')->get(),
        ]);
    }

    public function ajaxInfo(int $id)
    {
        $product = Product::with('unit')->find($id);

        if (! $product) {
            return response()->json(null, 404);
        }

        return response()->json([
            'unit'          => $product->unit?->name,
            'selling_price' => $product->selling_price,
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $formRequest = app()->make(ProductRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        if ($request->hasFile('image')) {
            $validatedData['image'] = FileManager::store($request->file('image'), 'products');
        } else {
            unset($validatedData['image']);
        }

        $initialStockEnabled = (bool) ($validatedData['initial_stock_enabled'] ?? false);
        $batches = $validatedData['initial_stocks'] ?? [];

        $productData = collect($validatedData)->except([
            'initial_stock_enabled', 'initial_stocks',
        ])->toArray();

        DB::transaction(function () use ($productData, $batches, $initialStockEnabled, $encryptedId) {
            $product = Product::findOrFail(Encryption::decrypt($encryptedId));
            $product->update($productData);

            if ($initialStockEnabled && count($batches) > 0) {
                $lastBatch = end($batches);

                $inventory = $product->inventory ?? Inventory::create([
                    'product_id' => $product->id,
                    'unit_cost'  => $lastBatch['unit_cost'] ?? 0,
                ]);

                if ($product->inventory) {
                    $inventory->update(['unit_cost' => $lastBatch['unit_cost'] ?? 0]);
                }

                $runningBalance = $inventory->total_quantity;
                foreach ($batches as $batch) {
                    $qty = (int) ($batch['quantity'] ?? 0);
                    $runningBalance += $qty;

                    $detail = InventoryDetail::create([
                        'inventory_id' => $inventory->id,
                        'quantity'     => $qty,
                        'received_at'  => $batch['received_at'],
                    ]);

                    InventoryLog::create([
                        'inventory_detail_id' => $detail->id,
                        'source'              => 'initiate',
                        'reference_id'        => $product->id,
                        'quantity'            => $qty,
                        'balance_after'       => $runningBalance,
                        'notes'               => null,
                    ]);
                }
            }
        });

        return redirect()->back()->with([
            'success' => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }
}
