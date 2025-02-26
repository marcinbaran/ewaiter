<?php

namespace App\Managers;

use App\DTO\Orders\BillDTO;
use App\Enum\DeliveryMethod;
use App\Events\BillStatusEvent;
use App\Events\BillStatusMobileEvent;
use App\Events\OrderStatusEvent;
use App\Events\PromotionBillEvent;
use App\Exceptions\ApiExceptions\Setting\SettingNotFoundException;
use App\Helpers\MoneyFormatter;
use App\Helpers\PromotionHelper;
use App\Http\Controllers\ParametersTrait;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Models\Addition;
use App\Models\Address;
use App\Models\Bill;
use App\Models\DeliveryRange;
use App\Models\Dish;
use App\Models\FireBaseNotificationV2;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\Refund;
use App\Models\Restaurant;
use App\Models\Settings;
use App\Services\GeoServices\GeoService;
use App\Services\GoogleDistanceService;
use App\Services\TranslationService;
use App\Services\UtilService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BillManager
{
    use ParametersTrait;

    final protected const string PERCENT_SERVICE_CHARGE_KEY = 'service_charge_procent';

    final protected const string FIXED_VALUE_SERVICE_CHARGE_KEY = 'service_charge';

    final protected const string SERVICE_CHARGE_SETTING_KEY = 'service_charge';

    final protected const string PACKAGE_COST_SETTING_KEY = 'koszt_opakowania';

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return Bill
     */
    public function create(Request $request): Bill
    {
        $params = $this->getParams($request, ['cart' => 1, 'paid', 'comment', 'paymentAt', 'gamesPayment', 'tip', 'roomDelivery', 'paidType', 'tableNumber', 'personalPickup', 'phone', 'deliveryTime']);
        $orders = (array) $request->orders;
        $address = (array) $request->address;

        // $deliveryType = $request->input('deliveryType');
        $deliveryType = $request->deliveryType;
        if ($deliveryType) {
            switch ($deliveryType['type']) {
                case 'delivery_address':
                    $address = (array) $deliveryType['value'];
                    break;
                case 'delivery_table':
                    $params['tableNumber'] = $deliveryType['value'];
                    break;
                case 'delivery_room':
                    $params['roomDelivery'] = $deliveryType['value'];
                    break;
                case 'delivery_personal_pickup':
                    $params['personalPickup'] = $deliveryType['value'];
                    break;
                default:;
            }
        }

        $bill = DB::connection('tenant')->transaction(function () use ($params, $orders, $address, $request) {
            $params['user_id'] = auth()->user()->id;
            $bill = Bill::create(Bill::decamelizeArray($params))->fresh();
            $user = Auth::user();
            foreach ($orders as $orderParam) {
                $orderParam['user_id'] = auth()->user()->id;
                $orderParam['dish_id'] = $orderParam['dish']['id'];
                $orderParam['table_id'] = $orderParam['table']['id'] ?? isset($user->table->id) ? $user->table->id : null;
                isset($orderParam['status']) ?: $orderParam['status'] = $bill->status;
                $paramsToJson = [];
                ! isset($orderParam['additions']) ?: $paramsToJson['additions'] = $orderParam['additions'];
                empty($paramsToJson) ?: $orderParam['customize'] = $paramsToJson;
                ! isset($orderParam['roast']) ?: $paramsToJson['roast'] = $orderParam['roast'];
                $additions_price = 0;
                $orderParam['customize']['additions'] = [];
                if (isset($paramsToJson['additions']) && is_array($paramsToJson['additions']) && count($paramsToJson['additions'])) {
                    foreach ($paramsToJson['additions'] as $addtion_row) {
                        $addition = Addition::where('id', $addtion_row['id'])->first();
                        $orderParam['customize']['additions'][] = [
                            'id' => $addition->id,
                            'price' => $addition->price,
                            'type' => (int) ! empty($addtion_row['type']) ?? $addtion_row['type'],
                            'quantity' => $orderParam['quantity'],
                        ];
                        $additions_price = $additions_price + ($addition->price * $orderParam['quantity']);
                    }
                }
                if ((isset($params['personalPickup']) && $params['personalPickup']) || count($address)) {
                    $package_price = Settings::getSetting('koszt_opakowania', 'koszt_opakowania', true, false);
                    $orderParam['package_price'] = $package_price;
                }
                $dish = Dish::where('id', $orderParam['dish_id'])->first();
                if (UtilService::finalPriceForDish($dish) < config('app.minimal_dish_price')) {
                    continue;
                }
                if ($dish) {
                    $orderParam['discount'] = $dish->discount * $orderParam['quantity'];
                    $orderParam['price'] = $params['tip'] + $dish->price - $orderParam['discount'];
                    $orderParam['additions_price'] = $additions_price;
                    $orderParam['tax'] = $dish->tax;
                }
                $orderParam = array_diff_key($orderParam, ['dish', 'roast', 'additions', 'bill', 'table', 'additions_price', 'package_price']);
                $bill->orders()->create(Order::decamelizeArray($orderParam));
            }

            event(new PromotionBillEvent($bill->fresh()));

            if ($address) {
                $address = array_diff_key($address, ['company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone']);
                $address = $bill->address()->create(Address::decamelizeArray($address));
                $bill->address_id = $address->id;
                $bill->delivery_cost = $this->getDeliveryPrice($request, $bill->fresh(), $address);
                $bill->save();
                $bill->fresh();
            }

            return $bill;
        });

        return $bill;
    }

    /**
     * @param Request $request
     * @param Bill $bill
     *
     * @return Bill
     */
    public function update(Request $request, Bill $bill): Bill
    {
        $params = $this->getParams($request, ['cart' => 0, 'status', 'timeWait', 'deliveryTime', 'paid'/*'comment', 'paymentAt', 'gamesPayment', 'tip', 'roomDelivery', 'paidType','tableNumber','personalPickup','phone'*/]);
        if ($bill->cart == 1 && $request->has('points') && $request->get('points')) {
            $ratio = config('admanager.ratio') ? config('admanager.ratio') : 100;
            $points = (float) $request->get('points');
            $user = Auth::user();
            $spend_points = $bill->spendPoints($points);
            throw_if(! $spend_points, new AccessDeniedHttpException(gtrans('admin.The availability of points has changed, please re-order')));
            $params['points'] = $points;
            $params['points_value'] = number_format($points / $ratio, 2, '.', '');
        }
        $orders = (array) $request->orders;
        $address = (array) $request->address;
        DB::connection('tenant')->transaction(function () use ($params, $orders, $bill, $address) {
            if (! empty($params)) {
                $bill->update($params);
            }
            $user = Auth::user();

            foreach ($orders as $orderParam) {
                $order = null;
                $orderParam['dish_id'] = $orderParam['dish']['id'] ?? null;
                $orderParam['table_id'] = $orderParam['table']['id'] ?? isset($user->table->id) ? $user->table->id : null;
                if (isset($orderParam['id'])) {
                    $order = Order::find($orderParam['id']);
                } elseif (! empty($orderParam['dish_id'])) {
                    $order = Order::where('dish_id', '=', $orderParam['dish_id'])
                        ->where('bill_id', '=', $bill->id)
                        ->first();
                }
                ! empty($orderParam['dish_id']) ?: $orderParam['dish_id'] = $order->dish_id;

                $orderParam['additions'] = [];
                if (isset($paramsToJson['additions']) && is_array($paramsToJson['additions']) && count($paramsToJson['additions'])) {
                    foreach ($paramsToJson['additions'] as $addtion_row) {
                        $addition = Addition::where('id', $addtion_row['id'])->first();
                        $orderParam['additions'][] = [
                            'id' => $addition->id,
                            'price' => $addition->price,
                            'type' => ! empty($addtion_row['type']) ?? $addtion_row['type'],
                            'quantity' => $orderParam['quantity'],
                        ];
                    }
                }
                $orderParam['customize'] = empty($paramsToJson) ? $order->customize : $paramsToJson;

                $orderParam = array_diff_key($orderParam, ['dish', 'roast', 'additions', 'bill', 'table']);

                if ($order) {
                    $orderParam['bill_id'] = $bill->id; //move if has other id
                    $orderParam['quantity'] ?? $orderParam['quantity'] = $order->quantity;
                    $orderParam['quantity'] ? $order->update(Order::decamelizeArray($orderParam)) : $order->delete(); //if quantity=0 then delete
                } else {
                    $order = $bill->orders()->create(Order::decamelizeArray($orderParam));
                }
            }

            if ($address) {
                $address = array_diff_key($address, ['id', 'company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone']);

                $bill->address()->update(Address::decamelizeArray($address));
            }

            if (! ($bill->paid_type == 'card_tpay' || $bill->paid_type == 'card_p24') && $bill->status == Bill::STATUS_NEW) {
                $bill->update(['ticket' => 1]);
                //$bill->sendTickets();
            }

            event(new BillStatusEvent($bill->fresh(), $this->transService));
            event(new BillStatusMobileEvent($bill->fresh(), $this->transService));
        });

        return $bill;
    }

    /**
     * @param Bill $bill
     *
     * @return Bill
     */
    public function delete(Bill $bill): Bill
    {
        DB::connection('tenant')->transaction(function () use ($bill) {
            $bill->delete();
        });

        return $bill;
    }

    /**
     * @param Request $request
     *
     * @return Bill
     */
    public function status_edit($request): Bill
    {
        $bill = Bill::where('id', $request->get('pk'))->first();
        DB::connection('tenant')->transaction(function () use ($bill, $request) {
            $bill->status = $request->get('value');
            $bill->save();
        });

        if (count($bill->orders)) {
            foreach ($bill->orders as $order) {
                DB::connection('tenant')->transaction(function () use ($order, $request) {
                    $order->status = $request->get('value');
                    $order->save();
                });
                event(new OrderStatusEvent($order->fresh(), $this->transService));
            }
        }

        event(new BillStatusEvent($bill->fresh(), $this->transService));
        event(new BillStatusMobileEvent($bill->fresh(), $this->transService));

        return $bill;
    }

    /**
     * @param Request $request
     *
     * @return Bill
     */
    public function paid_edit($request): Bill
    {
        $bill = Bill::where('id', $request->get('pk'))->first();
        DB::connection('tenant')->transaction(function () use ($bill, $request) {
            $bill->paid = (bool) $request->get('value');
            $bill->save();
        });

        if (count($bill->orders)) {
            foreach ($bill->orders as $order) {
                DB::connection('tenant')->transaction(function () use ($order, $request) {
                    $order->paid = (bool) $request->get('value');
                    $order->save();
                });
            }
        }

        return $bill;
    }

    /**
     * @param Request $request
     *
     * @return Bill
     */
    public function time_wait_edit($request): Bill
    {
        $bill = Bill::where('id', $request->get('pk'))->first();
        DB::connection('tenant')->transaction(function () use ($bill, $request) {
            if ($request->has('time_wait')) {
                $bill->time_wait = $request->get('time_wait');
            } else {
                if ($bill->time_wait) {
                    $time_wait = Carbon::parse($bill->time_wait)->format('Y-m-d');
                } else {
                    $time_wait = Carbon::now()->format('Y-m-d');
                }

                $bill->time_wait = $time_wait.' '.$request->get('value');
            }
            $bill->save();
        });

        return $bill;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkIfOpen(): bool
    {
        $website = \Hyn\Tenancy\Facades\TenancyFacade::website();
        $restaurant = Restaurant::where('hostname', $website->uuid)->first();

        return $restaurant->isOpened();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkPaymentOption($request)
    {
        if ($request->paidType == 'hotel_bill') {
            if (! ($request->roomDelivery || $request->deliveryType['type'] == 'delivery_room')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkDeliveryOption($request): bool
    {
        $locale = 'pl';
        $settings = Settings::where('key', 'rodzaje_dostawy')->first();
        if (! $settings) {
            return false;
        }
        $deliveryMethod = $request->deliveryType;
        if ($deliveryMethod) {
            switch ($deliveryMethod['type']) {
                case 'delivery_address':
                    return isset($settings->value_active['delivery_address']) && (bool) $settings->value_active['delivery_address'];
                case 'delivery_table':
                    return isset($settings->value_active['delivery_table']) && (bool) $settings->value_active['delivery_table'];
                case 'delivery_room':
                    return isset($settings->value_active['delivery_room']) && (bool) $settings->value_active['delivery_room'];
                case 'delivery_personal_pickup':
                    return isset($settings->value_active['delivery_personal_pickup']) && (bool) $settings->value_active['delivery_personal_pickup'];
                default:
                    return false;
            }
        }
        // if ($request->tableNumber) {                   // dostawa do stolika
        //     if (isset($settings->value_active[$locale]['delivery_table']) && !(bool)$settings->value_active[$locale]['delivery_table']) {
        //         return true;
        //     }
        // } elseif ($request->roomDelivery) {                   // dostawa do pokoju
        //     if (isset($settings->value_active[$locale]['delivery_room']) && !(bool)$settings->value_active[$locale]['delivery_room']) {
        //         return true;
        //     }
        // } elseif ($request->personalPickup) {                                             // odbior osobisty
        //     if (isset($settings->value_active[$locale]['delivery_personal_pickup']) && !(bool)$settings->value_active[$locale] ['delivery_personal_pickup']) {
        //         return true;
        //     }
        // } else {                                                                // dostawa pod adres
        //     if (isset($settings->value_active[$locale]['delivery_address']) && !(bool)$settings->value_active[$locale]['delivery_address']) {
        //         return true;
        //     }
        // }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkDeliveryRange($request): bool
    {
        $distance = self::getDistance($request);
        if ($distance === false) {
            return false;
        }
        $dr = DeliveryRange::where('range_from', '<=', $distance)->where('range_to', '>', $distance)->first();
        if ($dr) {
            return true;
        }

        return (bool) DeliveryRange::where('out_of_range', 1)->first();
    }

    /**
     * @param Request $request
     */
    public function getDistance($request)
    {
        $restaurant_lat_lng = Restaurant::getLatLngAddress();
        $restaurant_address = $restaurant_lat_lng[2];

        $address = $request->address ?? $request->deliveryType['value'];
        $address_bill = '';
        if (! empty($address['city'])) {
            $address_bill = $address['city'];
        }
        if (! empty($address['postcode'])) {
            $address_bill = str_replace('-', '', $address['postcode']).' '.$address_bill;
        }
        if (! empty($address['building_number'])) {
            $address_bill = $address['building_number'].' '.$address_bill;
        }
        if (! empty($address['street'])) {
            $address_bill = $address['street'].' '.$address_bill;
        }
        $distance = app(GoogleDistanceService::class)->calculate($restaurant_address, $address_bill);

        if ($distance != -1) {
            return $distance / 1000;
        } else {
            return false;
        }
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkOutRange($request): bool
    {
        $bool_range = Settings::getSetting('konfiguracja_dostawy', 'dostawa_poza_zasieg', true, false, true);
        if (! $bool_range) {
            return false;
        }
        $range = Settings::getSetting('konfiguracja_dostawy', 'zasieg_dostawy', true, false);
        if ($range) {
            $range = (float) $range;
        } else {
            return false;
        }
        $restaurant_lat_lng = Restaurant::getLatLngAddress();
        $lat = $restaurant_lat_lng[0];
        $lng = $restaurant_lat_lng[1];

        $address_bill = '';
        $lat_bill = null;
        $lng_bill = null;
        if (! empty($request->address['city'])) {
            $address_bill = $request->address['city'];
        }
        if (! empty($request->address['postcode'])) {
            $address_bill = $request->address['postcode'].' '.$address_bill;
        }
        if (! empty($request->address['street'])) {
            $address_bill = $request->address['street'].' '.$address_bill;
        }
        if (! empty($request->address['building_number'])) {
            $address_bill = $request->address['building_number'].' '.$address_bill;
        }
        if ($address_bill) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address_bill);

            if ($addressCoords) {
                $lat_bill = $addressCoords->getLat();
                $lng_bill = $addressCoords->getLng();
            }
        }

        $query = DB::table('restaurants')->selectRaw(DB::raw('check_distance(?,?,?,?,?) AS check_range'), [$lat, $lng, $lat_bill, $lng_bill, $range])->first();

        return (bool) $query->check_range;
    }

    public function checkAdditions(Request $request): bool
    {
        $orders = $request->get('orders', []);

        foreach ($orders as $order) {
            $additionGroups = $order['dish']['additionsGroups'] ?? $order['dish']['additions_groups'];
            if ($additionGroups) {
                foreach ($additionGroups as $additionGroup) {
                    $group = \DB::connection('tenant')->table('additions_groups')->find($additionGroup['addition_group']['id']);
                    if ($group) {
                        $additionGroup['mandatory'] = $group->mandatory;
                    } else {
                        return false;
                    }

                    if ($additionGroup['mandatory']) {
                        $faulty = false;
                        $additions = $additionGroup['additions'] ?? [];
                        foreach ($additions as $addition) {
                            if ($addition['quantity'] > 0) {
                                $faulty = false;
                                break;
                            }
                        }
                        if ($faulty) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param Bill $bill
     *
     * @return bool
     */
    public function checkDeliveryMinimumPrice($bill): bool
    {
        $distance = self::getDistance($bill);
        $dr = DeliveryRange::where('range_from', '<=', $distance)->where('range_to', '>', $distance)->first();
        if ($dr) {
            return ($bill->price - $bill->discount) < $dr->min_value ? false : true;
        }
        $dr_out_of_range = DeliveryRange::where('out_of_range', 1)->first();
        if ($dr_out_of_range) {
            return ($bill->price - $bill->discount) < $dr_out_of_range->min_value ? false : true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkNotActivatedAccount($request): bool
    {
        return (! auth()->user()->activated /*&& $request->paidType == 'cash'*/) ? true : false;
    }

    /**
     * @param Request $request
     * @param Bill $bill
     * @param Address $address
     *
     * @return float
     */
    public function getDeliveryPrice($request, $bill, $address): float
    {
        $distance = ceil(self::getDistance($request));
        $dr = DeliveryRange::where('range_from', '<=', $distance)->where('range_to', '>', $distance)->first();
        /*
         * Pobranie kosztow dostawy w przypadku zakresu
         */
        if ($dr) {
            $free_delivery_price = $dr->free_from;
            if ($free_delivery_price) {
                $free_delivery_price = number_format((float) $free_delivery_price, 2, '.', '');
            }
            /*
             * Platna dostawa
             */
            if (! $free_delivery_price || (($bill->price - $bill->discount) < $free_delivery_price)) {
                /*
                 * Koszt dostawy staly
                 */
                if ($dr->cost != 0.00) {
                    $delivery_price = number_format((float) ($dr->cost), 2, '.', '');

                    return $delivery_price;
                }
                /*
                 * Koszt dostawy za km
                 */
                if ($dr->km_cost != 0.00) {
                    $dr_previous = DeliveryRange::where('range_to', $dr->range_from)->first();
                    if ($dr_previous) {
                        $dr_previous_cost = self::getPreviousDeliveryPrice($dr_previous, null, $distance);
                        $delivery_price = number_format((float) ($dr_previous_cost + ($dr->km_cost * ($distance - $dr->range_from))), 2, '.', '');

                        return $delivery_price;
                    } else {
                        $delivery_price = number_format((float) ($dr->km_cost * $distance), 2, '.', '');

                        return $delivery_price;
                    }
                }
            } /*
             * Darmowa dostawa
             */ elseif ($free_delivery_price && ($bill->price - $bill->discount) > $free_delivery_price) {
                return 0;
            }
        }

        /*
         * Dostawa poza zasieg
         */
        $dr_out_of_range = DeliveryRange::where('out_of_range', 1)->first();
        if ($dr_out_of_range) {
            if ($dr_out_of_range->cost != 0.00) {
                return number_format((float) ($dr_out_of_range->cost), 2, '.', '');
            }
            if ($dr_out_of_range->km_cost != 0.00) {
                $dr_previous = DeliveryRange::where('range_to', $dr_out_of_range->range_from)->first();
                if ($dr_previous) {
                    $dr_previous_cost = self::getPreviousDeliveryPrice($dr_previous, null, $distance);
                    $dr_out_of_range->range_from = $dr_out_of_range->range_from == 0 ? 1 : $dr_out_of_range->range_from;

                    return number_format((float) ($dr_previous_cost + ($dr_out_of_range->km_cost * ($distance - $dr_out_of_range->range_from))), 2, '.', '');
                } else {
                    return number_format((float) ($dr_out_of_range->km_cost * $distance), 2, '.', '');
                }
            }
        }

        return 0;
    }

    public static function getPreviousDeliveryPrice(DeliveryRange $dr, $sum_price, $distance)
    {
        if ($dr->cost != 0.00) {
            $sum_price = number_format((float) ($dr->cost), 2, '.', '');
        }
        if ($dr->km_cost != 0.00) {
            $dr_previous = DeliveryRange::where('range_to', $dr->range_from)->first();
            if ($dr_previous) {
                $dr_previous_cost = self::getPreviousDeliveryPrice($dr_previous, $sum_price, $distance);
                $sum_price = number_format((float) ($dr_previous_cost + ($dr->km_cost * ($dr->range_to - $dr->range_from))), 2, '.', '');
            } else {
                $sum_price = number_format((float) ($dr->km_cost * ($dr->range_to - $dr->range_from)), 2, '.', '');
            }
        }

        return $sum_price;
    }

    /**
     * @param Bill $bill
     *
     * @return bool
     */
    public function checkProductStatuses($bill): bool
    {
        $changed = false;
        foreach ($bill->orders as $order) {
            if (! $order->dish) {
                return true;
            }
            foreach ($order->getAdditions() as $addition) {
                $addition_exist = Addition::where('id', $addition->id)->first();
                if (! $addition_exist) {
                    return true;
                }
            }
        }

        return $changed;
    }

    public function checkDeliveryType($request): bool
    {
        // dd($request->deliveryType);
        $noRoom = empty($request->roomDelivery);
        $noTable = empty($request->tableNumber);
        $noPersonalPickup = empty($request->personalPickup);
        $noAddress = empty($request->address);

        $deliveryType = $request->deliveryType;
        $noDeliveryType = empty($request->deliveryType);
        $wrongDeliveryType = false;
        if ($deliveryType) {
            switch ($deliveryType['type']) {
                case 'delivery_address':
                    $wrongDeliveryType = ! is_array($deliveryType['value']);
                    break;
                case 'delivery_table':
                    $wrongDeliveryType = ! is_numeric($deliveryType['value']);
                    break;
                case 'delivery_room':
                    $wrongDeliveryType = ! is_numeric($deliveryType['value']);
                    break;
                case 'delivery_personal_pickup':
                    ! is_bool($deliveryType['value']);
                    break;
                default:
                    $wrongDeliveryType = true;
                    break;
            }
        }

        if ($noRoom && $noTable && $noPersonalPickup && $noAddress && ($noDeliveryType || $wrongDeliveryType)) {
            return true;
        }

        return false;

        //     "deliveryType": {
        // 	"type": "delivery_personal_pickup",
        // 	"value": true
        // }

        dd('tutaj: '.$wrongDeliveryType);
        // 'delivery_address'
        // 'delivery_table'
        // 'delivery_room'
        // 'delivery_personal_pickup'
    }

    public function checkDeliveryAvailability($request): bool
    {
        $orders = $request->get('orders');
        foreach ($orders as $order) {
            $dish = Dish::find($order['dish']['id']);
            if (! ($dish && $dish['delivery'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Request $request
     * @param Bill $bill
     *
     * @return bool
     */
    public function checkPoints($request, $bill): bool
    {
        if ($bill->cart == 0 || ! $request->has('points') || $request->points == null || $request->points == 0) {
            return true;
        }
        $available_points = $bill->getAvailablePoints();

        return $available_points >= (float) $request->get('points') ? true : false;
    }

    /**
     * @param Bill $bill
     *
     * @return Bill
     */
    public function accept($bill): Bill
    {
        DB::connection('tenant')->transaction(function () use ($bill) {
            $bill->status = Bill::STATUS_ACCEPTED;
            $bill->save();
            $bill->fresh();
        });

        return $bill;
    }

    /**
     * @param Request $request
     * @param Bill $bill
     *
     * @return Bill
     */
    public function refund(Request $request, $bill, $amount): Bill
    {
        $bill = DB::connection('tenant')->transaction(function () use ($bill, $amount) {
            Refund::create([
                'payment_id' => $bill->payment->id,
                'bill_id' => $bill->id,
                'amount' => $amount,
                'status' => Refund::STATUS_TO_REFUNDED,
            ]);
            ($amount == ($bill->payment->p24_amount / 100)) ?
                $bill->update(['status' => Bill::STATUS_CANCELED])
                :
                $bill->update(['status' => Bill::STATUS_COMPLAINT]);

            return $bill->fresh();
        });

        return $bill;
    }

    public function getRefundAmount(Request $request, $bill)
    {
        $refund_amount = $request->get('refund_amount');
        switch ($request->get('refund_amount_type')) {
            case 0:
                $amount = $refund_amount;
                break;
            case 1:
            default:
                $amount = ($refund_amount * ($bill->payment->p24_amount / 100)) / 100;
                break;
        }

        return number_format($amount, 2, '.', '');
    }

    /**
     * @param Bill $bill
     *
     * @return Bill
     */
    public function ready($bill): Bill
    {
        DB::connection('tenant')->transaction(function () use ($bill) {
            $bill->status = Bill::STATUS_READY;
            $bill->paid = true;
            $bill->save();
            $bill->fresh();
        });

        return $bill;
    }

    public static function getTotalFoodPrice(BillDTO $billData): float
    {
        $totalPrice = 0;

        foreach ($billData->getOrders() as $orderFromRequest) {
            if (isset($orderFromRequest['dish']['id'])) {
                $dish = Dish::findOrFail($orderFromRequest['dish']['id']);
                $totalPrice += ($dish->price * (int) $orderFromRequest['quantity']) + self::getAdditionsPrice($orderFromRequest['additions']);
            } else {
                $bundle = Promotion::findOrFail($orderFromRequest['bundle']['id']);

                $totalPrice += ($bundle->value * (int) $orderFromRequest['quantity']) + $orderFromRequest['additionsPrice'];
            }
        }

        return MoneyFormatter::format($totalPrice);
    }

    public static function getTotalDiscount(BillDTO $billData): float
    {
        $discount = 0;
        $promotionForDish = null;

        foreach ($billData->getOrders() as $orderFromRequest) {

            if (isset($orderFromRequest['dish']['id'])) {
                $promotionForDish = PromotionHelper::getPromotionForDish(Dish::findOrFail($orderFromRequest['dish']['id']));
            }

            if ($promotionForDish !== null) {
                $discount += $promotionForDish['discount']['amount'] * $orderFromRequest[PlaceOrderRequest::QUANTITY_PARAM_KEY];
            }
        }

        return MoneyFormatter::format($discount);
    }

    public static function getPackageCost(BillDTO $billData): float
    {
        if (! in_array($billData->getDeliveryMethod(), [DeliveryMethod::DELIVERY_TO_ADDRESS, DeliveryMethod::PERSONAL_PICKUP])) {
            return 0;
        }

        $packagePrice = Settings::getSetting(self::PACKAGE_COST_SETTING_KEY, self::PACKAGE_COST_SETTING_KEY, true);

        if (empty($packagePrice)) {
            return 0;
        }

        $totalPackagePrice = 0;

        foreach ($billData->getOrders() as $orderFromRequest) {
            $totalPackagePrice += $orderFromRequest['quantity'] * $packagePrice;
        }

        return MoneyFormatter::format($totalPackagePrice);
    }

    public static function getServiceCharge(BillDTO $billData): float
    {
        if ($billData->getDeliveryMethod() !== DeliveryMethod::ROOM_DELIVERY) {
            return 0;
        }

        $serviceChargeConfiguration = Settings::where('key', self::SERVICE_CHARGE_SETTING_KEY)->first();

        if ($serviceChargeConfiguration === null) {
            throw new SettingNotFoundException(['missing_setting' => self::SERVICE_CHARGE_SETTING_KEY]);
        }

        if ($serviceChargeConfiguration->value_active[self::FIXED_VALUE_SERVICE_CHARGE_KEY]) {
            return MoneyFormatter::format($serviceChargeConfiguration->value[self::FIXED_VALUE_SERVICE_CHARGE_KEY]);
        }

        if ($serviceChargeConfiguration->value_active[self::PERCENT_SERVICE_CHARGE_KEY]) {
            return MoneyFormatter::format($billData->getTotalPrice() * (Str::replace(',', '.', $serviceChargeConfiguration->value[self::PERCENT_SERVICE_CHARGE_KEY]) / 100));
        }

        return 0;
    }

    protected static function getAdditionsPrice(array $additionsFromRequest): float
    {
        $totalAdditionsPrice = 0;

        foreach ($additionsFromRequest as $additionFromRequest) {
            $addition = Addition::findOrFail($additionFromRequest['id']);
            $totalAdditionsPrice += $addition->price * $additionFromRequest['quantity'];
        }

        return MoneyFormatter::format($totalAdditionsPrice);
    }
}
