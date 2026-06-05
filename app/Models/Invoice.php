<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'status'            => InvoiceStatus::class,
        'invoice_date'      => 'date',
        'due_date'          => 'date',
        'customer_snapshot' => 'array',
    ];

    public function getSubtotalAmountAttribute(): int
    {
        if (isset($this->attributes['details_sum_amount'])) {
            return (int) $this->attributes['details_sum_amount'];
        }
        if ($this->relationLoaded('details')) {
            return $this->details->sum('amount');
        }
        return (int) $this->details()->sum('amount');
    }

    public function getAmountAttribute(): int
    {
        return $this->subtotal_amount
            - ($this->discount_amount ?? 0)
            + ($this->tax_amount ?? 0);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function receiptDetails(): HasMany
    {
        return $this->hasMany(ReceiptDetail::class);
    }

    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function invoiceDetailBatches(): HasManyThrough
    {
        return $this->hasManyThrough(InvoiceDetailBatch::class, InvoiceDetail::class);
    }
}
