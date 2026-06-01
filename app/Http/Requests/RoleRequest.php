<?php

namespace App\Http\Requests;

use App\Helpers\Encryption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        } else if ($methodName == 'update') {
            return $this->update();
        }
    }

    public function store()
    {
        return [
            "name"            => 'required|unique:roles,name|min:2|max:255',
            'permission'      => 'nullable|array',
            'permission.*.*'  => 'nullable',
        ];
    }

    public function update()
    {
        $encryptedId = Route::current()->parameter('encryptedId');
        $id = Encryption::decrypt($encryptedId);

        return [
            "name"            => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('roles', 'name')->ignore($id, 'id')
            ],
            'permission'      => 'nullable|array',
            'permission.*.*'  => 'nullable',
        ];
    }
}
