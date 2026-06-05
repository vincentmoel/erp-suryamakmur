<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryDetail extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function invoiceDetailBatches(): HasMany
    {
        return $this->hasMany(InvoiceDetailBatch::class);
    }
}
