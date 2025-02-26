<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AttributeGroup extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    protected $table = 'attribute_groups';

    protected $fillable = [
        'key',
        'name',
        'description',
        'input_type',
        'is_primary',
        'is_active',
    ];

    public $translatable = ['name', 'description'];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'is_active' => 0,
    ];

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'attribute_group_id', 'id');
    }

    public function scopeActive(Builder $builder): void
    {
        $builder->where('is_active', true);
    }

    public function scopeNotEmpty(Builder $builder): void
    {
        $builder->whereHas('attributes');
    }
}
