<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="RestaurantRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of restaurant IDs to filter.")),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by restaurant ID."),
 *     @OA\Property(property="order.createdAt", type="string", enum={"asc", "desc"}, description="Order by restaurant creation date."),
 *     @OA\Property(property="order.updatedAt", type="string", enum={"asc", "desc"}, description="Order by restaurant update date."),
 *     @OA\Property(property="order.name", type="string", enum={"asc", "desc"}, description="Order by restaurant name."),
 *     @OA\Property(property="order.subname", type="string", enum={"asc", "desc"}, description="Order by restaurant subname."),
 *     @OA\Property(property="locale", type="string", example="en", description="Locale for the response."),
 *     description="Request schema for the GET method of RestaurantRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RestaurantRequestPOST",
 *     type="object",
 *     description="Request schema for the POST method of RestaurantRequest. No specific rules are defined yet."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RestaurantRequestPUT",
 *     type="object",
 *     description="Request schema for the PUT method of RestaurantRequest. No specific rules are defined yet."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RestaurantRequestPUT",
 *     type="object",
 *     description="Request schema for the PUT method of RestaurantRequest. No specific rules are defined yet."
 * )
 */

class RestaurantRequest extends FormRequest
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
            'order.createdAt' => 'string|in:asc,desc',
            'order.updatedAt' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
            'order.subname' => 'string|in:asc,desc',
        'locale' => '',
        ],
        self::METHOD_POST => [
        ],
        self::METHOD_PUT => [
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
        $rules = self::$rules[$this->getMethod()] ?? [];

        return $rules;
    }
}
