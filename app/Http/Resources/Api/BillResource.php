<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Restaurant;
use App\Models\Review;
use App\Repositories\MultiTentantRepositoryTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="BillResource",
 *     type="object",
 *     title="Bill Resource",
 *     description="Resource representing a bill",
 *     @OA\Property(property="id", type="integer", description="Bill ID"),
 *     @OA\Property(property="oldPrice", type="string", description="Original price of the bill"),
 *     @OA\Property(property="price", type="string", description="Total price of the bill"),
 *     @OA\Property(property="discount", type="string", description="Discount applied"),
 *     @OA\Property(property="paid", type="boolean", description="Payment status"),
 *     @OA\Property(property="paymentAt", type="string", format="date-time", description="Payment timestamp"),
 *     @OA\Property(property="comment", type="string", description="Customer's comment"),
 *     @OA\Property(property="timeWait", type="string", format="date-time", description="Estimated waiting time"),
 *     @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="status", type="string", description="Status of the bill"),
 *     @OA\Property(property="gamesPayment", type="boolean", description="Payment for games"),
 *     @OA\Property(property="orderMinimalPrice", type="string", description="Minimum order value"),
 *     @OA\Property(property="tip", type="number", format="float", description="Tip amount"),
 *     @OA\Property(property="roomDelivery", type="boolean", description="Room delivery flag"),
 *     @OA\Property(property="paidType", type="string", description="Type of payment"),
 *     @OA\Property(property="tableNumber", type="integer", description="Table number"),
 *     @OA\Property(property="personalPickup", type="boolean", description="Personal pickup flag"),
 *     @OA\Property(property="phone", type="string", description="Customer's phone number"),
 *     @OA\Property(property="cart", type="array", @OA\Items(type="object"), description="Cart items"),
 *     @OA\Property(property="deliveryTime", type="string", description="Delivery time"),
 *     @OA\Property(property="deliveryCost", type="number", format="float", description="Delivery cost"),
 *     @OA\Property(property="deliveryType", type="string", description="Type of delivery"),
 *     @OA\Property(property="deliverySettingsType", type="string", description="Settings type for delivery"),
 *     @OA\Property(property="serviceCharge", type="number", format="float", description="Service charge"),
 *     @OA\Property(property="points", type="integer", description="Loyalty points earned"),
 *     @OA\Property(property="pointsValue", type="number", format="float", description="Value of loyalty points"),
 *     @OA\Property(property="restaurant_id", type="integer", description="Restaurant ID"),
 *     @OA\Property(property="restaurant_name", type="string", description="Restaurant name"),
 *     @OA\Property(property="restaurant_hostname", type="string", description="Restaurant hostname"),
 *     @OA\Property(property="userResId", type="integer", description="User reservation ID"),
 *     @OA\Property(property="userId", type="integer", description="User ID"),
 *     @OA\Property(property="priceToPay", type="number", format="float", description="Total price to pay"),
 *     @OA\Property(property="paymentUrl", type="string", description="URL for payment"),
 *     @OA\Property(property="released_at", type="string", format="date-time", description="Release date"),
 *     @OA\Property(property="review", type="array", @OA\Items(ref="#/components/schemas/Review"), description="Customer reviews"),
 *     @OA\Property(property="prices", type="object", description="Price breakdown", @OA\Property(property="dishes", type="number", format="float", description="Total dish price"), @OA\Property(property="delivery", type="number", format="float", description="Total delivery cost"), @OA\Property(property="packages", type="number", format="float", description="Total package price"), @OA\Property(property="serviceCharge", type="number", format="float", description="Total service charge"), @OA\Property(property="discount", type="number", format="float", description="Total discount"), @OA\Property(property="points", type="number", format="float", description="Total points value")),
 *     @OA\Property(property="geolocalization", type="object", @OA\Property(property="lat", type="number", format="float", description="Latitude"), @OA\Property(property="lng", type="number", format="float", description="Longitude"))
 * )
 */
class BillResource extends ApiResource
{
    use ResourceTrait, MultiTentantRepositoryTrait;

    const string BILL_ID_KEY = 'bill_id';
    const string RESTAURANT_ID_KEY = 'restaurant_id';

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;
    const int TIME_TO_REVIEW = 48;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $restaurantDetails = Restaurant::getCurrentRestaurant();
        $restaurantResource = new RestaurantResource($restaurantDetails);
        $address = Restaurant::getLatLngAddress();
        list($lat, $lng, $address) = $address;

