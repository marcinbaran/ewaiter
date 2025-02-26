<?php

namespace App\Models;

use App\Repositories\MultiTentantRepositoryTrait;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use MultiTentantRepositoryTrait;
    use ModelTrait;
    use UsesSystemConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'ip_address',
        'mac_address',
        'restaurant_id',
        'restaurant_name',
        'user_id',
        'visit_object_type',
        'visit_object_name',
        'visit_object_id',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
