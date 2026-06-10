<?php

namespace App\Models;

use App\Enums\BillStatus;
use App\Helpers\CodeGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends BaseModel
{
    use SoftDeletes;

    protected $table = 'bills';

    protected $casts = [
        'status'    => BillStatus::class,
        'bill_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Bill $bill) {
            if (empty($bill->code)) {
                $bill->code = CodeGenerator::bill();
            }
        });
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BillDetail::class);
    }
}
