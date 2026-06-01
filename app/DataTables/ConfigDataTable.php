<?php

namespace App\DataTables;

use App\Enums\Module;
use App\Helpers\Encryption;
use App\Libraries\DataTablesComponentBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
 
class ConfigDataTable extends DataTable
{
    protected $trashed;
    protected $model;
    protected $view;
    protected $route;
    protected $module;
    protected $exceptActionButton;

    public function __construct($trashed, $model, $view, $route, $module, $exceptActionButton = [])
    {
        $this->trashed = $trashed;
        $this->model = $model;
        $this->view = $view;
        $this->route = $route;
        $this->module = $module;
        $this->exceptActionButton = $exceptActionButton;
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
                    $route['restore'] = route("$this->route.restore", ['encryptedId' => $encryptedId]);
                }
                else
                {
                    if(!in_array('show', $this->exceptActionButton))
                    {
                        $route['show'] = route("$this->route.show", ['encryptedId' => $encryptedId]);
                    }

                    if(!in_array('edit', $this->exceptActionButton))
                    {
                        $route['edit'] = route("$this->route.edit", ['encryptedId' => $encryptedId]);
                    }

                    if(!in_array('delete', $this->exceptActionButton))
                    {
                        $route['delete'] = route("$this->route.destroy", ['encryptedId' => $encryptedId]);
                    }
                }

                $updateUrl = route("$this->route.update", ['encryptedId' => $encryptedId]);

                return DataTablesComponentBuilder::actionButton(
                    $route ?? [],
                    Module::from($this->module)->name,
                    [
                        [
                            'module' => $this->module,
                            'modulePermission' => 'update',
                            'html' => "<button type='button' data-url='{$updateUrl}' data-name='{$row->name}' data-value='{$row->value}' class='btn-edit-value btn btn-sm btn-light-secondary text-secondary'>
                                <i class='ti ti-edit fs-4'></i>
                            </button>"
                        ]
                    ]
                );
            })
            ->editColumn('created_at', function($row){
                return Carbon::parse($row->created_at)->translatedFormat('d F Y | H:i:s');
            })
            ->editColumn('updated_at', function($row){
                return Carbon::parse($row->updated_at)->translatedFormat('d F Y | H:i:s');
            })
            ->rawColumns(['action']);
    }
 
    public function query(): QueryBuilder
    {
        return $this->model::latest()->newQuery();
    }
}