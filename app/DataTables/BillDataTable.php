<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Bill;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class BillDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Bill::class,
            view: 'bills',
            route: 'bills',
            module: Module::Bill->value,
        );
    }

    public function query(): QueryBuilder
    {
        return Bill::with('vendor')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('vendor_id', fn($row) => $row->vendor->name ?? '-')
            ->editColumn('grand_total', fn($row) => 'Rp ' . number_format($row->grand_total, 0, ',', '.'))
            ->editColumn('bill_date', fn($row) => $row->bill_date->translatedFormat('d M Y'))
            ->editColumn('status', fn($row) => '<span class="badge ' . $row->status->badgeClass() . '">' . $row->status->label() . '</span>')
            ->rawColumns(['action', 'status']);
    }
}
