<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Helpers\FileManager;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            Product::class,
            'products',
            'Product',
            'products',
            Module::Product->name,
            ProductRequest::class,
            ProductDataTable::class,
        );
    }

    public function create()
    {
        return view('products.create', [
            'title'      => $this->title,
            'route'      => $this->route,
            'categories' => Category::orderBy('name')->get(),
            'units'      => Unit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $formRequest = app()->make(ProductRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        if ($request->hasFile('image')) {
            $validatedData['image'] = FileManager::store($request->file('image'), 'products');
        } else {
            unset($validatedData['image']);
        }

        Product::create($validatedData);

        $flash = ['success' => ["title" => "Success Add", "message" => "Your data has been saved."]];

        if ($request->input('_action') === 'save_and_create') {
            return redirect()->route('products.create')->with($flash);
        }

        return redirect()->route('products.index')->with($flash);
    }

    public function edit($encryptedId)
    {
        $data = Product::findOrFail(Encryption::decrypt($encryptedId));

        return view('products.edit', [
            'data'        => $data,
            'title'       => $this->title,
            'route'       => $this->route,
            'encryptedId' => $encryptedId,
            'categories'  => Category::orderBy('name')->get(),
            'units'       => Unit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $formRequest = app()->make(ProductRequest::class);
        $formRequest->setContainer(app())->setRedirector(app()->make('redirect'));
        $formRequest->validateResolved();
        $validatedData = $formRequest->validated();

        if ($request->hasFile('image')) {
            $validatedData['image'] = FileManager::store($request->file('image'), 'products');
        } else {
            unset($validatedData['image']);
        }

        $data = Product::findOrFail(Encryption::decrypt($encryptedId));
        $data->update($validatedData);

        return redirect()->back()->with([
            'success' => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }
}
