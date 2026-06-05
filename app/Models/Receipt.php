<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Helpers\CodeGenerator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'receipt_date'   => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Receipt $receipt) {
            if (empty($receipt->code)) {
                $receipt->code = CodeGenerator::receipt();
            }
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReceiptDetail::class);
    }

    public function getAmountTotalAttribute(): int
    {
        if ($this->relationLoaded('details')) {
            return $this->details->sum('amount');
        }
        return (int) $this->details()->sum('amount');
    }
}
