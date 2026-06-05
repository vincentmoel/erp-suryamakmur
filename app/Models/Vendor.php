<?php

namespace App\Models;

use App\Enums\VendorType;
use App\Helpers\CodeGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'type'      => VendorType::class,
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Vendor $vendor) {
            if (empty($vendor->code)) {
                $vendor->code = CodeGenerator::vendor();
            }
        });
    }
}
