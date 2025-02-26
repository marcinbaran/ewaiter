<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     title="Review",
 *     description="Model representing a review",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the review"
 *     ),
 *     @OA\Property(
 *         property="rating_food",
 *         type="integer",
 *         description="Rating for the food"
 *     ),
 *     @OA\Property(
 *         property="rating_delivery",
 *         type="integer",
 *         description="Rating for the delivery"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="Comment from the user"
 *     ),
 *     @OA\Property(
 *         property="restaurant_comment",
 *         type="string",
 *         description="Comment from the restaurant"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The ID of the user who made the review"
 *     ),
 *     @OA\Property(
 *         property="restaurant_id",
 *         type="integer",
 *         description="The ID of the restaurant being reviewed"
 *     ),
 *     @OA\Property(
 *         property="bill_id",
 *         type="integer",
 *         description="The ID of the related bill"
 *     ),
 *     @OA\Property(
 *         property="user_edited",
 *         type="boolean",
 *         description="Flag indicating if the user edited the review"
 *     ),
 *     @OA\Property(
 *         property="restaurant_edited",
 *         type="boolean",
 *         description="Flag indicating if the restaurant edited the review"
 *     )
 * )
 */

class Review extends Model
{
    use ModelTrait, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'rating_food',
        'rating_delivery',
        'comment',
        'restaurant_comment',
        'user_id',
        'restaurant_id',
        'bill_id',
        'user_edited',
        'restaurant_edited',
        'user_name',
    ];

    /**
     * @var array
     */
    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /**
     * Define the relationship with the Restaurant model
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
