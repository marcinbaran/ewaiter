<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @OA\Schema(
 *     schema="AdditionDish",
 *     type="object",
 *     title="AdditionDish",
 *     description="Model representing the relationship between Additions and Dishes",
 *     @OA\Property(
 *         property="dish_id",
 *         type="integer",
 *         description="The ID of the associated dish",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="addition_id",
 *         type="integer",
 *         description="The ID of the associated addition",
 *         example=1
 *     )
 * )
 */
class AdditionDish extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additions_dishes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dish_id',
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
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }
}
