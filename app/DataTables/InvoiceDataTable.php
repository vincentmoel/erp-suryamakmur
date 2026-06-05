<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class InvoiceDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Invoice::class,
            view: 'invoices',
            route: 'invoices',
            module: Module::Invoice->value,
        );
    }

    public function query(): QueryBuilder
    {
        return Invoice::with('customer', 'salesperson')->withSum('details', 'amount')->latest()->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('customer_id', fn($row) => $row->customer->name ?? '-')
            ->editColumn('salesperson_id', fn($row) => $row->salesperson->name ?? '-')
            ->editColumn('status', fn($row) => '<span class="badge ' . $row->status->badgeClass() . '">' . $row->status->label() . '</span>')
            ->editColumn('amount', fn($row) => 'Rp ' . number_format($row->amount, 0, ',', '.'))
            ->editColumn('invoice_date', fn($row) => $row->invoice_date->translatedFormat('d M Y'))
            ->rawColumns(['action', 'status']);
    }
}
