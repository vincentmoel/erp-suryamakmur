<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class ReceiptDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Receipt::class,
            view: 'receipts',
            route: 'receipts',
            module: Module::Receipt->value,
        );
    }

    public function query(): QueryBuilder
    {
        return Receipt::with('customer')->withSum('details', 'amount')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('customer_id', fn($row) => $row->customer->name ?? '-')
            ->editColumn('payment_method', fn($row) => $row->payment_method->label())
            ->editColumn('receipt_date', fn($row) => $row->receipt_date->translatedFormat('d M Y'))
            ->addColumn('amount_total', fn($row) => 'Rp ' . number_format($row->details_sum_amount ?? 0, 0, ',', '.'))
            ->rawColumns(['action']);
    }
}
