<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceDetail extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'product_snapshot' => 'array',
    ];

    public function getSubtotalAmountAttribute(): int
    {
        return $this->quantity * $this->unit_price;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InvoiceDetailBatch::class);
    }
}
