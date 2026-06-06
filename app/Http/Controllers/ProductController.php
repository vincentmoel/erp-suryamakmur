<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Enums\InventorySource;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Http\Controllers\Traits\IsActive;
use App\Helpers\FileManager;
use App\Http\Requests\ProductRequest;
use App\Helpers\Response;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Unit;
use App\Services\InventoryService;
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
                $stockBatches = array_map(fn($b) => [
                    'product_id'  => $product->id,
                    'unit_cost'   => (int) ($b['unit_cost'] ?? 0),
                    'quantity'    => (int) ($b['quantity'] ?? 0),
                    'received_at' => $b['received_at'],
                ], $batches);

                InventoryService::addStockBulk(
                    $stockBatches,
                    source:      InventorySource::INITIATE,
                    referenceId: $product->id,
                );
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
        $data = Product::findOrFail(Encryption::decrypt($encryptedId));

        return view('products.edit', [
            'data'        => $data,
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

        $productData = collect($validatedData)->except([
            'initial_stock_enabled', 'initial_stocks',
        ])->toArray();

        $product = Product::findOrFail(Encryption::decrypt($encryptedId));
        $product->update($productData);

        return redirect()->back()->with([
            'success' => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }

    public function ajaxStock(int $id)
    {
        $product = Product::with('unit')->find($id);

        if (! $product) {
            return Response::build(404, 'Product not found');
        }

        $batches = Inventory::without('user_created_by', 'user_updated_by')
            ->select('inventories.unit_cost', DB::raw('SUM(invd.quantity) as qty'))
            ->join('inventory_details as invd', 'inventories.id', '=', 'invd.inventory_id')
            ->where('inventories.product_id', $id)
            ->groupBy('inventories.unit_cost')
            ->orderBy('inventories.unit_cost')
            ->having('qty', '>', 0)
            ->get()
            ->map(fn($row) => [
                'unit_cost'      => $row->unit_cost,
                'total_quantity' => (int) $row->qty,
            ]);

        return Response::build(200, 'OK', [
            'product_name' => $product->name,
            'unit'         => $product->unit?->name,
            'batches'      => $batches,
        ]);
    }
}
