<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\AddressSystem;
use App\Models\DeliveryRange;
use App\Models\Dish;
use App\Models\Resource;
use App\Models\Settings;
use App\Repositories\MultiTentantRepositoryTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *     schema="RestaurantResource",
 *     type="object",
 *     description="Restaurant resource schema",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="hostname",
 *         type="string",
 *         description="Hostname of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="open",
 *         type="boolean",
 *         description="Indicates whether the restaurant is open"
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date-time",
 *         description="Creation date of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="orderMinimalPrice",
 *         type="string",
 *         description="Minimum order value for the restaurant"
 *     ),
 *     @OA\Property(
 *         property="orderLowestDeliveryPrice",
 *         type="string",
 *         description="Lowest delivery price for the restaurant"
 *     ),
 *     @OA\Property(
 *         property="geolocalization",
 *         type="object",
 *         @OA\Property(property="lat", type="number", format="float"),
 *         @OA\Property(property="lng", type="number", format="float"),
 *         description="Geographical coordinates of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="distance",
 *         type="string",
 *         description="Distance from the user to the restaurant"
 *     ),
 *     @OA\Property(
 *         property="isDeliveryActive",
 *         type="boolean",
 *         description="Indicates whether delivery is active for the restaurant"
 *     ),
 *     @OA\Property(
 *         property="average_price",
 *         type="number",
 *         format="float",
 *         description="Average price of dishes in the restaurant"
 *     ),
 *     @OA\Property(
 *         property="restaurantTags",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RestaurantTagResource"),
 *         description="List of tags associated with the restaurant"
 *     ),
 *     @OA\Property(
 *         property="isPreview",
 *         type="boolean",
 *         description="Indicates whether the restaurant is in preview mode"
 *     ),
 *     @OA\Property(
 *         property="logo",
 *         type="string",
 *         description="URL of the restaurant's logo"
 *     ),
 *     @OA\Property(
 *         property="bg_image",
 *         type="string",
 *         description="URL of the restaurant's background image"
 *     ),
 *     @OA\Property(
 *         property="dish_default_image",
 *         type="string",
 *         description="URL of the default image for dishes"
 *     ),
 *     @OA\Property(
 *         property="settings",
 *         type="object",
 *         description="Settings related to the restaurant"
 *     ),
 *     @OA\Property(
 *         property="food_category",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FoodCategoryResource"),
 *         description="List of food categories available in the restaurant"
 *     ),
 *     @OA\Property(
 *         property="dishes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DishResource"),
 *         description="List of dishes available in the restaurant"
 *     ),
 *     @OA\Property(
 *         property="reviews",
 *         type="object",
 *         @OA\Property(property="averageFood", type="number", format="float"),
 *         @OA\Property(property="averageDelivery", type="number", format="float"),
 *         @OA\Property(property="countFood", type="integer"),
 *         @OA\Property(property="countDelivery", type="integer"),
 *         description="Reviews data for the restaurant"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="object",
 *         description="Address details of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="age_restricted",
 *         type="boolean",
 *         description="Indicates whether the restaurant has age restrictions"
 *     ),
 *     @OA\Property(
 *         property="visit_count",
 *         type="integer",
 *         description="Number of visits to the restaurant"
 *     )
 * )
 */
class RestaurantResource extends JsonResource
{
    use ResourceTrait;
    use MultiTentantRepositoryTrait;

    /**
     * @var int Default limit items per page
     */
    public const int LIMIT = 20;
    public const int DAYS_DISH_IS_NEW = 7;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $this->reconnect($this->resource);

        $basic = $request->get('names_only');
        $withoutSettings = $request->get('withoutSettings');
        $withoutTags = $request->get('withoutTags');
        $withoutAttributeGroups = $request->get('withoutAttributeGroups');
        $withoutAddress = $request->get('withoutAddress');
        $locale = $request->get('locale', env('APP_LOCALE'));
        $onlyNameAndHostname = $request->get('only_name_and_hostname');

