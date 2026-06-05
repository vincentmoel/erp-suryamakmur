<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnDetail extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function invoiceDetailBatch(): BelongsTo
    {
        return $this->belongsTo(InvoiceDetailBatch::class);
    }
}
