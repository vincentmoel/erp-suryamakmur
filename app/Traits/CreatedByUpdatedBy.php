<?php

namespace App\Traits;

use App\Models\User;

trait CreatedByUpdatedBy
{    
    public function user_created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function bootCreatedByUpdatedBy()
    {
        self::creating(function($model){
            $model->created_by = auth()->user()->id;
            $model->updated_by = auth()->user()->id;
        });
        
        self::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? 1;
        });
    }
}