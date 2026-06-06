<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function store(): array
    {
        return $this->baseRules() + [
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function update(): array
    {
        return $this->baseRules() + [
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'POST'          => $this->store(),
            'PATCH', 'PUT'  => $this->update(),
            default         => $this->baseRules(),
        };
    }

    private function baseRules(): array
    {
        return [
            'customer_id'              => ['required', 'exists:customers,id'],
            'receipt_date'             => ['required', 'date'],
            'payment_method'           => ['required', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'reference_number'         => ['nullable', 'string', 'max:255'],
            'notes'                    => ['nullable', 'string'],
            'allocations'              => ['required', 'array', 'min:1'],
            'allocations.*.invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'allocations.*.amount'     => ['required', 'integer', 'min:1'],
        ];
    }
}
