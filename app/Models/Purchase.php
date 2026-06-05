<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use App\Helpers\CodeGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'status'        => PurchaseStatus::class,
        'purchase_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase) {
            if (empty($purchase->code)) {
                $purchase->code = CodeGenerator::purchase();
            }
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
