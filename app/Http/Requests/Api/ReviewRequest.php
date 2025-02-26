<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use App\Rules\CommentTooLongRule;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="ReviewRequest_POST",
 *     type="object",
 *     title="Review Request - Create",
 *     description="Request body for creating a review",
 *     required={"restaurant_id", "bill_id", "rating_food"},
 *     @OA\Property(property="restaurant_id", type="integer", description="The ID of the restaurant being reviewed", example=123),
 *     @OA\Property(property="bill_id", type="integer", description="The ID of the related bill", example=456),
 *     @OA\Property(property="rating_food", type="integer", description="Rating for the food", minimum=1, maximum=5, example=4),
 *     @OA\Property(property="rating_delivery", type="integer", description="Rating for the delivery", minimum=1, maximum=5, nullable=true, example=5),
 *     @OA\Property(property="comment", type="string", description="Comment from the user", maxLength=1000, nullable=true, example="The food was delicious!"),
 *     @OA\Property(property="restaurant_comment", type="string", description="Comment from the restaurant", maxLength=1000, nullable=true, example="Thank you for your feedback!")
 * )
 *
 * @OA\Schema(
 *     schema="ReviewRequest_PUT",
 *     type="object",
 *     title="Review Request - Update",
 *     description="Request body for updating a review",
 *     required={"id", "rating_food"},
 *     @OA\Property(property="id", type="integer", description="The ID of the review", example=1),
 *     @OA\Property(property="rating_food", type="integer", description="Rating for the food", minimum=1, maximum=5, example=4),
 *     @OA\Property(property="rating_delivery", type="integer", description="Rating for the delivery", minimum=1, maximum=5, nullable=true, example=5),
 *     @OA\Property(property="comment", type="string", description="Comment from the user", maxLength=1000, nullable=true, example="The food was delicious!"),
 *     @OA\Property(property="restaurant_comment", type="string", description="Comment from the restaurant", maxLength=1000, nullable=true, example="Thank you for your feedback!")
 * )
 *
 * @OA\Schema(
 *     schema="ReviewRequest_DELETE",
 *     type="object",
 *     title="Review Request - Delete",
 *     description="Request body for deleting a review",
 *     required={"id"},
 *     @OA\Property(property="id", type="integer", description="The ID of the review to be deleted", example=1)
 * )
 *
 * @OA\Schema(
 *     schema="ReviewRequest_GET",
 *     type="object",
 *     title="Review Request - Retrieve",
 *     description="Request parameters for retrieving reviews",
 *     @OA\Property(property="id", type="integer", description="The ID of the review", example=1),
 *     @OA\Property(property="restaurant_id", type="integer", description="The ID of the restaurant being reviewed", example=123),
 *     @OA\Property(property="bill_id", type="integer", description="The ID of the related bill", example=456)
 * )
 */
class ReviewRequest extends FormRequest
{
    use RequestTrait;

    public const string ID_KEY = 'id';
    public const string RATING_FOOD_KEY = 'rating_food';
    public const string RATING_DELIVERY_KEY = 'rating_delivery';
    public const string COMMENT_KEY = 'comment';
    public const string RESTAURANT_COMMENT = 'restaurant_comment';
    public const string RESTAURANT_ID_KEY = 'restaurant_id';
    public const string BILL_ID_KEY = 'bill_id';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            self::METHOD_GET => [
                self::ID_KEY => 'nullable|integer',
                self::RESTAURANT_ID_KEY => 'nullable|integer',
                self::BILL_ID_KEY => 'nullable|integer',
            ],
            self::METHOD_POST => [
                self::RESTAURANT_ID_KEY => 'required|integer',
                self::BILL_ID_KEY => 'required|integer',
                self::RATING_FOOD_KEY => 'required|integer|between:1,5',
                self::RATING_DELIVERY_KEY => 'nullable|integer|between:1,5',
                self::COMMENT_KEY => ['nullable', 'string', new CommentTooLongRule()],
                self::RESTAURANT_COMMENT => 'nullable|string|max:1000',
            ],
            self::METHOD_PUT => [
                self::ID_KEY => 'required|integer',
                self::RATING_FOOD_KEY => 'required|integer|between:1,5',
                self::RATING_DELIVERY_KEY => 'nullable|integer|between:1,5',
                self::COMMENT_KEY => ['nullable', 'string', new CommentTooLongRule()],
                self::RESTAURANT_COMMENT => 'nullable|string|max:1000',
            ],
            self::METHOD_PATCH => [
                self::RESTAURANT_COMMENT => 'nullable|string',
            ],
            self::METHOD_DELETE => [
                self::ID_KEY => 'required|integer',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
