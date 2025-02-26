<?php

namespace App\Models;

use App\Repositories\MultiTentantRepositoryTrait;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use MultiTentantRepositoryTrait;
    use ModelTrait;
    use UsesSystemConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'restaurant_id',
        'restaurant_name',
        'bill_id',
        'bill_price',
        'commission',
        'status',
        'comment',
        'issued_at',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
