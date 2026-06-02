<?php

namespace App\Http\Requests;

use App\Helpers\Encryption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
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
            'name' => 'required|min:2|max:255|unique:units,name',
        ];
    }

    public function update(): array
    {
        $encryptedId = Route::current()->parameter('encryptedId');
        $id = Encryption::decrypt($encryptedId);

        return [
            'name' => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('units', 'name')->ignore($id, 'id'),
            ],
        ];
    }
}
