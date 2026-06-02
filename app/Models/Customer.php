<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'type' => CustomerType::class,
    ];
}
