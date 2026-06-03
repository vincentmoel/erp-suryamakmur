<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $methodName = Route::getCurrentRoute()->getActionMethod();

        if ($methodName == 'store') {
            return $this->store();
        } elseif ($methodName == 'update') {
            return $this->update();
        }

        return [];
    }

    public function store(): array
    {
        return [
            'category_id'     => 'nullable|exists:categories,id',
            'unit_id'         => 'required|exists:units,id',
            'name'            => 'required|min:2|max:255',
            'description'     => 'nullable',
            'sku'             => 'nullable|max:100',
            'image'           => 'nullable|image|max:2048',
            'stock_available' => 'required|integer|min:0',
            'stock_minimum'   => 'required|integer|min:0',
        ];
    }

    public function update(): array
    {
        return [
            'category_id'  => 'nullable|exists:categories,id',
            'unit_id'      => 'required|exists:units,id',
            'name'         => 'required|min:2|max:255',
            'description'  => 'nullable',
            'sku'          => 'nullable|max:100',
            'image'        => 'nullable|image|max:2048',
            'stock_minimum' => 'required|integer|min:0',
        ];
    }
}
