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
            ->editColumn('name', fn($row) => DataTablesComponentBuilder::userProfile($row))
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
                $iconOnline  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:0.6rem;height:0.6rem;flex-shrink:0;fill:currentColor;stroke:none"><circle cx="12" cy="12" r="8"/></svg>';
                $iconOffline = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:0.6rem;height:0.6rem;flex-shrink:0;fill:none;stroke:currentColor;stroke-width:2.5"><circle cx="12" cy="12" r="8"/></svg>';

                if ($row->last_seen != null && Carbon::parse($row->last_seen)->diffInMinutes() < 3)
                {
                    return '<span style="display:inline-flex;align-items:center;gap:0.35rem;color:#16a34a;">' . $iconOnline . 'Online</span>';
                }

                $lastSeen = $row->last_seen ? Carbon::parse($row->last_seen)->diffForHumans() : null;
                $label    = $lastSeen ? 'Offline (' . $lastSeen . ')' : 'Never';

                return '<span style="display:inline-flex;align-items:center;gap:0.35rem;color:var(--muted-foreground);">' . $iconOffline . $label . '</span>';
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
            ->rawColumns(['name', 'last_seen', 'action']);
    }
 
    /**
     * Get query source of dataTable.
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
    {
        return $model->with('user_created_by', 'user_updated_by', 'roles')->latest()->newQuery();
    }
}