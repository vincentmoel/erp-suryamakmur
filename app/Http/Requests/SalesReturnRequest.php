<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'                          => ['required', 'exists:invoices,id'],
            'return_date'                         => ['required', 'date'],
            'notes'                               => ['nullable', 'string'],
            'details'                             => ['required', 'array', 'min:1'],
            'details.*.invoice_detail_batch_id'   => ['required', 'exists:invoice_detail_batches,id'],
            'details.*.quantity'                  => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Please select an invoice and enter at least one return quantity.',
            'details.min'      => 'Please enter a return quantity for at least one batch.',
        ];
    }
}
