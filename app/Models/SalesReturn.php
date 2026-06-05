<?php

namespace App\Models;

use App\Helpers\CodeGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'return_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (SalesReturn $salesReturn) {
            if (empty($salesReturn->code)) {
                $salesReturn->code = CodeGenerator::salesReturn();
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SalesReturnDetail::class);
    }
}
