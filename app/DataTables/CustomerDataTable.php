<?php

namespace App\DataTables;

use App\Enums\Module;
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
            ->editColumn('type', function ($row) {
                return $row->type->label();
            });
    }
}
