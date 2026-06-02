<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Unit;

class UnitDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Unit::class,
            view: 'units',
            route: 'units',
            module: Module::Unit->value,
        );
    }
}
