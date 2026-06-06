<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'context'       => 'array',
        'user_snapshot' => 'array',
    ];

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(InventoryDetail::class);
    }
}
