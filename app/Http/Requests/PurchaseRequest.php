<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
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
            'vendor_id'                   => ['required', 'exists:vendors,id'],
            'invoice_number'              => ['nullable', 'string', 'max:100'],
            'purchase_date'               => ['required', 'date'],
            'discount_amount'             => ['nullable', 'integer', 'min:0'],
            'tax_percent'                 => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'                       => ['nullable', 'string'],
            'status'                      => ['required', Rule::in(['draft', 'ordered'])],
            'details'                     => ['required', 'array', 'min:1'],
            'details.*.product_id'        => ['required', 'exists:products,id'],
            'details.*.quantity'          => ['required', 'integer', 'min:1'],
            'details.*.unit_price'        => ['required', 'integer', 'min:0'],
            'details.*.discount_amount'   => ['nullable', 'integer', 'min:0'],
            'details.*.subtotal'          => ['required', 'integer', 'min:0'],
        ];
    }

    public function update(): array
    {
        return $this->store();
    }
}