        if ($onlyNameAndHostname) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'hostname' => $this->hostname,
            ];
        }

        if ($basic) {
            $array = [
                'id' => $this->id,
                'name' => $this->name,
                'hostname' => $this->hostname,
                'open' => (bool)$this->is_opened,
                'createdAt' => $this->dateFormat($this->created_at),
                'orderMinimalPrice' => $this->getMinOrderValue(),
                'orderLowestDeliveryPrice' => $this->getFirstDeliveryPrice(),
                'geolocalization' => [
                    'lat' => $this->address_system->lat,
                    'lng' => $this->address_system->lng,
                ],
                'distance' => number_format((float)$this->distance, 0, '.', ''),
                'isDeliveryActive' => (bool)$this->isDeliveryActive(),
                'average_price' => $this->average_price,
                'isPreview' => (bool)$this->is_preview,
                'logo' => $this->getLogoUrl(),
                'bg_image' => $this->getBgImageUrl(),
                'dish_default_image' => $this->getDishDefaultImageUrl(),
                'has_free_delivery' => $this->hasFreeDelivery(),
                'table_reservation_active' => (bool)$this->table_reservation_active,
            ];
        } else {
            $array = [
                'id' => $this->id,
                'name' => $this->name,
                'hostname' => $this->hostname,
                'open' => (bool)$this->is_opened,
                'createdAt' => $this->dateFormat($this->created_at),
                'orderMinimalPrice' => $this->getMinOrderValue(),
                'orderLowestDeliveryPrice' => $this->getFirstDeliveryPrice(),
                'lat' => $this->address_system->lat,
                'lng' => $this->address_system->lng,
                'distance' => number_format((float)$this->distance, 0, '.', ''),
                'isDeliveryActive' => (bool)$this->isDeliveryActive(),
                'average_price' => $this->average_price,
                'isPreview' => (bool)$this->is_preview,
                'logo' => $this->getLogoUrl(),
                'bg_image' => $this->getBgImageUrl(),
                'dish_default_image' => $this->getDishDefaultImageUrl(),
                'has_promotions' => (bool)$this->hasPromotions(),
                'has_new_dishes' => $this->hasNewDishes(),
                'table_reservation_active' => (bool)$this->table_reservation_active,
                'has_free_delivery' => $this->hasFreeDelivery(),
            ];

            if (!$this->withoutMenu($request)) {
                $array['food_category'] = $this->getRestaurantFoodCategories($locale);
                $array['dishes'] = $this->getRestaurantDishes($locale);
            }
        }

        if (!isset($withoutSettings) || $withoutSettings == false) {
            $array['settings'] = $this->getRestaurantSettings($this->locale);
        }

        if (!isset($withoutTags) || $withoutTags == false) {
            $array['restaurantTags'] = RestaurantTagResource::collection($this->restaurantTags);
        }

        if (!isset($withoutAttributeGroups) || $withoutAttributeGroups == false) {
            $array['attribute_groups'] = $this->cache_attributes ?? $this->getRestaurantAttributesGroups();
        }

        if (!isset($withoutAddress) || $withoutAddress == false) {
            $array['address'] = $this->getAddress();
        }

        if ($this->isWithReviews($request)) {
            $foodReviews = $this->reviews->whereNotNull('rating_food');
            $deliveryReviews = $this->reviews->whereNotNull('rating_delivery');
            $averageDelivery = null;

            if ($deliveryReviews->count() !== 0) {
                $averageDelivery = round($deliveryReviews->avg('rating_delivery'), 2);
            }

            $averageFood = round($foodReviews->avg('rating_food'), 2);
            $average = ($averageDelivery !== null) ? ($averageFood + $averageDelivery) / 2 : $averageFood;

            $array['reviews']['averageFood'] = $averageFood;
            $array['reviews']['averageDelivery'] = $averageDelivery;
            $array['reviews']['countFood'] = $foodReviews->count();
            $array['reviews']['countDelivery'] = $deliveryReviews->count();
            $array['reviews']['average'] = $average;
        }

        $array['age_restricted'] = (bool)$this->age_restricted;
        $array['visit_count'] = $this->getVisitCountForRestaurant($this->id);

        return $array;
    }

    public function getMinOrderValue(): string
    {
        $deliveryRange = DeliveryRange::first();

        if ($deliveryRange) {
            return $deliveryRange->min_value ? number_format((float)$deliveryRange->min_value, 2, '.', '') : '0.0';
        }

        return '0.0';
    }

    protected function getFirstDeliveryPrice(): string
    {
        $deliveryRange = DeliveryRange::first();

        if ($deliveryRange && $deliveryRange->cost) {
            return number_format((float)$deliveryRange->cost, 2, '.', '');
        }

        if ($deliveryRange && $deliveryRange->km_cost) {
            return number_format((float)$deliveryRange->cost, 2, '.', '');
        }

        return '0.0';
    }

    protected function getLogoUrl()
    {
        return $this->getImageUrlByType('logo');
    }

    protected function getImageUrlByType(string $type)
    {
        $logoSettings = Settings::query()->where('key', 'logo')->first();
        if (!$logoSettings instanceof Settings) {
            return '';
        }
        /** @var resource $logo */
        $logo = $logoSettings->getPhotoByType($type);
        if ($logo instanceof Resource) {
            return env('APP_URL') . str_replace('local', 'tenancy/' . $this->hostname, $logo->getPhoto(true));
        }

        return '';
    }

    protected function getBgImageUrl()
    {
        return $this->getImageUrlByType('bg_image');
    }

    protected function getDishDefaultImageUrl()
    {
        return $this->getImageUrlByType('dish_default_image');
    }

    private function hasNewDishes(): bool
    {
        $dateToDishNewest = Carbon::now()->subDays(self::DAYS_DISH_IS_NEW);

        return Dish::where('created_at', '>=', $dateToDishNewest)->count() > 0;
    }

    private function hasFreeDelivery(): bool
    {
        if (!$this->isDeliveryActive()) {
            return false;
        }

        $deliveryRanges = DeliveryRange::all();

        foreach ($deliveryRanges as $deliveryRange) {
            if ($deliveryRange->cost == 0 && $deliveryRange->km_cost == 0) {
                return true;
            }
        }

        return false;
    }

    protected function withoutMenu(Request $request): bool
    {
        return (int)$request->withoutMenu;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithReviews(Request $request): bool
    {
        return (int)$request->withReviews;
    }

    protected function getAddress()
    {
        $address = $this->address_system;

        if ($address === null) {
            foreach (App::make(AddressSystem::class)->getFillable() as $field) {
                $address[$field] = null;
            }

            return $address;
        }

        return new RestaurantAddressResource($address);
    }

    private function getVisitCountForRestaurant(int $id): int
    {
        return DB::table('visits')->where("restaurant_id", $id)->count();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithAddress(Request $request): bool
    {
        return (int)$request->withAddress;
    }
}
