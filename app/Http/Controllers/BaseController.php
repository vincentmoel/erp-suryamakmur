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
        $dataTable = new $this->dataTable(
            false,
            $this->model,
            $this->view,
            $this->route,
            $this->module,
            $this->exceptActionButton
        );

        return $dataTable->render("{$this->view}.index", [
            "title"     => $this->title,
            "route"     => $this->route,
            "module"    => $this->module
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
        $formRequest = app()->make($this->validationRequest);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        $this->model::create($validatedData);

        return redirect()->back()->with([
            'success' => ["title" => "Success Add", "message" => "Your data has been saved."]
        ]);
    }

    public function show($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));

        return view("{$this->view}.show", [
            "title" => $this->title,
            "route" => $this->route,
            "data" => $data,
        ]);
    }

    public function edit($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));

        return view("{$this->view}.edit", [
            "data"  => $data,
            "title" => $this->title,
            "route" => $this->route
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $formRequest = $this->validationRequest::createFrom($request);
        $formRequest = app()->make($this->validationRequest);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));
        $data->update($validatedData);

        return redirect()->back()->with([
            'success' => ["title" => "Success Update", "message" => "Your data has been updated."]
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
                "title" => "Success Delete",
                "message" => "Your data has been deleted."
            ]
        );
    }

    public function trashed()
    {
        $dataTable = new $this->dataTable(
            true,
            $this->model,
            $this->view,
            $this->route,
            $this->module,
        );

        return $dataTable->render("{$this->view}.index", [
            "title"     => $this->title,
            "route"     => $this->route,
            "module"    => $this->module
        ]);
    }

    public function restore($encryptedId)
    {
        $data = $this->model::onlyTrashed()->findOrFail(Encryption::decrypt($encryptedId));
        $data->restore();

        return redirect()->back()->with([
            'success' => ["title" => "Success Restore", "message" => "Your data has been restored."]
        ]);
    }
}
