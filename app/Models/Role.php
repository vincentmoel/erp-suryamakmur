<?php

namespace App\Models;

use App\Traits\CreatedByUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use CreatedByUpdatedBy;
    
    protected $with = ['user_created_by', 'user_updated_by'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public static function boot()
    {
        parent::boot();
        static::bootCreatedByUpdatedBy();
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'role_id');
    }

    public function permissionsGrouped(): array
    {
        return $this->permissions->groupBy('module')->map(fn($rows) => $rows->pluck('action')->toArray())->toArray();
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
