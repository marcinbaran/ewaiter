<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Label extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
    ];

    public $translatable = [
        'name',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    protected $table = 'label';

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_label', 'label_id', 'dish_id');
    }
}
