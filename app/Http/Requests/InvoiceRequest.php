<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = Route::getCurrentRoute()->getActionMethod();

        return match ($method) {
            'store'  => $this->store(),
            'update' => $this->update(),
            default  => [],
        };
    }

    public function store(): array
    {
        return [
            'customer_id'                  => ['required', 'exists:customers,id'],
            'salesperson_id'               => ['required', 'exists:users,id'],
            'invoice_date'                 => ['required', 'date'],
            'due_date'                     => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'discount_amount'              => ['nullable', 'integer', 'min:0'],
            'tax_amount'                   => ['nullable', 'integer', 'min:0'],
            'status'                       => ['required', Rule::enum(InvoiceStatus::class)],
            'details'                      => ['required', 'array', 'min:1'],
            'details.*.product_id'         => ['required', 'exists:products,id'],
            'details.*.quantity'           => ['required', 'integer', 'min:1'],
            'details.*.unit_price'         => ['required', 'integer', 'min:0'],
            'details.*.discount_amount'    => ['nullable', 'integer', 'min:0'],
            'details.*.tax_amount'         => ['nullable', 'integer', 'min:0'],
            'details.*.amount'             => ['required', 'integer', 'min:0'],
        ];
    }

    public function update(): array
    {
        return $this->store();
    }
}
