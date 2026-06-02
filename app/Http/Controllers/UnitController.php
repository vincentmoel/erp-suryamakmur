<?php

namespace App\Http\Controllers;

use App\DataTables\UnitDataTable;
use App\Enums\Module;
use App\Http\Requests\UnitRequest;
use App\Models\Unit;

class UnitController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Unit::class,
            'units',
            'Unit',
            'units',
            Module::Unit->name,
            UnitRequest::class,
            UnitDataTable::class,
        );
    }
}
