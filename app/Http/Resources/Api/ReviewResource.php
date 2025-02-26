<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Bill;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="ReviewResource",
 *     type="object",
 *     title="Review",
 *     description="Review resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the review"
 *     ),
 *     @OA\Property(
 *         property="rating_food",
 *         type="integer",
 *         description="Rating for the food",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="rating_delivery",
 *         type="integer",
 *         description="Rating for the delivery",
 *         example=4,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="Comment from the user",
 *         example="The food was delicious and arrived on time."
 *     ),
 *     @OA\Property(
 *         property="restaurant_comment",
 *         type="string",
 *         description="Comment from the restaurant",
 *         example="Thank you for your feedback!",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The ID of the user who left the review"
 *     ),
 *     @OA\Property(
 *         property="user_name",
 *         type="string",
 *         description="The name or email of the user who left the review",
 *         example="John Doe"
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
 *         description="Indicates if the review was edited by the user",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="restaurant_edited",
 *         type="boolean",
 *         description="Indicates if the review was edited by the restaurant",
 *         example=false
 *     )
 * )
 */
class ReviewResource extends ApiResource
{
    use ResourceTrait, MultiTentantRepositoryTrait;

    const int TIME_TO_EDIT = 48;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating_food' => $this->rating_food,
            'rating_delivery' => $this->rating_delivery,
            'comment' => $this->comment,
            'restaurant_comment' => $this->restaurant_comment,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'restaurant_id' => $this->restaurant_id,
            'bill_id' => $this->bill_id,
            'user_edited' => $this->user_edited,
            'restaurant_edited' => $this->restaurant_edited,
            'created_at' => $this->dateFormat($this->created_at),
            'restaurant_comment_created_at' => $this->dateFormat($this->restaurant_comment_created_at),
            'is_editable' => $this->isEditable(),
        ];
    }

    private function isEditable(): bool
    {
        $currentRestaurant = Restaurant::find($this->restaurant_id);
        $this->reconnect($currentRestaurant);

        $releasedAt = Bill::find($this->bill_id)?->released_at;

        if (!$releasedAt) {
            return false;
        }

        $this->reset();

        return Carbon::now()->diffInHours($releasedAt) < self::TIME_TO_EDIT;
    }
}
