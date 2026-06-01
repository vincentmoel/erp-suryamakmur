<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends BaseModel
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
}