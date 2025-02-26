<?php

namespace App\Http\Requests\Api;

use App\Enum\DeliveryMethod;
use App\Enum\PaymentType;
use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="PlaceOrderRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of IDs to filter orders.")),
 *     @OA\Property(property="user", type="array", @OA\Items(type="integer", example=1, description="Array of user IDs to filter orders.")),
 *     @OA\Property(property="table", type="array", @OA\Items(type="integer", example=1, description="Array of table IDs to filter orders.")),
 *     @OA\Property(property="withTable", type="boolean", example=true, description="Whether to include table information."),
 *     @OA\Property(property="order", type="object",
 *         @OA\Property(property="id", type="string", enum={"asc", "desc"}, description="Order by order ID."),
 *         @OA\Property(property="createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date.")
 *     ),
 *     description="Request schema for the GET method of PlaceOrderRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="PlaceOrderRequestPOST",
 *     type="object",
 *     @OA\Property(property="paymentType", type="string", enum={"credit", "debit", "paypal"}, description="Type of payment."),
 *     @OA\Property(property="usePoints", type="integer", example=100, description="Number of points to use."),
 *     @OA\Property(property="pointsRatio", type="integer", example=10, description="Ratio for points conversion."),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Customer's phone number."),
 *     @OA\Property(property="delivery", type="object",
 *         @OA\Property(property="type", type="string", enum={"delivery", "pickup"}, description="Type of delivery."),
 *         @OA\Property(property="value", type="string", description="Additional delivery details.")
 *     ),
 *     @OA\Property(property="deliveryTime", type="string", format="date-time", example="15:30:00", description="Preferred delivery time."),
 *     @OA\Property(property="comment", type="string", example="Please deliver after 5 PM.", description="Additional comments for the order."),
 *     @OA\Property(property="orders", type="array", @OA\Items(type="object",
 *         @OA\Property(property="dish", type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Dish ID."),
 *             @OA\Property(property="price", type="number", format="float", example=12.99, description="Dish price.")
 *         ),
 *         @OA\Property(property="bundle", type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Bundle ID."),
 *             @OA\Property(property="price", type="number", format="float", example=29.99, description="Bundle price.")
 *         ),
 *         @OA\Property(property="additions", type="array", @OA\Items(type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Addition ID."),
 *             @OA\Property(property="price", type="number", format="float", example=2.50, description="Addition price.")
 *         )),
 *         @OA\Property(property="quantity", type="integer", example=2, description="Quantity of the dish or bundle."),
 *         @OA\Property(property="promotion", type="object",
 *             @OA\Property(property="type", type="integer", example=1, description="Promotion type."),
 *             @OA\Property(property="price", type="array", @OA\Items(type="number", format="float", example=5.00, description="Promotion price.")),
 *             @OA\Property(property="discount", type="array", @OA\Items(type="number", format="float", example=1.00, description="Promotion discount."))
 *         )
 *     )),
 *     description="Request schema for the POST method of PlaceOrderRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="PlaceOrderRequestPUT",
 *     type="object",
 *     @OA\Property(property="paymentType", type="string", enum={"credit", "debit", "paypal"}, description="Type of payment."),
 *     @OA\Property(property="usePoints", type="integer", example=100, description="Number of points to use."),
 *     @OA\Property(property="pointsRatio", type="integer", example=10, description="Ratio for points conversion."),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Customer's phone number."),
 *     @OA\Property(property="delivery", type="object",
 *         @OA\Property(property="type", type="string", enum={"delivery", "pickup"}, description="Type of delivery."),
 *         @OA\Property(property="value", type="string", description="Additional delivery details.")
 *     ),
 *     @OA\Property(property="deliveryTime", type="string", format="date-time", example="15:30:00", description="Preferred delivery time."),
 *     @OA\Property(property="comment", type="string", example="Please deliver after 5 PM.", description="Additional comments for the order."),
 *     @OA\Property(property="orders", type="array", @OA\Items(type="object",
 *         @OA\Property(property="dish", type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Dish ID."),
 *             @OA\Property(property="price", type="number", format="float", example=12.99, description="Dish price.")
 *         ),
 *         @OA\Property(property="bundle", type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Bundle ID."),
 *             @OA\Property(property="price", type="number", format="float", example=29.99, description="Bundle price.")
 *         ),
 *         @OA\Property(property="additions", type="array", @OA\Items(type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Addition ID."),
 *             @OA\Property(property="price", type="number", format="float", example=2.50, description="Addition price.")
 *         )),
 *         @OA\Property(property="quantity", type="integer", example=2, description="Quantity of the dish or bundle."),
 *         @OA\Property(property="promotion", type="object",
 *             @OA\Property(property="type", type="integer", example=1, description="Promotion type."),
 *             @OA\Property(property="price", type="array", @OA\Items(type="number", format="float", example=5.00, description="Promotion price.")),
 *             @OA\Property(property="discount", type="array", @OA\Items(type="number", format="float", example=1.00, description="Promotion discount."))
 *         )
 *     )),
 *     description="Request schema for the PUT method of PlaceOrderRequest."
 * )
 */

/**
 * @OA\Schema(
 *     schema="PlaceOrderRequestDELETE",
 *     type="object",
 *     description="Request schema for the DELETE method of PlaceOrderRequest. Generally used for deletion requests which might not require any body content."
 * )
 */
