<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class RoleDataTable extends BaseDataTable
{
    public function __construct()
    {
        parent::__construct(
            trashed: false,
            model: Role::class,
            view: 'roles',
            route: 'roles',
            module: Module::Role->value,
        );
    }

    public function query(): QueryBuilder
    {
        return Role::with('user_created_by', 'user_updated_by')
            ->where(fn($q) => $q->where('editable', 1)->orWhere('deletable', 1))
            ->latest()
            ->newQuery();
    }
}
