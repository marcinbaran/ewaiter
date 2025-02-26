<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="BillRequest_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", description="Number of items per page", example=10),
 *     @OA\Property(property="page", type="integer", description="Page number", example=1),
 *     @OA\Property(property="noLimit", type="boolean", description="No limit on results", example=false),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="List of IDs"),
 *     @OA\Property(property="restaurant_id", type="array", @OA\Items(type="integer"), description="List of restaurant IDs"),
 *     @OA\Property(property="status", type="array", @OA\Items(type="integer"), description="List of statuses"),
 *     @OA\Property(property="roomDelivery", type="array", @OA\Items(type="string"), description="List of room delivery options"),
 *     @OA\Property(property="withOrders", type="boolean", description="Include orders", example=true),
 *     @OA\Property(property="withPromotions", type="boolean", description="Include promotions", example=true),
 *     @OA\Property(property="order", type="object", @OA\Property(property="id", type="string", enum={"asc", "desc"}), @OA\Property(property="price", type="string", enum={"asc", "desc"}), @OA\Property(property="timeWait", type="string", enum={"asc", "desc"}), @OA\Property(property="paymentAt", type="string", enum={"asc", "desc"}), @OA\Property(property="createdAt", type="string", enum={"asc", "desc"})),
 *     @OA\Property(property="paid", type="boolean", description="Paid status", example=false),
 *     @OA\Property(property="personalPickup", type="boolean", description="Personal pickup option", example=false),
 *     @OA\Property(property="deliveryTime", type="string", format="time", description="Delivery time", example="15:00:00"),
 *     @OA\Property(property="fromDate", type="string", format="date", description="Start date for filtering", example="2024-01-01"),
 *     @OA\Property(property="toDate", type="string", format="date", description="End date for filtering", example="2024-12-31"),
 *     @OA\Property(property="date", type="string", format="date", description="Specific date", example="2024-06-15"),
 *     @OA\Property(property="deliveryType", type="string", description="Delivery type"),
 * )
 *
 * @OA\Schema(
 *     schema="BillRequest_POST",
 *     type="object",
 *     @OA\Property(property="timeWait", type="string", format="date-time", description="Time waited for processing", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="comment", type="string", description="Comment for the bill", example="Please handle with care."),
 *     @OA\Property(property="gamesPayment", type="number", format="float", description="Games payment amount", example=0.00),
 *     @OA\Property(property="tip", type="number", format="float", description="Tip amount", example=5.00),
 *     @OA\Property(property="roomDelivery", type="string", description="Room delivery option", example="Room 101"),
 *     @OA\Property(property="paid", type="boolean", description="Payment status", example=false),
 *     @OA\Property(property="paymentAt", type="string", format="date", description="Payment date", example="2024-01-01"),
 *     @OA\Property(property="paidType", type="string", description="Payment type", enum={"cash", "card", "room", "card_delivery", "hotel_bill", "card_p24", "card_tpay"}),
 *     @OA\Property(property="personalPickup", type="boolean", description="Personal pickup option", example=false),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Phone number", example="+1234567890"),
 *     @OA\Property(property="deliveryTime", type="string", format="time", description="Delivery time", example="15:00:00"),
 *     @OA\Property(property="deliveryType", type="object", @OA\Property(property="type", type="string", enum={"delivery_address", "delivery_table", "delivery_room", "delivery_personal_pickup"})),
 * )
 *
 * @OA\Schema(
 *     schema="BillRequest_PUT",
 *     type="object",
 *     @OA\Property(property="timeWait", type="string", format="date-time", description="Time waited for processing", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="status", type="integer", description="Status of the bill", example=1),
 *     @OA\Property(property="comment", type="string", description="Comment for the bill", example="Please handle with care."),
 *     @OA\Property(property="gamesPayment", type="number", format="float", description="Games payment amount", example=0.00),
 *     @OA\Property(property="tip", type="number", format="float", description="Tip amount", example=5.00),
 *     @OA\Property(property="roomDelivery", type="string", description="Room delivery option", example="Room 101"),
 *     @OA\Property(property="paid", type="boolean", description="Payment status", example=false),
 *     @OA\Property(property="paymentAt", type="string", format="date", description="Payment date", example="2024-01-01"),
 *     @OA\Property(property="paidType", type="string", description="Payment type", enum={"cash", "card", "room", "card_delivery", "hotel_bill", "card_p24", "card_tpay"}),
 *     @OA\Property(property="personalPickup", type="boolean", description="Personal pickup option", example=false),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Phone number", example="+1234567890"),
 *     @OA\Property(property="deliveryTime", type="string", format="time", description="Delivery time", example="15:00:00"),
 *     @OA\Property(property="deliveryType", type="object", @OA\Property(property="type", type="string", enum={"delivery_address", "delivery_table", "delivery_room", "delivery_personal_pickup"})),
 * )
 *
 * @OA\Schema(
 *     schema="BillRequest_DELETE",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID of the bill", example=1),
 * )
 */
class BillRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'noLimit' => 'boolean',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'restaurant_id' => 'array|min:1',
            'restaurant_id.*' => 'integer|min:1',
            'status' => 'array|min:1',
            'status.*' => 'integer|min:0|max:4',
            'roomDelivery' => 'array|min:1',
            'roomDelivery.*' => 'string',
            'withOrders' => 'boolean',
            'withPromotions' => 'boolean',
            'order.id' => 'string|in:asc,desc',
            'order.price' => 'string|in:asc,desc',
            'order.timeWait' => 'string|in:asc,desc',
            'order.paymentAt' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
            'paid' => 'boolean',
            'personalPickup' => 'boolean',
            'deliveryTime' => 'string|date_format:H:i:s',
            'fromDate' => 'date_format:Y-m-d|nullable',
            'toDate' => 'date_format:Y-m-d|nullable',
            'date' => 'date_format:Y-m-d|nullable',
            'deliveryType' => '',
        ],
        self::METHOD_POST => [
            'timeWait' => 'nullable|string|date_format:"Y-m-d H:i:s"',
            'comment' => 'nullable|string',
            'gamesPayment' => 'numeric|min:0',
            'tip' => 'numeric|min:0',
            'roomDelivery' => 'nullable|string',
            'paid' => 'boolean',
            'paymentAt' => 'date',
            'paidType' => 'string|in:cash,card,room,card_delivery,hotel_bill,card_p24,card_tpay',
            'personalPickup' => 'nullable|boolean',
            'phone' => 'nullable|string|max:15',
            'deliveryTime' => 'nullable|string|date_format:"H:i:s"',
            'deliveryType.type' => 'in:delivery_address,delivery_table,delivery_room,delivery_personal_pickup',
        ],
        self::METHOD_PUT => [
            'timeWait' => 'nullable|string|date_format:"Y-m-d H:i:s"',
            'status' => 'integer|min:0|max:4',
            'comment' => 'nullable|string',
            'gamesPayment' => 'numeric|min:0',
            'tip' => 'numeric|min:0',
            'roomDelivery' => 'nullable|string',
            'paid' => 'boolean',
            'paymentAt' => 'date',
            'paidType' => 'string|in:cash,card,room,card_delivery,hotel_bill,card_p24,card_tpay',
            'personalPickup' => 'nullable|boolean',
            'phone' => 'nullable|string|max:15',
            'deliveryTime' => 'nullable|string|date_format:"H:i:s"',
            'deliveryType.type' => 'in:delivery_address,delivery_table,delivery_room,delivery_personal_pickup',
        ],
        self::METHOD_DELETE => [],
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

        if (in_array($this->getMethod(), [self::METHOD_POST, self::METHOD_PUT])) {
            $user = auth()->user();
            if (! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
                unset($rules['paid']);
            }

            if (in_array($this->getMethod(), [self::METHOD_POST])) {
                $orderRules = array_merge(OrderRequest::getRule($this->getMethod()), [
                    'id' => 'integer|min:1',
                    'quantity' => 'integer|min:0',
                ]);
                $rules['orders'] = 'array|min:1';
            }

            if (in_array($this->getMethod(), [self::METHOD_PUT])) {
                $orderRules = array_merge(OrderRequest::getRule($this->getMethod()), [
                    'id' => 'integer|min:1|exists:tenant.orders,id',
                    'quantity' => 'integer|min:0',
                ]);
                $rules['orders'] = 'array|min:1';
            }

            foreach ($orderRules as $k => $oRule) {
                $rules['orders.*.'.$k] = $oRule;
                if ('bill.id' == $k) {
                    $rules['orders.*.'.$k] = 'integer|min:1|exists:tenant.bills,id';
                }
            }
        }

        if (in_array($this->getMethod(), [self::METHOD_PUT])) {
            $user = auth()->user();
            $available_points = $user->getBalance();
            $rules['points'] = 'nullable|integer|max:'.$available_points;
        }

        return $rules;
    }
}