        $array = [
            'id' => $this->id,
            'oldPrice' => number_format($this->price, 2, '.', ''),
            'price' => (string)$this->getFullPrice(), //@TODO remove casting to string after app update
            'discount' => number_format($this->discount, 2, '.', ''),
            'paid' => $this->paid,
            'paymentAt' => $this->payment_at,
            'comment' => $this->comment,
            'timeWait' => $this->dateFormat($this->time_wait, 'Y-m-d\TH:i'),
            'createdAt' => $this->dateFormat($this->created_at),
            'status' => $this->status,
            'gamesPayment' => $this->games_payment,
            'orderMinimalPrice' => $restaurantResource->getMinOrderValue(),
            'tip' => $this->tip,
            'roomDelivery' => $this->room_delivery,
            'paidType' => $this->paid_type,
            'tableNumber' => $this->table_number,
            'personalPickup' => $this->personal_pickup,
            'phone' => $this->phone,
            'cart' => $this->cart,
            'deliveryTime' => $this->delivery_time,
            'deliveryCost' => $this->delivery_cost,
            'deliveryType' => $this->getDeliveryType(),
            'deliverySettingsType' => $this->getTypeSettingsDelivery(),
            'serviceCharge' => number_format($this->service_charge, 2, '.', ''),
            'points' => $this->points,
            'pointsValue' => number_format($this->points_value, 2, '.', ''),
            'restaurant_id' => $restaurantDetails ? $restaurantDetails->id : $this->restaurant_id,
            'restaurant_name' => $restaurantDetails ? $restaurantDetails->name : $this->restaurant_name,
            'restaurant_hostname' => $restaurantDetails ? $restaurantDetails->hostname : $this->restaurant_hostname,
            'userResId' => $this->user_res_id,
            'userId' => $this->user_id,
            'priceToPay' => round($this->getPriceToPay(), 2),
            'paymentUrl' => $this->getPaymentUrlForUnpaidBill(),
            'released_at' => $this->released_at,
            'review' => $this->getReview($this->id, $restaurantDetails ? $restaurantDetails->id : $this->restaurant_id),
            'review_editable' => $this->reviewEditable(),
            'prices' => [
                'dishes' => $this->getDishPrice(),
                'delivery' => $this->delivery_cost,
                'packages' => $this->getPackagePrice(),
                'serviceCharge' => $this->service_charge,
                'discount' => $this->discount,
                'points' => $this->points_value,
            ],
            'geolocalization' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ];

        if ('bills.store' == $request->route()->getName()) {
            $ratio = config('admanager.ratio') ? config('admanager.ratio') : 100;
            $array['availablePointsValue'] = number_format($this->getAvailablePoints() / $ratio, 2, '.', '');
            $array['availablePoints'] = $this->getAvailablePoints();
        }

        if ($this->isWithOrders($request) || $this->with_orders) {
            if (!$restaurantDetails) {
                $restaurantDetails = Restaurant::find($this->restaurant_id);
            }

            $this->reconnect($restaurantDetails);
            $array['orders'] = OrderResource::collection($this->orders);
            $this->reset();
        }

        if ($this->isWithRestaurantOrders($request)) {
            $array['orders'] = [];
            if (count($this->orders)) {
                foreach ($this->orders as $order) {
                    $array['orders'][] = new OrderResource($order, $this->restaurant_id);
                }
            }
            //$array['orders'] = OrderResource::collection($this->orders);
        }

        if ($this->isWithAddress($request) || $this->with_address) {
            $array['address'] = $this->address;
        }

        if ($this->with_user) {
            $array['user'] = $this->user;
        }

        if ($this->with_updated_at) {
            $array['updatedAt'] = $this->updated_at;
        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithOrders(Request $request): bool
    {
        return $this->isBillsRoute($request) && (!$this->isRootRoute($request) || (int)$request->withOrders);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithRestaurantOrders(Request $request): bool
    {
        return $request->route()->getName() == 'bills.index_all';
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithPromotions(Request $request): bool
    {
        return $this->isBillsRoute($request) && (!$this->isRootRoute($request) || (int)$request->withPromotions);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithAddress(Request $request): bool
    {
        return $this->isBillsRoute($request) && (!$this->isRootRoute($request) || (int)$request->withAddress);
    }

    protected function isRootRoute(Request $request): bool
    {
        return 'bills.index' == $request->route()->getName();
    }

    private function getReview(int $billId, int $restaurantId): ?Review
    {
        return Review::where(self::BILL_ID_KEY, $billId)->where(self::RESTAURANT_ID_KEY, $restaurantId)->get()->first();
    }

    private function reviewEditable(): bool
    {
        return Carbon::now()->diffInHours($this->released_at) < self::TIME_TO_REVIEW;
    }
}
