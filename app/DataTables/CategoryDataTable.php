<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Category;

class CategoryDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: Category::class,
            view: 'categories',
            route: 'categories',
            module: Module::Category->value,
        );
    }
}
