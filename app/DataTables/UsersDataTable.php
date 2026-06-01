<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\Encryption;
use App\Libraries\DataTablesComponentBuilder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
 
class UsersDataTable extends DataTable
{
    protected $trashed;

    public function __construct($trashed = false)
    {
        $this->trashed = $trashed;
    }

    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        if($this->trashed)
        {
            $query = $query->onlyTrashed();
        }

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $encryptedId = Encryption::encrypt($row->id);

                if($this->trashed)
                {
                    $route['restore'] = route('users.restore', ['encryptedId' => $encryptedId]);
                }
                else
                {
                    $route['show'] = route('users.show', ['encryptedId' => $encryptedId]);
                    $route['edit'] = route('users.edit', ['encryptedId' => $encryptedId]);
                    $route['delete'] = route('users.destroy', ['encryptedId' => $encryptedId]);
                }

                return DataTablesComponentBuilder::actionButton(
                    $route,
                    Module::User->name,
                );
            })
            ->editColumn('last_seen', function($row){
                if (Carbon::parse($row->last_seen)->diffInMinutes() < 3 && $row->last_seen != null)
                {
                    return '<span class="logged-in" style="color:green;">● Online</span>';
                }

                $lastSeen = "Never Logged In";
                
                if ($row->last_seen != null)
                {
                    $lastSeen = Carbon::parse($row->last_seen)->diffForHumans();
                }

                return '<span class="logged-in text-muted">● Offline (' . $lastSeen . ')</span>';
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
            ->rawColumns(['last_seen', 'action']);
    }
 
    /**
     * Get query source of dataTable.
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->with('user_created_by', 'user_updated_by')->latest()->newQuery();
    }
}