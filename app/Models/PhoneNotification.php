<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class PhoneNotification extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    protected $table = 'phone_notification';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'counter',
        'phone',
        'sender_number',
        'content',
        'object_id',
        'object',
        'response',
        'send_at',
        'active',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
