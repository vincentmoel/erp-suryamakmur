<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Libraries\DataTablesComponentBuilder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

class UsersDataTable extends BaseDataTable
{
    public function __construct(bool $trashed = false)
    {
        parent::__construct(
            trashed: $trashed,
            model: User::class,
            view: 'users',
            route: 'users',
            module: Module::User->value,
        );
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return parent::dataTable($query)
            ->editColumn('name', fn($row) => DataTablesComponentBuilder::userProfile($row))
            ->editColumn('last_seen', fn($row) => DataTablesComponentBuilder::userStatus($row))
            ->rawColumns(['name', 'last_seen', 'action']);
    }

    public function query(): QueryBuilder
    {
        return parent::query()->with('roles');
    }
}
