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
        $method = Route::getCurrentRoute()?->getActionMethod() ?? 'store';

        return match ($method) {
            'store'  => $this->store(),
            'update' => $this->update(),
            default  => [],
        };
    }

    public function attributes(): array
    {
        return [
            'customer_id'                => __('general.customer'),
            'salesperson_id'             => __('general.salesperson'),
            'invoice_date'               => __('general.invoice_date'),
            'due_date'                   => __('general.due_date'),
            'discount_percent'           => __('general.discount'),
            'discount_amount'            => __('general.discount'),
            'tax_percent'                => __('general.tax'),
            'tax_amount'                 => __('general.tax'),
            'notes'                      => __('general.notes'),
            'details'                    => __('general.items'),
            'details.*.product_id'       => __('general.product'),
            'details.*.quantity'         => __('general.qty'),
            'details.*.unit_price'       => __('general.unit_price'),
            'details.*.discount_percent' => __('general.discount'),
            'details.*.discount_amount'  => __('general.discount'),
            'details.*.tax_percent'      => __('general.tax'),
            'details.*.tax_amount'       => __('general.tax'),
            'details.*.amount'           => __('general.amount'),
        ];
    }

    public function store(): array
    {
        return [
            'customer_id'                  => ['required', 'exists:customers,id'],
            'salesperson_id'               => ['required', 'exists:users,id'],
            'invoice_date'                 => ['required', 'date'],
            'due_date'                     => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'discount_percent'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount'              => ['nullable', 'integer', 'min:0'],
            'tax_percent'                  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_amount'                   => ['nullable', 'integer', 'min:0'],
            'status'                       => ['required', Rule::enum(InvoiceStatus::class)],
            'details'                      => ['required', 'array', 'min:1'],
            'details.*.product_id'         => ['required', 'exists:products,id'],
            'details.*.quantity'           => ['required', 'integer', 'min:1'],
            'details.*.unit_price'         => ['required', 'integer', 'min:0'],
            'details.*.discount_percent'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'details.*.discount_amount'    => ['nullable', 'integer', 'min:0'],
            'details.*.tax_percent'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'details.*.tax_amount'         => ['nullable', 'integer', 'min:0'],
            'details.*.subtotal_amount'    => ['nullable', 'integer', 'min:0'],
            'details.*.amount'             => ['required', 'integer', 'min:0'],
            'notes'                        => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function update(): array
    {
        return $this->store();
    }
}
