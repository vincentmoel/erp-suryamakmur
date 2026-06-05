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
            'category_id'   => 'nullable|exists:categories,id',
            'unit_id'       => 'required|exists:units,id',
            'sku'           => 'nullable|string|unique:products,sku|max:100',
            'name'          => 'required|min:2|max:255',
            'description'   => 'nullable',
            'selling_price' => 'required|integer|min:0',
            'image'         => 'nullable|image|max:2048',
            'stock_minimum' => 'required|integer|min:0',
            'is_active'     => 'boolean',
        ];
    }

    public function update(): array
    {
        $id = \App\Helpers\Encryption::decrypt(request()->route('encryptedId'));

        return array_merge($this->store(), [
            'sku'   => 'nullable|string|unique:products,sku,' . $id . '|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
    }
}
