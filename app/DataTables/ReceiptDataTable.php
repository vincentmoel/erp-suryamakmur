<?php

namespace App\DataTables;

use App\Enums\InvoiceStatus;
use App\Enums\Module;
use App\Libraries\DataTablesComponentBuilder;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Blade;
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
            exceptActionButton: ['delete'],
        );
    }

    public function query(): QueryBuilder
    {
        return Receipt::with('customer')
            ->withSum('details', 'amount')
            ->withCount([
                'details as paid_invoices_count'          => fn($q) => $q->whereHas('invoice', fn($q) => $q->where('status', InvoiceStatus::PAID->value)),
                'details as partially_paid_invoices_count' => fn($q) => $q->whereHas('invoice', fn($q) => $q->where('status', InvoiceStatus::PARTIALLY_PAID->value)),
            ])
            ->latest()
            ->newQuery();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('customer_id', function ($row) {
                $c = $row->customer;
                if (!$c) return '-';
                return $c->company_name ? $c->company_name . ' (' . $c->name . ')' : $c->name;
            })
            ->editColumn('payment_method', fn($row) => '<span class="badge ' . $row->payment_method->badgeClass() . '">' . $row->payment_method->icon() . $row->payment_method->label() . '</span>')
            ->editColumn('receipt_date', fn($row) => $row->receipt_date->translatedFormat('d F Y'))
            ->addColumn('amount_total', fn($row) => 'Rp ' . number_format($row->details_sum_amount ?? 0, 0, ',', '.'))
            ->addColumn('allocation_summary', function ($row) {
                $parts = [];
                if ($row->paid_invoices_count)          $parts[] = $row->paid_invoices_count . ' ' . InvoiceStatus::PAID->label();
                if ($row->partially_paid_invoices_count) $parts[] = $row->partially_paid_invoices_count . ' ' . InvoiceStatus::PARTIALLY_PAID->label();
                return $parts ? implode(', ', $parts) : '-';
            })
            ->addColumn('action', function ($row) {
                $encryptedId = \App\Helpers\Encryption::encrypt($row->id);
                $deleteUrl   = route('receipts.destroy', ['encryptedId' => $encryptedId]);
                $deleteIcon  = Blade::render('<x-icon name="delete" class="size-4" />');
                $deleteBtn   = "<button type='button'
                    class='dt-action-btn dt-action-btn--destructive receipt-delete-btn'
                    data-url='{$deleteUrl}'
                    data-code='" . e($row->code) . "'
                    title='Delete'>{$deleteIcon}</button>";

                return DataTablesComponentBuilder::actionButton(
                    [
                        'show' => route('receipts.show', ['encryptedId' => $encryptedId]),
                        'edit' => route('receipts.edit', ['encryptedId' => $encryptedId]),
                    ],
                    Module::Receipt->name,
                    [
                        [
                            'module'           => Module::Receipt->value,
                            'modulePermission' => 'delete',
                            'html'             => $deleteBtn,
                            'position'         => 'after',
                        ],
                    ]
                );
            })
            ->rawColumns(['action', 'payment_method']);
    }
}
