<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="PromotionRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of promotion IDs to filter.")),
 *     @OA\Property(property="orderDish", type="array", @OA\Items(type="integer", example=1, description="Array of dish IDs for orders.")),
 *     @OA\Property(property="giftDish", type="array", @OA\Items(type="integer", example=1, description="Array of dish IDs for gifts.")),
 *     @OA\Property(property="orderCategory", type="array", @OA\Items(type="integer", example=1, description="Array of food category IDs for orders.")),
 *     @OA\Property(property="type", type="integer", example=1, description="Promotion type (0-3)."),
 *     @OA\Property(property="typeValue", type="integer", example=1, description="Value associated with the promotion type (0-1)."),
 *     @OA\Property(property="merge", type="boolean", example=true, description="Whether promotions should be merged."),
 *     @OA\Property(property="active", type="boolean", example=true, description="Whether the promotion is active."),
 *     @OA\Property(property="order", type="object",
 *         @OA\Property(property="id", type="string", enum={"asc", "desc"}, description="Order by promotion ID."),
 *         @OA\Property(property="createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date."),
 *         @OA\Property(property="startAt", type="string", enum={"asc", "desc"}, description="Order by start date."),
 *         @OA\Property(property="endAt", type="string", enum={"asc", "desc"}, description="Order by end date.")
 *     ),
 *     description="Request schema for the GET method of PromotionRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="PromotionRequestPOST",
 *     type="object",
 *     @OA\Property(property="type", type="integer", example=1, description="Promotion type (0-2)."),
 *     @OA\Property(property="typeValue", type="integer", example=1, description="Value associated with the promotion type (0-1)."),
 *     @OA\Property(property="value", type="number", format="float", example=10.00, description="Value of the promotion."),
 *     @OA\Property(property="orderDish.id", type="integer", example=1, description="ID of the dish in the order."),
 *     @OA\Property(property="orderCategory.id", type="integer", example=1, description="ID of the food category in the order."),
 *     @OA\Property(property="giftDish.id", type="integer", example=1, description="ID of the gift dish."),
 *     @OA\Property(property="minQuantityOrderDish", type="integer", example=1, description="Minimum quantity of the order dish."),
 *     @OA\Property(property="minPriceBill", type="integer", example=50, description="Minimum price for the bill."),
 *     @OA\Property(property="maxQuantityGiftDish", type="integer", example=3, description="Maximum quantity of gift dishes."),
 *     @OA\Property(property="description", type="string", example="Promotion for new year.", description="Description of the promotion."),
 *     @OA\Property(property="description_en", type="string", example="Promotion for new year.", description="Description of the promotion in English."),
 *     @OA\Property(property="merge", type="boolean", example=true, description="Whether to merge promotions."),
 *     @OA\Property(property="active", type="boolean", example=true, description="Whether the promotion is active."),
 *     @OA\Property(property="startAt", type="string", format="date", example="2024-01-01", description="Start date of the promotion."),
 *     @OA\Property(property="endAt", type="string", format="date", example="2024-01-31", description="End date of the promotion."),
 *     @OA\Property(property="photo", type="string", format="binary", description="Promotion photo."),
 *     @OA\Property(property="box", type="integer", example=1, description="Box number for promotion (1-3)."),
 *     description="Request schema for the POST method of PromotionRequest."
 * )
 */

