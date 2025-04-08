<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionRerunLog extends Model
{
    protected $fillable = [
        'session_id',
        'externalId',
        'status',
        'error_reason',
        'created_at'
    ];
}
