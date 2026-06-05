<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class PurchaseDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Purchase::class,
            view: 'purchases',
            route: 'purchases',
            module: Module::Purchase->value,
        );
    }

    public function query(): QueryBuilder
    {
        return Purchase::with('vendor')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('vendor_id', fn($row) => $row->vendor->name ?? '-')
            ->editColumn('grand_total', fn($row) => 'Rp ' . number_format($row->grand_total, 0, ',', '.'))
            ->editColumn('purchase_date', fn($row) => $row->purchase_date->translatedFormat('d M Y'))
            ->editColumn('status', fn($row) => '<span class="badge ' . $row->status->badgeClass() . '">' . $row->status->label() . '</span>')
            ->rawColumns(['action', 'status']);
    }
}
