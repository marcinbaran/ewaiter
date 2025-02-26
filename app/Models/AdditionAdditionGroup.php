<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionAdditionGroup extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additions_additions_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addition_group_id',
        'addition_id',
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
    public function addition(): BelongsTo
    {
        return $this->belongsTo(Addition::class, 'addition_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function addition_group(): BelongsTo
    {
        return $this->belongsTo(AdditionGroup::class, 'addition_group_id', 'id');
    }
}
