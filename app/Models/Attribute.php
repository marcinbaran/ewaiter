<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    protected $fillable = [
        'key',
        'name',
        'icon',
        'description',
        'is_active',
        'attribute_group_id',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_attribute', 'attribute_id', 'dish_id');
    }

    public function attributeGroup(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class, 'attribute_group_id', 'id');
    }

    public function scopeActive(Builder $builder): void
    {
        $builder->where('is_active', true);
    }
}
