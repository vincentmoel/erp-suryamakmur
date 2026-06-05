<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceDetailBatch extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function invoiceDetail(): BelongsTo
    {
        return $this->belongsTo(InvoiceDetail::class);
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(InventoryDetail::class);
    }

    public function salesReturnDetails(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class);
    }
}
