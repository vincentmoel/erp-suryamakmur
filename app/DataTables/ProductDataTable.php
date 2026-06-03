<?php

namespace App\DataTables;

use App\Enums\Module;
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
        return $this->model::with('category', 'unit')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('category_id', fn($row) => $row->category?->name ?? '-')
            ->editColumn('unit_id', fn($row) => $row->unit?->name ?? '-');
    }
}
