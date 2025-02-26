<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DishAttribute extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    protected $table = 'dish_attribute';

    protected $fillable = [
        'dish_id',
        'attribute_id',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }
}
