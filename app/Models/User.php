<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'birthdate'     => 'date'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->password = Hash::make($model->password);

            $model->created_by = auth()->user()->id ?? 1;
            $model->updated_by = auth()->user()->id ?? 1;
        });
        
        self::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? 1;

            if ($model->password === null) {
                unset($model->password);
            }
            
            if ($model->isDirty('password')) {
                $model->password = Hash::make($model->password);
            }
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('role_id');
    }

    public function user_created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
