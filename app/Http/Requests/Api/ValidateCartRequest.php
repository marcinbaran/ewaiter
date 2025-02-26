<?php

namespace App\Http\Requests\Api;

use App\Enum\DeliveryMethod;
use App\Enum\PaymentType;
/**
 * @OA\Schema(
 *     schema="ValidateCartRequest_POST",
 *     type="object",
 *     required={"orders"},
 *     @OA\Property(property="payment_type", type="string", description="Payment type", enum={"credit_card", "paypal", "bank_transfer"}),
 *     @OA\Property(property="use_points", type="integer", description="Points to use"),
 *     @OA\Property(property="points_ratio", type="integer", description="Points ratio"),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="User's phone number"),
 *     @OA\Property(property="delivery_type", type="string", description="Delivery method", enum={"standard", "express"}),
 *     @OA\Property(property="delivery_details", type="string", description="Additional delivery details"),
 *     @OA\Property(property="delivery_time", type="string", format="time", description="Preferred delivery time"),
 *     @OA\Property(property="comment", type="string", description="Additional comments", minLength=3, maxLength=200),
 *     @OA\Property(property="orders", type="array", @OA\Items(
 *         type="object",
 *         @OA\Property(property="dish", type="object", description="Dish details",
 *             @OA\Property(property="id", type="integer", description="Dish ID"),
 *             @OA\Property(property="price", type="number", format="float", description="Dish price"),
 *             @OA\Property(property="additionsGroups", type="array", @OA\Items(type="string")),
 *         ),
 *         @OA\Property(property="bundle", type="object", description="Bundle details",
 *             @OA\Property(property="id", type="integer", description="Bundle ID"),
 *             @OA\Property(property="price", type="number", format="float", description="Bundle price"),
 *         ),
 *         @OA\Property(property="quantity", type="integer", description="Quantity ordered"),
 *         @OA\Property(property="promotion", type="object", description="Promotion details",
 *             @OA\Property(property="promotion_type", type="integer", description="Type of promotion"),
 *             @OA\Property(property="promotion_price", type="array", @OA\Items(type="number", format="float")),
 *             @OA\Property(property="promotion_discount", type="array", @OA\Items(type="number", format="float")),
 *         ),
 *     )),
 * )
 *
 * @OA\Schema(
 *     schema="ValidateCartRequest_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", description="Page number"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer")),
 *     @OA\Property(property="order", type="object", @OA\Property(property="id", type="string", enum={"asc", "desc"}), @OA\Property(property="name", type="string", enum={"asc", "desc"})),
 * )
 */
final class ValidateCartRequest extends PlaceOrderRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            self::PAYMENT_TYPE_PARAM_KEY => 'nullable|string|in:'.PaymentType::getValuesForRequestRule(),
            self::USE_POINTS_PARAM_KEY => 'nullable|int|min:0',
            self::POINTS_RATIO_PARAM_KEY => 'nullable|int|min:0',
            self::PHONE_PARAM_KEY => 'nullable|string|max:15',
            self::DELIVERY_TYPE_FULL_PARAM_KEY => 'nullable|string|in:'.DeliveryMethod::getValuesForRequestRule(),
            self::DELIVERY_DETAILS_FULL_PARAM_KEY => 'nullable',
            self::DELIVERY_TIME_PARAM_KEY => 'nullable|string|date_format:"H:i:s"',
            self::COMMENT_PARAM_KEY => 'nullable|string|between:3,200',

            self::ORDERS_PARAM_KEY => 'required|array|min:1',
            self::ORDERS_PARAM_KEY.'.*.'.self::DISH_PARAM_KEY => 'required_without:' . self::ORDERS_PARAM_KEY.'.*.'.self::BUNDLE_PARAM_KEY,
            self::ORDERS_PARAM_KEY.'.*.'.self::DISH_PARAM_KEY.'.id' => 'required_without:' . self::ORDERS_PARAM_KEY.'.*.'.self::BUNDLE_PARAM_KEY.'.id' . '|int|min:1',
            self::ORDERS_PARAM_KEY.'.*.'.self::DISH_PARAM_KEY.'.price' => 'required_without:' . self::ORDERS_PARAM_KEY.'.*.'.self::BUNDLE_PARAM_KEY.'.price' . '|numeric|min:0.01',
            self::ORDERS_PARAM_KEY.'.*.'.self::DISH_PARAM_KEY.'.additionsGroups' => 'nullable|array',
            self::ORDERS_PARAM_KEY.'.*.'.self::ADDITIONS_PARAM_KEY => 'nullable|array',
            self::ORDERS_PARAM_KEY.'.*.'.self::QUANTITY_PARAM_KEY => 'nullable|int|min:1',

            self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY => 'nullable|array',
            self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'.'.self::PROMOTION_TYPE_PARAM_KEY => 'required_with:'.self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'|int|min:0',
            self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'.'.self::PROMOTION_PRICE_PARAM_KEY => 'required_with:'.self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'|array',
            self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'.'.self::PROMOTION_DISCOUNT_PARAM_KEY => 'required_with:'.self::ORDERS_PARAM_KEY.'.*.'.self::PROMOTION_PARAM_KEY.'|array',
        ];
    }
}
