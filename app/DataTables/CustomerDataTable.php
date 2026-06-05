<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\HtmlBuilder;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class CustomerDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Customer::class,
            view: 'customers',
            route: 'customers',
            module: Module::Customer->value,
        );
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('type', fn($row) => $row->type->label())
            ->editColumn('is_active', function ($row) {
                $encryptedId = \App\Helpers\Encryption::encrypt($row->id);
                $url = route('customers.toggleActive', $encryptedId);
                return HtmlBuilder::toggle($row->is_active, url: $url, label: $row->is_active ? 'Active' : 'Inactive');
            })
            ->rawColumns(['action', 'is_active']);
    }
}
