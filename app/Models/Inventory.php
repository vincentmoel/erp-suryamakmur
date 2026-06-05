<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends BaseModel
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(InventoryDetail::class);
    }

    public function getTotalQuantityAttribute(): int
    {
        if ($this->relationLoaded('details')) {
            return $this->details->sum('quantity');
        }
        return (int) $this->details()->sum('quantity');
    }
}
