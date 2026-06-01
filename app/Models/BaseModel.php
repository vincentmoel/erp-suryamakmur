<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedBy;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use CreatedByUpdatedBy;
    
    protected $with = ['user_created_by', 'user_updated_by'];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public static function boot()
    {
        parent::boot();
        static::bootCreatedByUpdatedBy();
    }
}

