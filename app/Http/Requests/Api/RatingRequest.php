<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="RatingRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of rating IDs to filter.")),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by rating ID."),
 *     @OA\Property(property="order.restaurant_name", type="string", enum={"asc", "desc"}, description="Order by restaurant name."),
 *     @OA\Property(property="order.rating", type="string", enum={"asc", "desc"}, description="Order by rating value."),
 *     description="Request schema for the GET method of RatingRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RatingRequestPOST",
 *     type="object",
 *     @OA\Property(property="bill_id", type="integer", example=1, description="ID of the bill associated with the rating."),
 *     @OA\Property(property="restaurant_id", type="integer", example=1, description="ID of the restaurant being rated."),
 *     @OA\Property(property="comment", type="string", example="Great food!", description="Comment about the food."),
 *     @OA\Property(property="restaurant_comment", type="string", example="Friendly staff.", description="Comment about the restaurant service."),
 *     @OA\Property(property="r_food", type="integer", example=4, description="Rating for the food (1-5)."),
 *     @OA\Property(property="r_delivery", type="integer", example=5, description="Rating for the delivery (1-5)."),
 *     @OA\Property(property="anonymous", type="boolean", example=false, description="Whether the rating is anonymous."),
 *     @OA\Property(property="visibility", type="boolean", example=true, description="Whether the rating is visible to others."),
 *     description="Request schema for the POST method of RatingRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RatingRequestPUT",
 *     type="object",
 *     @OA\Property(property="bill_id", type="integer", example=1, description="ID of the bill associated with the rating."),
 *     @OA\Property(property="restaurant_id", type="integer", example=1, description="ID of the restaurant being rated."),
 *     @OA\Property(property="comment", type="string", example="Great food!", description="Comment about the food."),
 *     @OA\Property(property="restaurant_comment", type="string", example="Friendly staff.", description="Comment about the restaurant service."),
 *     @OA\Property(property="r_food", type="integer", example=4, description="Rating for the food (1-5)."),
 *     @OA\Property(property="r_delivery", type="integer", example=5, description="Rating for the delivery (1-5)."),
 *     @OA\Property(property="anonymous", type="boolean", example=false, description="Whether the rating is anonymous."),
 *     @OA\Property(property="visibility", type="boolean", example=true, description="Whether the rating is visible to others."),
 *     description="Request schema for the PUT method of RatingRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RatingRequestDELETE",
 *     type="object",
 *     description="Request schema for the DELETE method of RatingRequest. Typically used for deleting ratings, usually without a body."
 * )
 */

class RatingRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'order.id' => 'string|in:asc,desc',
            'order.restaurant_name' => 'string|in:asc,desc',
            'order.rating' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'bill_id' => 'required|integer',
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'comment' => 'nullable|string',
            'restaurant_comment' => 'nullable|string',
            'r_food' => 'nullable|integer|between:1,5',
            'r_delivery' => 'nullable|integer|between:1,5',
            'anonymous' => 'boolean',
            'visibility' => 'boolean',
        ],
        self::METHOD_PUT => [
            'bill_id' => 'required|integer',
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'comment' => 'nullable|string',
            'restaurant_comment' => 'nullable|string',
            'r_food' => 'nullable|integer|between:1,5',
            'r_delivery' => 'nullable|integer|between:1,5',
            'anonymous' => 'boolean',
            'visibility' => 'boolean',
        ],
        self::METHOD_DELETE => [
        ],
    ];

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
        return self::$rules[$this->getMethod()] ?? [];
    }
}