/**
 * @OA\Schema(
 *     schema="PromotionRequestPUT",
 *     type="object",
 *     @OA\Property(property="type", type="integer", example=1, description="Promotion type (0-2)."),
 *     @OA\Property(property="typeValue", type="integer", example=1, description="Value associated with the promotion type (0-1)."),
 *     @OA\Property(property="value", type="number", format="float", example=10.00, description="Value of the promotion."),
 *     @OA\Property(property="orderDish.id", type="integer", example=1, description="ID of the dish in the order."),
 *     @OA\Property(property="orderCategory.id", type="integer", example=1, description="ID of the food category in the order."),
 *     @OA\Property(property="giftDish.id", type="integer", example=1, description="ID of the gift dish."),
 *     @OA\Property(property="minQuantityOrderDish", type="integer", example=1, description="Minimum quantity of the order dish."),
 *     @OA\Property(property="minPriceBill", type="number", format="float", example=50.00, description="Minimum price for the bill."),
 *     @OA\Property(property="maxQuantityGiftDish", type="integer", example=3, description="Maximum quantity of gift dishes."),
 *     @OA\Property(property="description", type="string", example="Promotion for new year.", description="Description of the promotion."),
 *     @OA\Property(property="description_en", type="string", example="Promotion for new year.", description="Description of the promotion in English."),
 *     @OA\Property(property="merge", type="boolean", example=true, description="Whether to merge promotions."),
 *     @OA\Property(property="active", type="boolean", example=true, description="Whether the promotion is active."),
 *     @OA\Property(property="startAt", type="string", format="date", example="2024-01-01", description="Start date of the promotion."),
 *     @OA\Property(property="endAt", type="string", format="date", example="2024-01-31", description="End date of the promotion."),
 *     @OA\Property(property="photo", type="string", format="binary", description="Promotion photo."),
 *     @OA\Property(property="removePhoto.id", type="integer", example=1, description="ID of the photo to remove."),
 *     @OA\Property(property="box", type="integer", example=1, description="Box number for promotion (1-3)."),
 *     description="Request schema for the PUT method of PromotionRequest."
 * )
 */

/**
 * @OA\Schema(
 *     schema="PromotionRequestDELETE",
 *     type="object",
 *     description="Request schema for the DELETE method of PromotionRequest. Typically used for deleting promotions, usually without a body."
 * )
 */

class PromotionRequest extends FormRequest
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
            'orderDish' => 'array|min:1',
            'orderDish.*' => 'integer|min:1|exists:tenant.dishes,id',
            'giftDish' => 'array|min:1',
            'giftDish.*' => 'integer|min:1|exists:tenant.dishes,id',
            'orderCategory' => 'array|min:1',
            'orderCategory.*' => 'integer|min:1|exists:tenant.food_categories,id',
            'type' => 'integer|min:0|max:3',
            'typeValue' => 'integer|min:0|max:1',
            'merge' => 'boolean',
            'active' => 'boolean',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
            'order.startAt' => 'string|in:asc,desc',
            'order.endAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'type' => 'integer|min:0|max:2',
            'typeValue' => 'integer|min:0|max:1',
            'value' => 'required|numeric|min:1',
            'orderDish.id' => 'required_unless:type,1,2|min:1|exists:tenant.dishes,id',
            'orderCategory.id' => 'required_unless:type,0,1|min:1|exists:tenant.food_categories,id',
            'giftDish.id' => 'min:1|exists:tenant.dishes,id',
            'minQuantityOrderDish' => 'integer|min:0',
            'minPriceBill' => 'integer|min:0',
            'maxQuantityGiftDish' => 'integer|min:0',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'merge' => 'boolean',
            'active' => 'boolean',
            'startAt' => 'date',
            'endAt' => 'date',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=320,min_height=320,max_width=1024,max_height=1024',
            'box' => 'integer|min:1|max:3',
        ],
        self::METHOD_PUT => [
            'type' => 'integer|min:0|max:2',
            'typeValue' => 'integer|min:0|max:1',
            'value' => 'numeric|min:0',
            'orderDish.id' => 'required_unless:type,1,2|min:1|exists:tenant.dishes,id',
            'orderCategory.id' => 'required_unless:type,0,1|min:1|exists:tenant.food_categories,id',
            'giftDish.id' => 'min:1|exists:tenant.dishes,id',
            'minQuantityOrderDish' => 'integer|min:0',
            'minPriceBill' => 'numeric|min:0',
            'maxQuantityGiftDish' => 'integer|min:0',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'merge' => 'boolean',
            'active' => 'boolean',
            'startAt' => 'date',
            'endAt' => 'date',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=320,min_height=320,max_width=1024,max_height=1024',
            'removePhoto.id' => 'integer|exists:tenant.resources,id',
            'box' => 'integer|min:1|max:3',
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
