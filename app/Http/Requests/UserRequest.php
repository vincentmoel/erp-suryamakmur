<?php

namespace App\Http\Requests;

use App\Helpers\Encryption;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
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
        $roleIds = Role::pluck('id');
        return [
            "name"      => 'required|min:2|max:255',
            "username"  => 'required|min:2|max:255|unique:users,username',
            "password"  => 'required|min:8|confirmed',
            "roles"     => 'required|array',
            "roles.*"   => [
                Rule::in($roleIds)
            ]
        ];
    }

    public function update()
    {
        $encryptedId = Route::current()->parameter('encryptedId');
        $id = Encryption::decrypt($encryptedId);
        $roleIds = Role::pluck('id');

        return [
            "name"      => 'required|min:2|max:255',
            'username'  => [
                'required',
                'min:2',
                'max:255',
                Rule::unique('users')->ignore($id, 'id')
            ],
            'password'  => 'nullable|min:8|confirmed',
            "roles"     => 'sometimes|array',
            "roles.*"   => [
                Rule::in($roleIds)
            ]
        ];
    }
}
