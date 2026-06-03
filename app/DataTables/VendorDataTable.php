<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class VendorDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Vendor::class,
            view: 'vendors',
            route: 'vendors',
            module: Module::Vendor->value,
        );
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('type', fn($row) => $row->type->label());
    }
}
