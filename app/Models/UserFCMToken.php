<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFCMToken extends Model
{
    protected $table = 'users_fcm_tokens';

    protected $fillable = [
        'user_id',
        'token',
    ];
}
