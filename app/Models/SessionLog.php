<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    protected $table = 'session_logs';

    protected $fillable = [
        'staffId',
        'location',
        'message',
        'lendmark',
        'device',
        'ip',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
