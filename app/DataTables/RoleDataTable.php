<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\Encryption;
use App\Libraries\DataTablesComponentBuilder;
use App\Models\Grade;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
 
class RoleDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $encryptedId = Encryption::encrypt($row->id);

                $route['show'] = route('roles.show', ['encryptedId' => $encryptedId]);
                $route['edit'] = route('roles.edit', ['encryptedId' => $encryptedId]);
                $route['delete'] = route('roles.destroy', ['encryptedId' => $encryptedId]);

                return DataTablesComponentBuilder::actionButton(
                    $route,
                    Module::Role->value,
                );
            })
            ->editColumn('created_by', function($row){
                return $row->user_created_by->name;
            })
            ->editColumn('updated_by', function($row){
                return $row->user_updated_by->name;
            })
            ->editColumn('created_at', function($row){
                return Carbon::parse($row->created_at)->translatedFormat('d F Y | H:i:s');
            })
            ->editColumn('updated_at', function($row){
                return Carbon::parse($row->updated_at)->translatedFormat('d F Y | H:i:s');
            })
            ->rawColumns(['action']);
    }
 
    public function query(Role $model): QueryBuilder
    {
        return $model->where('editable', 1)->orWhere('deletable', 1)->latest()->newQuery();
    }
}