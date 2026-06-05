<?php

namespace App\Http\Requests;

use App\Enums\VendorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
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
            'code'                => 'nullable|string|max:50|unique:vendors,code',
            'type'                => ['required', Rule::enum(VendorType::class)],
            'name'                => 'required|min:2|max:255',
            'tax_number'          => 'nullable|max:30',
            'phone'               => 'nullable|max:50',
            'email'               => 'nullable|email|max:255',
            'contact_person'      => 'nullable|max:255',
            'address'             => 'nullable',
            'city'                => 'nullable|max:100',
            'province'            => 'nullable|max:100',
            'postal_code'         => 'nullable|max:10',
            'bank_name'           => 'nullable|max:100',
            'bank_account_number' => 'nullable|max:50',
            'bank_account_name'   => 'nullable|max:255',
            'notes'               => 'nullable',
            'is_active'           => 'boolean',
        ];
    }

    public function update(): array
    {
        $id = \App\Helpers\Encryption::decrypt(request()->route('encryptedId'));

        return array_merge($this->store(), [
            'code' => 'nullable|string|max:50|unique:vendors,code,' . $id,
        ]);
    }
}
