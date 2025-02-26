<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionGroupDish extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additions_groups_dishes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dish_id',
        'addition_group_id',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return BelongsTo
     */
    public function addition_group(): BelongsTo
    {
        return $this->belongsTo(AdditionGroup::class, 'addition_group_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }
}
