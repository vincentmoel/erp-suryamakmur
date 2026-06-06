<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\SalesReturn;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class SalesReturnDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: SalesReturn::class,
            view: 'sales-returns',
            route: 'sales-returns',
            module: Module::SalesReturn->value,
        );
    }

    public function query(): QueryBuilder
    {
        return SalesReturn::with('invoice')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('invoice_id', fn($row) => $row->invoice->code ?? '-')
            ->editColumn('return_date', fn($row) => $row->return_date->translatedFormat('d M Y'));
    }
}
