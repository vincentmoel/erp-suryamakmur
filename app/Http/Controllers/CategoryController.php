<?php

namespace App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use App\Enums\Module;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Category::class,
            'categories',
            'Category',
            'categories',
            Module::Category->name,
            CategoryRequest::class,
            CategoryDataTable::class,
        );
    }
}
