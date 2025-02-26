<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="FoodCategoryRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="itemsPerPage",
 *         type="integer",
 *         minimum=1,
 *         maximum=50,
 *         description="Number of items per page",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         minimum=1,
 *         description="Page number for pagination",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Array of category IDs to filter"
 *     ),
 *     @OA\Property(
 *         property="order.id",
 *         type="string",
 *         enum={"asc", "desc"},
 *         description="Order by ID"
 *     ),
 *     @OA\Property(
 *         property="order.name",
 *         type="string",
 *         enum={"asc", "desc"},
 *         description="Order by name"
 *     ),
 *     @OA\Property(
 *         property="order.position",
 *         type="string",
 *         enum={"asc", "desc"},
 *         description="Order by position"
 *     ),
 *     @OA\Property(
 *         property="parent",
 *         type="integer",
 *         minimum=0,
 *         description="Parent category ID"
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="integer",
 *         minimum=0,
 *         description="Visibility status"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FoodCategoryRequestPOST",
 *     required={"name"},
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category",
 *         maxLength=255,
 *         example="Main Dishes"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the category",
 *         example="A variety of main dishes"
 *     ),
 *     @OA\Property(
 *         property="name_en",
 *         type="string",
 *         description="English name of the category",
 *         maxLength=255,
 *         example="Main Dishes"
 *     ),
 *     @OA\Property(
 *         property="description_en",
 *         type="string",
 *         description="English description of the category",
 *         example="A variety of main dishes"
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         format="binary",
 *         description="Photo of the category"
 *     ),
 *     @OA\Property(
 *         property="parent.id",
 *         type="integer",
 *         description="Parent category ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the category",
 *         example=1
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FoodCategoryRequestPut",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category",
 *         maxLength=255,
 *         example="Main Dishes"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the category",
 *         example="A variety of main dishes"
 *     ),
 *     @OA\Property(
 *         property="name_en",
 *         type="string",
 *         description="English name of the category",
 *         maxLength=255,
 *         example="Main Dishes"
 *     ),
 *     @OA\Property(
 *         property="description_en",
 *         type="string",
 *         description="English description of the category",
 *         example="A variety of main dishes"
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         format="binary",
 *         description="Photo of the category"
 *     ),
 *     @OA\Property(
 *         property="removePhoto.id",
 *         type="integer",
 *         description="ID of the photo to remove",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="parent.id",
 *         type="integer",
 *         description="Parent category ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the category",
 *         example=1
 *     )
 * )
 */
class FoodCategoryRequest extends FormRequest
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
            'order.name' => 'string|in:asc,desc',
            'order.position' => 'string|in:asc,desc',
            'parent' => 'integer|min:0',
            'visibility' => 'integer|min:0',
        ],
        self::METHOD_POST => [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'name_en' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=34,min_height=34,max_width=320,max_height=320',
            'parent.id' => 'integer|exists:tenant.food_categories,id',
            'position' => 'nullable|integer',
        ],
        self::METHOD_PUT => [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'name_en' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=34,min_height=34,max_width=320,max_height=320',
            'removePhoto.id' => 'integer|exists:tenant.resources,id',
            'parent.id' => 'integer|exists:tenant.food_categories,id',
            'position' => 'nullable|integer',
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
