<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'price' => number_format($this->price, 2, '.', ''),
            'discount' => $this->discount,
            'paid' => $this->paid,
            'paymentAt' => $this->payment_at,
            'comment' => $this->comment,
            'timeWait' => $this->dateFormat($this->time_wait),
            'createdAt' => $this->dateFormat($this->created_at),
            'status' => $this->status,
            'gamesPayment' => $this->games_payment,
            'tip' => $this->tip,
            'roomDelivery' => $this->room_delivery,
            'paidType' => $this->paid_type,
            'tableNumber' => $this->table_number,
            'personalPickup' => $this->personal_pickup,
            'phone' => $this->phone,
            'deliveryTime' => $this->delivery_time,
            'deliveryCost' => $this->delivery_cost,
            'deliverySettingsType' => $this->getTypeSettingsDelivery(),
            'serviceCharge' => number_format($this->service_charge, 2, '.', ''),
            'totalPrice' => $this->getFullPrice(),
        ];
        $array['orders'] = OrderResource::collection($this->orders->fresh());
        $array['address'] = $this->address;
        $promotions = Promotion::findActiveForBillAndOrderDish($this->resource);
        $promotions->count() < 2 ?: $promotions = $promotions->filter(function ($promotion) {
            return $promotion->merge = Promotion::MERGE_YES;
        });
        $array['promotions'] = PromotionResource::collection($promotions);

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithOrders(Request $request): bool
    {
        return $this->isBillsRoute($request) && (! $this->isRootRoute($request) || (int) $request->withOrders);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithPromotions(Request $request): bool
    {
        return $this->isBillsRoute($request) && (! $this->isRootRoute($request) || (int) $request->withPromotions);
    }

    /**
     * @return string
     */
    public function isPaid(): string
    {
        return $this->paid ? 'Yes' : 'No';
    }
}
