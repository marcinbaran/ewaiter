<?php

namespace App\Repositories;

use App\Exceptions\RestaurantWithoutDeliveryException;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Settings;
use App\Services\GeoServices\GeoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class RestaurantRepository
{
    use MultiTentantRepositoryTrait;

    protected $model = Restaurant::class;

    protected $query;

    public function getRestaurantList(array $criteria, array $order, int $limit, int $offset, array $search, array $filters = [], bool $withCount = false): Collection
    {
        $this->initQuery();
        $this->filteringRestaurants($filters);
        $locale = $this->resolveLocale($criteria);
        $this->filterByVisibility($criteria);
        $this->filterByCoordinates($criteria);
        $this->applyOrder();

        $allRestaurants = $this->query->count();

        $this->applyPagination($criteria, $limit, $offset);
        if ($withCount) {
            return collect([
                'restaurants' => $this->hydrateRestaurants($locale, $search),
                'allRestaurants' => $allRestaurants,
            ]);
        }

        return $this->hydrateRestaurants($locale, $search);
    }

    private function filterByCoordinates(array $criteria)
    {
        $this->query->whereNotNull('max_delivery_range');

        if (isset($criteria['lat']) && !empty($criteria['lat']) && isset($criteria['lng']) && !empty($criteria['lng'])) {
            $point = [
                0 => $criteria['lat'],
                1 => $criteria['lng']
            ];
        } else {
            $point = $this->getCoords($criteria);
        }

        $latitude = $point[0];
        $longitude = $point[1];

        $this->query->whereRaw("
            ST_Contains(
                max_delivery_range,
                ST_GeomFromText(?)
            )
        ", ["POINT($latitude $longitude)"]);
    }

    public function initQuery()
    {
        $this->query = Restaurant::select();
    }

    public function filteringRestaurants(array $criteria)
    {
        $restaurants = Restaurant::all();
        $filters = [];
        foreach ($criteria as $key => $value) {
            if ($value == null) {
                continue;
            }
            $filters[$key] = [];
        }

        foreach ($restaurants as $restaurant) {
            if (!$restaurant->hasDishes()) {
                $this->query->where('restaurants.id', '!=', $restaurant->id);
                continue;
            }

            foreach ($criteria as $key => $value) {
                $methodName = 'filterBy' . $key;
                if ($value != null && method_exists($this, $methodName)) {
                    $result = $this->{$methodName}($restaurant, $value);

                    if ($result != null) {
                        $filters[$key][] = $this->{$methodName}($restaurant, $value);
                    }
                }
            }
        }

        foreach ($filters as $key => $value) {
            if ($criteria[$key] != null) {
                $this->query->whereIn('restaurants.id', $value);
            }
        }

    }

    public function applyPagination(array $criteria, int $limit, int $offset)
    {
        if (!$criteria['noLimit']) {
            $this->query->offset($offset)->limit($limit);
        }
    }

    public function applyOrder()
    {
        $this->query->orderByDesc('is_opened');
        $this->query->orderByRaw('minimal_delivery_price IS NULL, minimal_delivery_price ASC');
    }

    public function resolveLocale(array $criteria)
    {
        return $criteria['locale'] ?? config('app.locale');
    }

    public function filterByVisibility()
    {
        $this->query->where('visibility', true);
    }

    public function filterByTag(Restaurant $restaurant, $value)
    {
        return $restaurant->restaurantTags()->whereIn('key', explode(',', $value))->count() > 0 ? $restaurant->id : null;
    }

    public function getCoords(array $criteria)
    {
        $lat = null;
        $lng = null;
        $address = '';

        if (!empty($criteria['city'])) {
            $address = $criteria['city'];
        }
        if (!empty($criteria['postcode'])) {
            $address = $criteria['postcode'] . ' ' . $address;
        }
        if (!empty($criteria['street'])) {
            $address = $criteria['street'] . ' ' . $address;
        }

        if ($address) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address);

            if ($addressCoords) {
                $lat = $addressCoords->getLat();
                $lng = $addressCoords->getLng();
            } else {
                $lat = 0.1;
                $lng = 0.1;
            }
        }

        if (!empty($criteria['lat'])) {
            $lat = $criteria['lat'];
        }
        if (!empty($criteria['lng'])) {
            $lng = $criteria['lng'];
        }

        return [$lat, $lng];
    }

    private function hydrateRestaurants(string $locale, array $search)
    {
        $restaurants = $this->query->get();
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $key => $restaurant) {
            try {
                $this->hydrateRestaurant($restaurant, $locale, $search);
            } catch (RestaurantWithoutDeliveryException $e) {
                $restaurants->forget($key);
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                if ($e->getCode() == 404) {
                    continue;
                }
            }
        }

        return $restaurants;
    }

    private function hydrateRestaurant(Restaurant $restaurant, string $locale, array $search)
    {
        $this->reconnect($restaurant);
        $this->hydrateWithDeliverySettings($restaurant, $locale, $search);
        $this->hydrateRestaurantWithTags($restaurant, $locale);
        $this->hydrateRestaurantWithDeliveryConfig($restaurant);
        $this->hydrateRestaurantWithAttributes($restaurant);

        DB::purge('tenant');
    }

    private function hydrateWithDeliverySettings(Restaurant $restaurant, string $locale, array $search)
    {
        $restaurant->is_delivery_active = $restaurant->isDeliveryActive();
        $settings = Settings::query()->where('key', 'rodzaje_dostawy')->first();

        if (!empty($search)) {
            if (!empty($search['delivery_address'])) {
                $value_active = json_decode($settings->value_active);
                if (!(isset($value_active->delivery_address) && $value_active->delivery_address == $search['delivery_address'])) {
                    throw new RestaurantWithoutDeliveryException();
                }
                if (!Dish::where('delivery', 1)->count()) {
                    throw new RestaurantWithoutDeliveryException();
                }
            }
        }
    }

    private function hydrateRestaurantWithTags(Restaurant $restaurant, string $locale)
    {
        $restaurant->restaurantTags = RestaurantTag::getRows([
            'locale' => $locale,
            'restaurantId' => $restaurant->id,
        ]);
    }

    private function hydrateRestaurantWithDeliveryConfig(Restaurant $restaurant)
    {
        $settings = DB::connection('tenant')->table('settings')->where('key', 'konfiguracja_dostawy')->first();
        $value = json_decode($settings->value);
        $value_active = json_decode($settings->value_active);

        if (isset($value_active->minimana_wartosc) && $value_active->minimana_wartosc) {
            $restaurant->order_minimal_price = $value->minimana_wartosc;
        }

        if (isset($value_active->darmowa_dostawa_do_km) && $value_active->darmowa_dostawa_do_km) {
            $restaurant->order_lowest_delivery_price = 0.00;
        } elseif (isset($value_active->koszt_dostawy) && $value_active->koszt_dostawy) {
            $restaurant->order_lowest_delivery_price = $value->koszt_dostawy;
        } elseif (isset($value_active->dodatkowe_km_oplata) && $value_active->dodatkowe_km_oplata) {
            $restaurant->order_lowest_delivery_price = $value->dodatkowe_km_oplata;
        } else {
            $restaurant->order_lowest_delivery_price = 0.00;
        }
    }

    private function hydrateRestaurantWithAttributes(Restaurant $restaurant)
    {
        $restaurant->cache_attributes = $restaurant->getRestaurantAttributesGroups()->toArray(request());
    }

    private function filterByOpen(Restaurant $restaurant, $value)
    {
        return ($restaurant->isOpened() == filter_var($value, FILTER_VALIDATE_BOOLEAN)) ? $restaurant->id : null;
    }

    private function filterByTableBook(Restaurant $restaurant, $value)
    {
        return (bool)$restaurant->table_reservation_active ? $restaurant->id : null;
    }

    private function filterByPromotions(Restaurant $restaurant, $value)
    {
        return ($restaurant->hasPromotions() == filter_var($value, FILTER_VALIDATE_BOOLEAN)) ? $restaurant->id : null;
    }

    private function filterByFreeDelivery(Restaurant $restaurant, $value)
    {
//        dd($value==$restaurant->hasFreeDelivery());
        return ($value == $restaurant->hasFreeDelivery()) ? $restaurant->id : null;
    }

    private function filterByNews(Restaurant $restaurant, $value)
    {
        return ($restaurant->created_at >= Carbon::now()->subDays(14) == filter_var($value, FILTER_VALIDATE_BOOLEAN)) ? $restaurant->id : null;
    }

    private function filterByRestaurantTags(Restaurant $restaurant, $value)
    {
        foreach ($restaurant->restaurantTags->pluck('key') as $tag) {
            if (str_contains($value, $tag)) {
                return $restaurant->id;
            }
        }

        return null;
    }

    private function filterBySearch(Restaurant $restaurant, $value)
    {
        return ($restaurant->searchByName($value)) ? $restaurant->id : null;
    }


}
