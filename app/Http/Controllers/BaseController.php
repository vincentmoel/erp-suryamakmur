<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Encryption;
use App\Helpers\Response;

class BaseController extends Controller
{
    protected $model;
    protected $view;
    protected $title;
    protected $route;
    protected $module;
    protected $validationRequest;
    protected $dataTable;
    protected $exceptActionButton;

    public function __construct($model, $view, $title, $route, $module, $validationRequest, $dataTable, $exceptActionButton = [])
    {
        $this->model = $model;
        $this->view = $view;
        $this->title = $title;
        $this->route = $route;
        $this->module = $module;
        $this->validationRequest = $validationRequest;
        $this->dataTable = $dataTable;
        $this->exceptActionButton = $exceptActionButton;
    }

    public function index()
    {
        $dataTable = new $this->dataTable(false);

        return $dataTable->render("{$this->view}.index", [
            "title"  => $this->title,
            "route"  => $this->route,
            "module" => $this->module,
        ]);
    }

    public function create()
    {
        return view("{$this->view}.create", [
            "title" => $this->title,
            "route" => $this->route
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = $this->validationRequest::createFrom($request);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        $this->model::create($validatedData);

        $flash = ['success' => ["title" => __('general.success_add'), "message" => __('general.success_add_message')]];

        if ($request->input('_action') === 'save_and_create') {
            return redirect()->route("{$this->route}.create")->with($flash);
        }

        return redirect()->route("{$this->route}.index")->with($flash);
    }

    public function show($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));

        return view("{$this->view}.show", [
            "title"       => $this->title,
            "route"       => $this->route,
            "data"        => $data,
            "encryptedId" => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));

        return view("{$this->view}.edit", [
            "data"        => $data,
            "title"       => $this->title,
            "route"       => $this->route,
            "encryptedId" => $encryptedId,
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $formRequest = $this->validationRequest::createFrom($request);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));
        $data->update($validatedData);

        return redirect()->back()->with([
            'success' => ["title" => __('general.success_update'), "message" => __('general.success_update_message')]
        ]);
    }

    public function destroy($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));
        $data->delete();

        return Response::build(
            200,
            "Success",
            [
                "title" => __('general.success_delete'),
                "message" => __('general.success_delete_message')
            ]
        );
    }

    public function trashed()
    {
        $dataTable = new $this->dataTable(true);

        return $dataTable->render("{$this->view}.index", [
            "title"  => $this->title,
            "route"  => $this->route,
            "module" => $this->module,
        ]);
    }

    public function restore($encryptedId)
    {
        $data = $this->model::onlyTrashed()->findOrFail(Encryption::decrypt($encryptedId));
        $data->restore();

        return redirect()->back()->with([
            'success' => ["title" => __('general.success_restore'), "message" => __('general.success_restore_message')]
        ]);
    }
}
