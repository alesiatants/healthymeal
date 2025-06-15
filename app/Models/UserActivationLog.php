<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivationLog extends Model
{
    protected $fillable = [
        'user_id',
        'action_by',
        'action'
    ];
}
