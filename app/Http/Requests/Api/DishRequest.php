<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="DishRequestGet",
 *     type="object",
 *     title="Dish Request (GET)",
 *     description="Request payload for retrieving dishes",
 *     @OA\Property(property="itemsPerPage", type="integer", description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", description="Current page number"),
 *     @OA\Property(property="category", type="array", @OA\Items(type="integer"), description="Array of category IDs"),
 *     @OA\Property(property="strictCategory", type="boolean", description="Flag to enable strict category filtering"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="Array of dish IDs"),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by ID"),
 *     @OA\Property(property="order.name", type="string", enum={"asc", "desc"}, description="Order by name"),
 *     @OA\Property(property="order.price", type="string", enum={"asc", "desc"}, description="Order by price"),
 *     @OA\Property(property="order.discount", type="string", enum={"asc", "desc"}, description="Order by discount"),
 *     @OA\Property(property="order.timeWait", type="string", enum={"asc", "desc"}, description="Order by time wait"),
 *     @OA\Property(property="order.position", type="string", enum={"asc", "desc"}, description="Order by position"),
 *     @OA\Property(property="withAdditions", type="boolean", description="Include additions in response")
 * )
 *
 * @OA\Schema(
 *     schema="DishRequestPost",
 *     type="object",
 *     title="Dish Request (POST)",
 *     description="Request payload for creating a dish",
 *     @OA\Property(property="name", type="string", description="Name of the dish", maxLength=255),
 *     @OA\Property(property="description", type="string", description="Description of the dish"),
 *     @OA\Property(property="name_en", type="string", description="English name of the dish", maxLength=255),
 *     @OA\Property(property="description_en", type="string", description="English description of the dish"),
 *     @OA\Property(property="category.id", type="integer", description="Category ID"),
 *     @OA\Property(
 *         property="photos",
 *         type="array",
 *         @OA\Items(type="file"),
 *         description="Array of dish photos"
 *     ),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the dish"),
 *     @OA\Property(property="tax", type="number", format="float", description="Tax percentage"),
 *     @OA\Property(property="discount", type="number", format="float", description="Discount percentage"),
 *     @OA\Property(property="visibility", type="boolean", description="Visibility status of the dish"),
 *     @OA\Property(property="timeWait", type="integer", description="Estimated wait time in minutes"),
 *     @OA\Property(
 *         property="additions_groups",
 *         type="array",
 *         @OA\Items(type="object", @OA\Property(property="id", type="integer")),
 *         description="Array of addition groups"
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(type="object", @OA\Property(property="id", type="integer")),
 *         description="Array of tag IDs"
 *     ),
 *     @OA\Property(property="position", type="integer", description="Position of the dish in the menu")
 * )
 *
 * @OA\Schema(
 *     schema="DishRequestPut",
 *     type="object",
 *     title="Dish Request (PUT)",
 *     description="Request payload for updating a dish",
 *     @OA\Property(property="name", type="string", description="Name of the dish", maxLength=255),
 *     @OA\Property(property="description", type="string", description="Description of the dish"),
 *     @OA\Property(property="name_en", type="string", description="English name of the dish", maxLength=255),
 *     @OA\Property(property="description_en", type="string", description="English description of the dish"),
 *     @OA\Property(property="category.id", type="integer", description="Category ID"),
 *     @OA\Property(
 *         property="photos",
 *         type="array",
 *         @OA\Items(type="file"),
 *         description="Array of dish photos"
 *     ),
 *     @OA\Property(property="removePhotos", type="array", @OA\Items(type="integer"), description="Array of photo IDs to remove"),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the dish"),
 *     @OA\Property(property="tax", type="number", format="float", description="Tax percentage"),
 *     @OA\Property(property="discount", type="number", format="float", description="Discount percentage"),
 *     @OA\Property(property="visibility", type="boolean", description="Visibility status of the dish"),
 *     @OA\Property(property="timeWait", type="integer", description="Estimated wait time in minutes"),
 *     @OA\Property(
 *         property="additions_groups",
 *         type="array",
 *         @OA\Items(type="object", @OA\Property(property="id", type="integer")),
 *         description="Array of addition groups"
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(type="object", @OA\Property(property="id", type="integer")),
 *         description="Array of tag IDs"
 *     ),
 *     @OA\Property(property="position", type="integer", description="Position of the dish in the menu")
 * )
 *
 * @OA\Schema(
 *     schema="DishRequestDelete",
 *     type="object",
 *     title="Dish Request (DELETE)",
 *     description="Request payload for deleting a dish"
 * )
 */
class DishRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'category' => 'array|min:1',
            'category.*' => 'integer|min:1',
            'strictCategory' => 'boolean',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
            'order.price' => 'string|in:asc,desc',
            'order.discount' => 'string|in:asc,desc',
            'order.timeWait' => 'string|in:asc,desc',
            'order.position' => 'string|in:asc,desc',
            'withAdditions' => 'boolean',
        ],
        self::METHOD_POST => [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'name_en' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'category.id' => 'required|integer|exists:tenant.food_categories,id',
            'photos' => 'array|min:1|max:6',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=320,min_height=320,max_width=1024,max_height=1024',
            'price' => 'required|numeric|min:0.01',
//            'tags' => 'array|min:0',
//            'tags.*' => 'string|in:SPICY,VEGETARIAN,VEGAN,GLUTEN_FREE,LACTOSE_FREE,WINE_RED,WINE_ROSE,WINE_WHITE,WINE_SPARKLING,WINE_DRY,WINE_SEMI_DRY,WINE_SEMI_SWEET,WINE_SWEET',
            'tax' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'visibility' => 'boolean',
            'timeWait' => 'integer|min:1',
            'additions_groups.*.id' => 'nullable|exists:tenant.additions_groups,id',
            'additions_groups' => 'nullable|exists:tenant.additions_groups,id',
            'tags.*.id' => 'nullable|exists:tenant.tags,id',
            'tags' => 'nullable|exists:tenant.tags,id',
            'position' => 'nullable|integer',
        ],
        self::METHOD_PUT => [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'name_en' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'category.id' => 'integer|exists:tenant.food_categories,id',
            'photos' => 'array|min:1|max:6',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=320,min_height=320,max_width=1024,max_height=1024',
            'removePhotos' => 'array|min:0|max:3',
            'removePhotos.*.id' => 'integer|exists:tenant.resources,id',
            'price' => 'numeric|min:0.01',
//            'tags' => 'array|min:0',
//            'tags.*' => 'string|in:SPICY,VEGETARIAN,VEGAN,GLUTEN_FREE,LACTOSE_FREE,WINE_RED,WINE_ROSE,WINE_WHITE,WINE_SPARKLING,WINE_DRY,WINE_SEMI_DRY,WINE_SEMI_SWEET,WINE_SWEET',
            'tax' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'visibility' => 'boolean',
            'timeWait' => 'integer|min:1',
            'additions_groups.*.id' => 'nullable|exists:tenant.additions_groups,id',
            'additions_groups' => 'nullable|exists:tenant.additions_groups,id',
            'tags.*.id' => 'nullable|exists:tenant.tags,id',
            'tags' => 'nullable|exists:tenant.tags,id',
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
        $rules = self::$rules[$this->getMethod()] ?? [];
        if (empty($rules) || ! in_array($this->getMethod(), [self::METHOD_POST, self::METHOD_PUT])) {
            return $rules;
        }
        $additionRules = AdditionRequest::getRule(self::METHOD_PUT);
        unset($additionRules['dish.id']);
        if (self::METHOD_PUT == $this->getMethod()) {
            $additionRules['id'] = 'integer|min:1|exists:additions,id';
        }
        $rules['additions'] = 'array|min:1';
        foreach ($additionRules as $k => $oRule) {
            $rules['additions.*.'.$k] = $oRule;
        }
    }
}
