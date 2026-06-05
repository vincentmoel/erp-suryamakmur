<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptDetail extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
