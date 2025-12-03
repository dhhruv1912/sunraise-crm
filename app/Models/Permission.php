<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'role',
        'name',
        'value',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
