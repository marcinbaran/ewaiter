<?php

namespace App\Managers;

use App\DTO\Orders\BillDTO;
use App\Enum\DeliveryMethod;
use App\Exceptions\ApiExceptions\Delivery\DeliveryCostCouldNotBeCalculatedException;
use App\Helpers\MoneyFormatter;
use App\Helpers\PolygonHelper;
use App\Http\Controllers\ParametersTrait;
use App\Models\DeliveryRange;
use App\Services\GeoServices\GeoService;
use App\ValueObjects\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryRangeManager
{
    use ParametersTrait;

    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @return DeliveryRange
     */
    public function create(Request $request): DeliveryRange
    {
        $params = $this->getParams($request, ['name', 'range_from', 'range_to', 'range_polygon', 'min_value', 'free_from', 'cost', 'km_cost', 'out_of_range']);
        $delivery_range = DB::connection('tenant')->transaction(function () use ($params) {
            $delivery_range = DeliveryRange::create(DeliveryRange::decamelizeArray($params))->fresh();
            $delivery_range->update(['range_from' => $delivery_range->getRangeFrom()]);

            return $delivery_range;
        });

        return $delivery_range;
    }

    /**
     * @param Request $request
     * @param DeliveryRange    $delivery_range
     *
     * @return DeliveryRange
     */
    public function update(Request $request, DeliveryRange $delivery_range): DeliveryRange
    {
        $params = $this->getParams($request, ['name', 'range_to', 'range_polygon', 'min_value', 'free_from', 'cost', 'km_cost', 'out_of_range']);
        DB::connection('tenant')->transaction(function () use ($params, $delivery_range) {
            $delivery_range->update($params);
        });

        return $delivery_range;
    }

    /**
     * @param DeliveryRange $delivery_range
     *
     * @return DeliveryRange
     */
    public function delete(DeliveryRange $delivery_range): DeliveryRange
    {
        DB::connection('tenant')->transaction(function () use ($delivery_range) {
            $delivery_range->delete();
        });

        return $delivery_range;
    }

    /**
     * @throws DeliveryCostCouldNotBeCalculatedException
     */
    public static function getDeliveryCost(BillDTO $billData): float
    {
        if ($billData->getDeliveryMethod() !== DeliveryMethod::DELIVERY_TO_ADDRESS) {
            return 0;
        }

        if (self::getDeliveryCostForPolygon($billData) !== null) {

            return self::getDeliveryCostForPolygon($billData);
        }

        throw new DeliveryCostCouldNotBeCalculatedException();
    }

    /**
     * @throws DeliveryCostCouldNotBeCalculatedException
     */
    protected static function getDeliveryCostForPolygon(BillDTO $billData): ?float
    {
        foreach (DeliveryRange::orderBy('cost', 'asc')->get() as $deliveryRange) {
            $rangePolygon = $deliveryRange->range_polygon;
            $jsonString = preg_replace('/\s+/', '', $rangePolygon);
            $array = json_decode($jsonString, true);

            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoordsForValueObjectAddress(Address::createFromRequest(request()));

            $point = array_reverse($addressCoords->toArray());

            if (PolygonHelper::isPointInPolygon($point, $array)) {
                if (self::isFreeDelivery($deliveryRange, $billData)) {
                    return 0;
                }

                return MoneyFormatter::format($deliveryRange->cost);
            }
        }

        return null;
    }

    protected static function isFreeDelivery(DeliveryRange $deliveryRange, BillDTO $billData): bool
    {
        return $deliveryRange->free_from !== null && (($billData->getTotalPrice() - $billData->getDiscount()) >= $deliveryRange->free_from);
    }
}
