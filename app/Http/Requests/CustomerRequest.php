<?php

namespace App\Http\Requests;

use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
            'type'         => ['required', Rule::enum(CustomerType::class)],
            'name'         => 'required|min:2|max:255',
            'company_name' => 'nullable|max:255',
            'tax_number'   => 'nullable|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|max:50',
            'mobile'       => 'nullable|max:50',
            'notes'        => 'nullable',
            'is_active'    => 'boolean',
        ];
    }

    public function update(): array
    {
        return $this->store();
    }
}