class PlaceOrderRequest extends FormRequest
{
    use RequestTrait;

    final public const string USE_POINTS_PARAM_KEY = 'usePoints';

    final public const string POINTS_RATIO_PARAM_KEY = 'pointsRatio';

    final public const string COMMENT_PARAM_KEY = 'comment';

    final public const string PAYMENT_TYPE_PARAM_KEY = 'paymentType';

    final public const string PHONE_PARAM_KEY = 'phone';

    final public const string DELIVERY_PARAM_KEY = 'delivery';

    final public const string DELIVERY_TYPE_PARAM_KEY = 'type';

    final public const string DELIVERY_TYPE_FULL_PARAM_KEY = self::DELIVERY_PARAM_KEY . '.' . self::DELIVERY_TYPE_PARAM_KEY;

    final public const string DELIVERY_DETAILS_PARAM_KEY = 'value';

    final public const string DELIVERY_DETAILS_FULL_PARAM_KEY = self::DELIVERY_PARAM_KEY . '.' . self::DELIVERY_DETAILS_PARAM_KEY;

    final public const string DELIVERY_TIME_PARAM_KEY = 'deliveryTime';

    final public const string ORDERS_PARAM_KEY = 'orders';

    final public const string DISH_PARAM_KEY = 'dish';

    final public const string BUNDLE_PARAM_KEY = 'bundle';

    final public const string DISH_PRICE_PARAM_KEY = 'price';

    final public const string DISH_NAME_PARAM_KEY = 'name';

    final public const string DISH_ID_PARAM_KEY = 'id';

    final public const string ADDITIONS_PARAM_KEY = 'additions';

    final public const string ADDITION_PRICE_PARAM_KEY = 'price';

    final public const string ADDITION_GROUP_ID_PARAM_KEY = 'id';

    final public const string ADDITION_ID_PARAM_KEY = 'id';

    final public const string ADDITION_QUANTITY_PARAM_KEY = 'quantity';

    final public const string QUANTITY_PARAM_KEY = 'quantity';

    final public const string PROMOTION_PARAM_KEY = 'promotion';

    final public const string PROMOTION_PRICE_PARAM_KEY = 'price';

    final public const string PROMOTION_DISCOUNT_PARAM_KEY = 'discount';

    final public const string PROMOTION_DISCOUNT_AMOUNT_PARAM_KEY = 'amount';

    final public const string PROMOTION_TYPE_PARAM_KEY = 'type';

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
        return [
            self::PAYMENT_TYPE_PARAM_KEY => 'required|string|in:' . PaymentType::getValuesForRequestRule(),
            self::USE_POINTS_PARAM_KEY => 'nullable|int|min:0',
            self::POINTS_RATIO_PARAM_KEY => 'nullable|int|min:0',
            self::PHONE_PARAM_KEY => 'required|string|max:15',
            self::DELIVERY_TYPE_FULL_PARAM_KEY => 'required|string|in:' . DeliveryMethod::getValuesForRequestRule(),
            self::DELIVERY_DETAILS_FULL_PARAM_KEY => 'required',
            self::DELIVERY_TIME_PARAM_KEY => 'nullable|string|date_format:"H:i:s"',
            self::COMMENT_PARAM_KEY => 'nullable|string|between:3,200',

            self::ORDERS_PARAM_KEY => 'required|array|min:1',
            self::ORDERS_PARAM_KEY . '.*.' . self::DISH_PARAM_KEY => 'required_without:' . self::ORDERS_PARAM_KEY . '.*.' . self::BUNDLE_PARAM_KEY,
            self::ORDERS_PARAM_KEY . '.*.' . self::DISH_PARAM_KEY . '.' . self::DISH_ID_PARAM_KEY => 'required_without:' . self::ORDERS_PARAM_KEY . '.*.' . self::BUNDLE_PARAM_KEY . '.' . self::DISH_ID_PARAM_KEY . '|int|min:1',
            self::ORDERS_PARAM_KEY . '.*.' . self::DISH_PARAM_KEY . '.' . self::DISH_PRICE_PARAM_KEY => 'required_without:' . self::ORDERS_PARAM_KEY . '.*.' . self::BUNDLE_PARAM_KEY . '.' . self::DISH_PRICE_PARAM_KEY . '|numeric|min:0.01',
            self::ORDERS_PARAM_KEY . '.*.' . self::DISH_PARAM_KEY . '.additionsGroups' => 'nullable|array',
            self::ORDERS_PARAM_KEY . '.*.' . self::ADDITIONS_PARAM_KEY => 'nullable|array',
            self::ORDERS_PARAM_KEY . '.*.' . self::QUANTITY_PARAM_KEY => 'required|int|min:1',

            self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY => 'nullable|array',
            self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '.' . self::PROMOTION_TYPE_PARAM_KEY => 'required_with:' . self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '|int|min:0',
            self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '.' . self::PROMOTION_PRICE_PARAM_KEY => 'required_with:' . self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '|array',
            self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '.' . self::PROMOTION_DISCOUNT_PARAM_KEY => 'required_with:' . self::ORDERS_PARAM_KEY . '.*.' . self::PROMOTION_PARAM_KEY . '|array',
        ];
    }
}
