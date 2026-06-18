<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\Encryption;
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
        $query = Invoice::with('customer', 'salesperson')->withSum('details', 'amount')->withSum('receiptDetails', 'amount');

        if ($customerId = request('filter_customer')) {
            $query->where('customer_id', $customerId);
        }

        if ($salespersonId = request('filter_salesperson')) {
            $query->where('salesperson_id', $salespersonId);
        }

        if ($dateFrom = request('filter_date_from')) {
            $query->whereDate('invoice_date', '>=', $dateFrom);
        }

        if ($dateTo = request('filter_date_to')) {
            $query->whereDate('invoice_date', '<=', $dateTo);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->setRowAttr([
                'data-show-url'    => fn($row) => route('invoices.show', ['encryptedId' => Encryption::encrypt($row->id)]),
                'data-preview-url' => fn($row) => route('ajax.invoices.preview', ['encryptedId' => Encryption::encrypt($row->id)]),
                'style'            => 'cursor: pointer;',
            ])
            ->editColumn('customer_id', fn($row) => $row->customer->name ?? '-')
            ->editColumn('salesperson_id', fn($row) => $row->salesperson->name ?? '-')
            ->editColumn('status', fn($row) => '<span class="badge ' . $row->status->badgeClass() . '">' . $row->status->icon() . $row->status->label() . '</span>')
            ->editColumn('amount', fn($row) => 'Rp ' . number_format($row->amount, 0, ',', '.'))
            ->addColumn('paid_amount', fn($row) => 'Rp ' . number_format($row->paid_amount ?? 0, 0, ',', '.'))
            ->editColumn('invoice_date', fn($row) => $row->invoice_date->translatedFormat('d F Y'))
            ->filterColumn('customer_id', fn($q, $k) => $q->whereHas('customer', fn($r) => $r->where('name', 'LIKE', "%{$k}%")))
            ->filterColumn('salesperson_id', fn($q, $k) => $q->whereHas('salesperson', fn($r) => $r->where('name', 'LIKE', "%{$k}%")))
            ->filterColumn('invoice_date', fn($q, $k) => $q->whereRaw("DATE_FORMAT(invoice_date, '%d %M %Y') LIKE ?", ["%{$k}%"]))
            ->filterColumn('amount', fn($q, $k) => $q->where('grand_total', 'LIKE', "%{$k}%"))
            ->filterColumn('status', fn($q, $k) => $q->where('status', 'LIKE', "%{$k}%"))
            ->orderColumn('amount', 'grand_total $1')
            ->rawColumns(['action', 'status']);
    }
}
