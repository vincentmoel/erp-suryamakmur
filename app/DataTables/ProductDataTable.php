<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\HtmlBuilder;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class ProductDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Product::class,
            view: 'products',
            route: 'products',
            module: Module::Product->value,
        );
    }

    public function query(): QueryBuilder
    {
        return $this->model::with('category', 'unit')
            ->addSelect([
                'products.*',
                \Illuminate\Support\Facades\DB::raw('(SELECT COALESCE(SUM(invd.quantity), 0) FROM inventory_details invd JOIN inventories inv ON inv.id = invd.inventory_id WHERE inv.product_id = products.id) as total_stock'),
            ])
            ->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('category_id', fn($row) => $row->category?->name ?? '-')
            ->editColumn('unit_id', fn($row) => $row->unit?->name ?? '-')
            ->editColumn('selling_price', fn($row) => 'Rp ' . number_format($row->selling_price, 0, ',', '.'))
            ->addColumn('stock', function ($row) {
                $qty  = $row->total_stock ?? 0;
                $unit = $row->unit?->name ?? '';
                $eye  = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1rem;height:1rem;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>';
                return '<button type="button" class="btn-stock-detail" data-product-id="' . $row->id . '" style="display:inline-flex;align-items:center;gap:0.375rem;background:none;border:none;cursor:pointer;padding:0;color:inherit;">'
                    . $qty . ' ' . $unit . $eye
                    . '</button>';
            })
            ->editColumn('is_active', function ($row) {
                $encryptedId = \App\Helpers\Encryption::encrypt($row->id);
                $url = route('products.toggleActive', $encryptedId);
                return HtmlBuilder::toggle($row->is_active, url: $url, label: $row->is_active ? __('general.active') : __('general.inactive'));
            })
            ->rawColumns(['action', 'is_active', 'stock']);
    }
}
