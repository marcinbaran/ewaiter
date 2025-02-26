<?php

namespace App\Models;

use App\Helpers\PromotionHelper;
use App\Http\Helpers\SearchHelper;
use App\Http\Resources\Api\AttributeGroupResource;
use App\Http\Resources\Api\DishResource;
use App\Managers\WorktimeManager;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\GeoServices\GeoService;
use App\Services\GlobalSearch\Searchable;
use Bkwld\Croppa\Facades\Croppa;
use Carbon\Carbon;
use DateTime;
use DB;
use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * @OA\Schema(
 *     schema="Restaurant",
 *     type="object",
 *     title="Restaurant",
 *     description="Model representing a restaurant",
 *     required={"name", "hostname"},
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
 *         description="Hostname associated with the restaurant"
 *     ),
 *     @OA\Property(
 *         property="hostname_id",
 *         type="integer",
 *         description="ID of the associated hostname"
 *     ),
 *     @OA\Property(
 *         property="address_id",
 *         type="integer",
 *         description="ID of the associated address"
 *     ),
 *     @OA\Property(
 *         property="blurhash_logo",
 *         type="string",
 *         description="Blurhash code for the restaurant's logo"
 *     ),
 *     @OA\Property(
 *         property="blurhash_background",
 *         type="string",
 *         description="Blurhash code for the restaurant's background image"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         format="url",
 *         description="URL to the restaurant's photo"
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="boolean",
 *         description="Visibility status of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="provision",
 *         type="number",
 *         format="float",
 *         description="Provision rate for the restaurant"
 *     ),
 *     @OA\Property(
 *         property="provision_logged",
 *         type="number",
 *         format="float",
 *         description="Provision rate for logged-in users"
 *     ),
 *     @OA\Property(
 *         property="provision_unlogged",
 *         type="number",
 *         format="float",
 *         description="Provision rate for unlogged users"
 *     ),
 *     @OA\Property(
 *         property="account_number",
 *         type="string",
 *         description="Bank account number of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="subname",
 *         type="string",
 *         description="Subname of the restaurant"
 *     ),
 *     @OA\Property(
 *         property="order_minimal_price",
 *         type="number",
 *         format="float",
 *         description="Minimal order price"
 *     ),
 *     @OA\Property(
 *         property="distance",
 *         type="number",
 *         format="float",
 *         description="Distance to the restaurant in kilometers"
 *     ),
 *     @OA\Property(
 *         property="last_activity_request_date",
 *         type="string",
 *         format="date-time",
 *         description="Last activity request date"
 *     ),
 *     @OA\Property(
 *         property="table_reservation_active",
 *         type="boolean",
 *         description="Whether table reservation is active"
 *     ),
 *     @OA\Property(
 *         property="is_opened",
 *         type="boolean",
 *         description="Whether the restaurant is currently opened"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the restaurant was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the restaurant was last updated"
 *     ),
 *     @OA\Property(
 *         property="restaurantTags",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/RestaurantTag"),
 *         description="Tags associated with the restaurant"
 *     ),
 *     @OA\Property(
 *         property="settings",
 *         type="object",
 *         description="Settings associated with the restaurant",
 *         @OA\Property(
 *             property="key",
 *             type="string",
 *             description="Key for the setting"
 *         ),
 *         @OA\Property(
 *             property="value",
 *             type="string",
 *             description="Value for the setting"
 *         )
 *     )
 * )
 */
class Restaurant extends Model implements Searchable
{
    use MultiTentantRepositoryTrait,BroadcastsEvents;

    /**
     * @var array
     */
    protected static $dayOfWeek = [
        1 => 'poniedzialek',
        2 => 'wtorek',
        3 => 'sroda',
        4 => 'czwartek',
        5 => 'piatek',
        6 => 'sobota',
        0 => 'niedziela',
    ];

    use ModelTrait;
    use UsesSystemConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'hostname',
        'hostname_id',
        'address_id',
        'blurhash_logo',
        'blurhash_background',
        'description',
        'photo',
        'visibility',
        'provision',
        'provision_logged',
        'provision_unlogged',
        'account_number',
        'subname',
        'order_minimal_price',
        'minimal_delivery_price',
        'distance',
        'last_activity_request_date',
        'table_reservation_active',
        'is_opened',
        'max_delivery_range',
        'manager_email',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'visibility' => 0,
    ];

    private $foodCategories;

    public static function getSingleRestaurant($restaurant, $locale)
    {
        $result = new self();

        config(['database.connections.tenant.database' => $restaurant->hostname]);
        DB::reconnect('tenant');
        $settings = DB::connection('tenant')->table('settings')->where('key', 'konfiguracja_dostawy')->first();
        $value = json_decode($settings->value);
        $value_active = json_decode($settings->value_active);

        //
        // SETTINGS
        //
        $result->settings = Settings::getRowsLang($locale);

        //
        // TAGS
        //
        $tagsCriteria['locale'] = $locale;
        $tagsCriteria['restaurantId'] = $restaurant->id;
        $result->restaurantTags = RestaurantTag::getRows($tagsCriteria);

        /*
         * orderMinimalPrice
         */
        if (isset($value_active->$locale->minimana_wartosc) && $value_active->$locale->minimana_wartosc) {
            $result->order_minimal_price = $value->$locale->minimana_wartosc;
        }
        /*
         * orderLowestDeliveryPrice
         */
        if (isset($value_active->$locale->darmowa_dostawa_do_km) && $value_active->$locale->darmowa_dostawa_do_km) {
            $result->order_lowest_delivery_price = 0.00;
        } elseif (isset($value_active->$locale->koszt_dostawy) && $value_active->$locale->koszt_dostawy) {
            $result->order_lowest_delivery_price = $value->$locale->koszt_dostawy;
        } elseif (isset($value_active->$locale->dodatkowe_km_oplata) && $value_active->$locale->dodatkowe_km_oplata) {
            $result->order_lowest_delivery_price = $value->$locale->dodatkowe_km_oplata;
        } else {
            $result->order_lowest_delivery_price = 0.00;
        }

        $settings = DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first();
        $value_active_delivery = json_decode($settings->value_active);
        $result->is_delivery_active = (isset($value_active_delivery->$locale->delivery_address) && $value_active_delivery->$locale->delivery_address == 1) ? 1 : 0;

        if (!empty($search)) {
            if (!empty($search['delivery_address'])) {
                $value_active = json_decode($settings->value_active);
                if (!(isset($value_active->$locale->delivery_address) && $value_active->$locale->delivery_address == $search['delivery_address'])) {
                    $restaurants->forget($key);
                }
                if ($search['delivery_address']) {
                    $dishes = DB::connection('tenant')->table('dishes')->where('delivery', 1)->get();
                    if (!count($dishes)) {
                        $restaurants->forget($key);
                    }
                }
            }
        }
        DB::purge('tenant');

        return $result;
    }

    public static function getRows(array $criteria, array $order, int $limit, int $offset, array $search): Collection
    {
        $query = self::select();
        if (!$criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }
        $address = '';
        $lat = null;
        $lng = null;
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
            }
        }
        if (!empty($criteria['lat'])) {
            $lat = $criteria['lat'];
        }
        if (!empty($criteria['lng'])) {
            $lng = $criteria['lng'];
        }
        $locale = 'pl';
        if (!empty($criteria['locale'])) {
            $locale = $criteria['locale'];
        }

        if (null !== ($criteria['visibility'] ?? null)) {
            $query->where('visibility', '=', $criteria['visibility']);
        }

        if ($lat && $lng) {
            $query->whereHas('address', function ($q) use ($lat, $lng) {
                $q->whereNotNull('lat')
                    ->whereNotNull('lng')
                    ->whereNotNull('radius')
                    ->whereRaw('check_distance(?,?,lat,lng,radius)', [$lat, $lng]);
            });
            $query->leftJoin('addresses', 'addresses.id', '=', 'restaurants.address_id');
            $query->selectRaw('(ACOS( SIN(lat*PI()/180)*SIN(?*PI()/180) + COS(lat*PI()/180)*COS(?*PI()/180)*COS(?*PI()/180-lng*PI()/180) ) * 6371000) * 1.3 AS distance', [$lat, $lat, $lng]);
            $query->selectRaw('restaurants.*,addresses.lat,addresses.lng');
        }

        if (!empty($order)) {
            foreach ($order as $column => $direction) {
                $column == 'distance' ? $query->orderBy(self::decamelize($column), $direction) : $query->orderBy('restaurants.' . self::decamelize($column), $direction);
            }
        }
        $restaurants = $query->get();
        if ($restaurants) {
            foreach ($restaurants as $key => $restaurant) {
                // $restaurants[$key] = self::getSingleRestaurant($restaurant, $locale);
                $hostname = Hostname::query()->where('id', $restaurant->hostname_id)->first();
                if (!$hostname instanceof Hostname) {
                    continue;
                }

                config(['database.connections.tenant.database' => $restaurant->hostname]);
                DB::reconnect('tenant');
                $settings = DB::connection('tenant')->table('settings')->where('key', 'konfiguracja_dostawy')->first();
                $value = json_decode($settings->value);
                $value_active = json_decode($settings->value_active);

                // $food_category = \DB::connection('tenant')->
                //      select('select fc.id, fc.name, fc.description, fc.position, fc.parent_id from food_categories fc where fc.visibility = 1');
                // $restaurants[$key]->food_category = $food_category;

                //
                // SETTINGS
                //
                $restaurants[$key]->settings = Settings::getRowsLang($locale);

                //
                // TAGS
                //
                $tagsCriteria['locale'] = $locale;
                $tagsCriteria['restaurantId'] = $restaurants[$key]->id;
                $restaurants[$key]->restaurantTags = RestaurantTag::getRows($tagsCriteria);

                /*
                 * orderMinimalPrice
                 */
                if (isset($value_active->minimana_wartosc) && $value_active->minimana_wartosc) {
                    $restaurants[$key]->order_minimal_price = $value->minimana_wartosc;
                }
                /*
                 * orderLowestDeliveryPrice
                 */
                if (isset($value_active->darmowa_dostawa_do_km) && $value_active->darmowa_dostawa_do_km) {
                    $restaurants[$key]->order_lowest_delivery_price = 0.00;
                } elseif (isset($value_active->koszt_dostawy) && $value_active->koszt_dostawy) {
                    $restaurants[$key]->order_lowest_delivery_price = $value->koszt_dostawy;
                } elseif (isset($value_active->dodatkowe_km_oplata) && $value_active->dodatkowe_km_oplata) {
                    $restaurants[$key]->order_lowest_delivery_price = $value->dodatkowe_km_oplata;
                } else {
                    $restaurants[$key]->order_lowest_delivery_price = 0.00;
                }

                $settings = DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first();
                $value_active_delivery = json_decode($settings->value_active);
                $restaurants[$key]->is_delivery_active = (isset($value_active_delivery->delivery_address) && $value_active_delivery->delivery_address == 1) ? 1 : 0;

                if (!empty($search)) {
                    if (!empty($search['delivery_address'])) {
                        $value_active = json_decode($settings->value_active);
                        if (!(isset($value_active->delivery_address) && $value_active->delivery_address == $search['delivery_address'])) {
                            $restaurants->forget($key);
                        }
                        if ($search['delivery_address']) {
                            $dishes = DB::connection('tenant')->table('dishes')->where('delivery', 1)->get();
                            if (!count($dishes)) {
                                $restaurants->forget($key);
                            }
                        }
                    }
                }
                DB::purge('tenant');
            }

            return $restaurants;
        }

        return $query->get();
    }

    /**
     * @param string|null $filter
     * @param int $paginateSize
     * @param bool $onlyGroup
     * @param array $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null, array $search = null): LengthAwarePaginator
    {
        $query = self::distinct()->select(['*']);
        if (!empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        } else {
            $query->orderBy('name');
        }

        if (!empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (!empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere('subname', 'LIKE', '%' . $filter . '%')
                    ->orWhere('hostname', 'LIKE', '%' . $filter . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $filter . '%')
                    ->orWhere('provision', 'LIKE', '%' . $filter . '%');
            });
        }

        return $query->paginate($paginateSize, ['restaurants.*']);
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $check_active
     * @param bool $lang
     */
    public static function getLatLngAddress($restaurant = null)
    {
        $website = TenancyFacade::website();
        $restaurant = self::where('hostname', $website?->uuid)->first();
        if (!$restaurant) {
            return [null, null, null];
        }

        $address = '';
        $lat = null;
        $lng = null;
        if (!empty($restaurant->address_system->postcode)) {
            $address = str_replace('-', '', $restaurant->address_system->postcode) . ' ' . $address;
        }
        if (!empty($restaurant->address_system->city)) {
            $address = $restaurant->address_system->city;
        }
        if (!empty($restaurant->address_system->street)) {
            $address = $restaurant->address_system->street . ' ' . $address;
        }
        if (!empty($restaurant->address_system->building_number)) {
            $address = $restaurant->address_system->building_number . ' ' . $address;
        }
        if ($address) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address);

            if ($addressCoords) {
                $lat = $addressCoords->getLat();
                $lng = $addressCoords->getLng();
            }
        }

        return [$lat, $lng, $address];
    }

    public static function getRowsFast(array $criteria, array $order, int $limit, int $offset, array $search): Collection
    {
        $query = self::select();
        if (!$criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }
        $address = '';
        $lat = null;
        $lng = null;
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
            }
        }
        if (!empty($criteria['lat'])) {
            $lat = $criteria['lat'];
        }
        if (!empty($criteria['lng'])) {
            $lng = $criteria['lng'];
        }

        if (null !== ($criteria['visibility'] ?? null)) {
            $query->where('visibility', '=', $criteria['visibility']);
        }

        if ($lat && $lng) {
            $query->whereHas('address', function ($q) use ($lat, $lng) {
                $q->whereNotNull('lat')
                    ->whereNotNull('lng')
                    ->whereNotNull('radius')
                    ->whereRaw('check_distance(?,?,lat,lng,radius)', [$lat, $lng]);
            });
            $query->leftJoin('addresses', 'addresses.id', '=', 'restaurants.address_id');
            $query->selectRaw('(ACOS( SIN(lat*PI()/180)*SIN(?*PI()/180) + COS(lat*PI()/180)*COS(?*PI()/180)*COS(?*PI()/180-lng*PI()/180) ) * 6371000) * 1.3 AS distance', [$lat, $lat, $lng]);
            $query->selectRaw('restaurants.*,addresses.lat,addresses.lng');
        }

        if (!empty($order)) {
            foreach ($order as $column => $direction) {
                $column == 'distance' ? $query->orderBy(self::decamelize($column), $direction) : $query->orderBy('restaurants.' . self::decamelize($column), $direction);
            }
        }
        $restaurants = $query->get();
        if ($restaurants) {
            $locale = 'pl';
            if (app()->getLocale()) {
                $locale = app()->getLocale();
            }
            foreach ($restaurants as $key => $restaurant) {
                config(['database.connections.tenant.database' => $restaurant->hostname]);
                DB::reconnect('tenant');
                $settings = DB::connection('tenant')->table('settings')->where('key', 'konfiguracja_dostawy')->first();
                $value = json_decode($settings->value);
                $value_active = json_decode($settings->value_active);

                $food_category = json_decode(DB::connection('tenant')->table('food_categories')->where('visibility', '1')->get());
                $restaurants[$key]->food_category = $food_category;

                //$restaurants[$key]->settings = Settings::select()->get();
                $restaurants[$key]->settings = Settings::getRowsLang();

                //		$dishes = \DB::connection('tenant')
                //			->select('select d.id, d.food_category_id, d.name, d.description, d.price, d.time_wait, d.delivery, r.filename, p.type_value, p.value from dishes d left join promotions p on p.order_dish_id=d.id left join resources r on r.resourcetable_id=d.id where (p.active=1 or p.active is null) and (resourcetable_type = "dishes" or resourcetable_type is null) and d.deleted_at is null and d.visibility = 1 order by d.id');
                // $dishesB = Dish::select()->get(); //->with('additions_groups_dishes')->get();
                // $proba = DishResource::collection($dishes);
                //		$restaurants[$key]->dishes = $dishes;

                /*
                 * orderMinimalPrice
                 */
                if (isset($value_active->$locale->minimana_wartosc) && $value_active->$locale->minimana_wartosc) {
                    $restaurants[$key]->order_minimal_price = $value->$locale->minimana_wartosc;
                }
                /*
                 * orderLowestDeliveryPrice
                 */
                if (isset($value_active->$locale->darmowa_dostawa_do_km) && $value_active->$locale->darmowa_dostawa_do_km) {
                    $restaurants[$key]->order_lowest_delivery_price = 0.00;
                } elseif (isset($value_active->$locale->koszt_dostawy) && $value_active->$locale->koszt_dostawy) {
                    $restaurants[$key]->order_lowest_delivery_price = $value->$locale->koszt_dostawy;
                } elseif (isset($value_active->$locale->dodatkowe_km_oplata) && $value_active->$locale->dodatkowe_km_oplata) {
                    $restaurants[$key]->order_lowest_delivery_price = $value->$locale->dodatkowe_km_oplata;
                } else {
                    $restaurants[$key]->order_lowest_delivery_price = 0.00;
                }

                $settings = DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first();
                $value_active_delivery = json_decode($settings->value_active);
                $restaurants[$key]->is_delivery_active = (isset($value_active_delivery->$locale->delivery_address) && $value_active_delivery->$locale->delivery_address == 1) ? 1 : 0;

                if (!empty($search)) {
                    if (!empty($search['delivery_address'])) {
                        $value_active = json_decode($settings->value_active);
                        if (!(isset($value_active->$locale->delivery_address) && $value_active->$locale->delivery_address == $search['delivery_address'])) {
                            $restaurants->forget($key);
                        }
                        if ($search['delivery_address']) {
                            $dishes = DB::connection('tenant')->table('dishes')->where('delivery', 1)->get();
                            if (!count($dishes)) {
                                $restaurants->forget($key);
                            }
                        }
                    }
                }
                DB::purge('tenant');
            }

            return $restaurants;
        }

        return $query->get();
    }

    public static function findForPhrase(string $phrase = ''): Collection
    {
        if (TenancyFacade::website()) {
            return new Collection([]);
        }

        return self::query()
            ->where('name', 'like', '%' . $phrase . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getSearchGroupName(): string
    {
        return __('admin.Restaurants');
    }

    public static function getCurrentRestaurant(): ?self
    {
        $restaurantUUID = TenancyFacade::website()?->uuid;

        return $restaurantUUID ? self::where('hostname', $restaurantUUID)->first() : null;
    }

    /**
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'restaurant_id', 'id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'restaurant_id', 'id')->whereNull('deleted_at');
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'restaurant_id', 'id');
    }

    public function tpayMerchant(): HasOne
    {
        return $this->hasOne(TpayMerchant::class, 'restaurant_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function address(): HasOne
    {
        $website = TenancyFacade::website();
        if ($website) {
            return $this->hasOne(Address::class, 'id', 'address_id');
        } else {
            return $this->hasOne(AddressSystem::class, 'id', 'address_id');
        }
    }

    /**
     * @return HasOne
     */
    public function address_system(): HasOne
    {
        return $this->hasOne(AddressSystem::class, 'id', 'address_id');
    }

    public function getFormattedAddress(): string
    {
        $address = $this->address_system;

        if ($address) {
            return $address->house_number ?
                $address->city . ' ' . $address->postcode . ' ' . $address->street . ' ' . $address->building_number . '/' . $address->house_number
                : $address->city . ' ' . $address->postcode . ' ' . $address->street . ' ' . $address->building_number;
        }
        return '';
    }

    /**
     * @return MorphOne
     */
    public function photo(): MorphOne
    {
        return $this->morphOne(ResourceSystem::class, 'resourcetable');
    }

    public function getDefaultDishPhoto()
    {
        $logoSetting = Settings::where('key', 'logo')->first();

        return $logoSetting->photos()->where('additional', 'LIKE', '%dish_default_image%')->first();
    }

    public function restaurant_tags(): HasMany
    {
        return $this->hasMany(RestaurantsRestaurantTag::class, 'restaurant_id', 'id');
    }

    public function restaurantTags()
    {
        return $this->belongsToMany(RestaurantTag::class, 'restaurants_restaurant_tags', 'restaurant_id', 'tag_id');
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }

    public function online_payment_provider_account(): HasOne
    {
        return $this->HasOne(OnlinePaymentProviderAccount::class);
    }

    public function getRestaurantSettings($locale)
    {
        return Settings::getRowsLang($locale);
    }

    public function isOpened()
    {
        $this->reconnect($this);
        $isOpened = Restaurant::whereId($this->id)->where('is_opened', 1)->exists();
        return $isOpened;
//        $this->reconnect($this);
//        $d = date('Y-m-d');
//
//        foreach (Worktime::todayAndYesterdayActiveWorkTimes()->get() as $forcedWorkTime) {
//            $forcedWorkTimeDateTime = DateTimeHelper::getDateTimeRangeFromTimeStrings($forcedWorkTime->start, $forcedWorkTime->end, $forcedWorkTime->date);
//            $forcedWorkTimeIsNow = Carbon::now()->between($forcedWorkTimeDateTime['start'], $forcedWorkTimeDateTime['end']->addMinute());
//
//            if ($forcedWorkTime->type == Worktime::TYPE_CLOSED && $forcedWorkTimeIsNow) {
//                return false;
//            }
//
//            if ($forcedWorkTime->type == Worktime::TYPE_OPENED && $forcedWorkTimeIsNow) {
//                return true;
//            }
//
//            DB::purge('tenant');
//        }
//
//        $day_number = Carbon::parse(date('Y-m-d'))->dayOfWeek;
//        $day_name = self::$dayOfWeek[$day_number];
//        $settings = DB::connection('tenant')->table('settings')->where('key', 'czas_pracy')->first();
//        if (!$settings) {
//            DB::purge('tenant');
//
//            return false;
//        }
//        $settings->value = json_decode($settings->value, 2);
//        $settings->value_active = json_decode($settings->value_active, 2);
//        $worktime_settings = null;
//        if (isset($settings->value[$day_name]) && $settings->value[$day_name]) {
//            $worktime_settings = $settings->value[$day_name];
//        }
//        if (isset($settings->value_active[$day_name]) && !$settings->value_active[$day_name]) {
//            return false;
//        }
//        if (!isset($worktime_settings)) {
//            DB::purge('tenant');
//
//            return false;
//        }
//        $worktime_settings = explode('-', $worktime_settings);
//        $worktime_settings_start = $worktime_settings[0];
//        $worktime_settings_end = $worktime_settings[1];
//        $compare = Carbon::parse($worktime_settings_start)->lessThan(Carbon::parse($worktime_settings_end));
//        if ($compare) {
//            $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d . ' ' . $worktime_settings_start . ':00'))->format('Y-m-d H:i:s');
//            $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d . ' ' . $worktime_settings_end . ':00'))->format('Y-m-d H:i:s');
//        } else {
//            $d_tomorrow = Carbon::parse($d)->addDay()->format('Y-m-d');
//            $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d . ' ' . $worktime_settings_start . ':00'))->format('Y-m-d H:i:s');
//            $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d_tomorrow . ' ' . $worktime_settings_end . ':00'))->format('Y-m-d H:i:s');
//        }
//        DB::purge('tenant');
//
//        return Carbon::now()->between(Carbon::parse($worktime_settings_start), Carbon::parse($worktime_settings_end)) ? true : false;
    }

    public function isDeliverySuspended(): bool
    {
        $todayWorkTime = App::make(WorktimeManager::class)->getTodayWorktime();

        if ($todayWorkTime === null) {
            return true;
        }

        $now = Carbon::now();

        $timeStringBeforeSuspendingAddressDelivery = Settings::getSetting(
            Settings::TIME_BEFORE_SUSPENDING_ADDRESS_DELIVERY_SETTING_KEY,
            Settings::TIME_BEFORE_SUSPENDING_ADDRESS_DELIVERY_VALUE_KEY,
            true
        );

        $isRestaurantClosed = $now < $todayWorkTime['start'] || $now > $todayWorkTime['end'];

        if ($isRestaurantClosed) {
            return true;
        }

        if ($timeStringBeforeSuspendingAddressDelivery === null && !$isRestaurantClosed) {
            return false;
        }

        $timeBeforeSuspendingAddressDelivery = Carbon::parse($timeStringBeforeSuspendingAddressDelivery);

        $dateTimeBeforeDeliveryIsSuspended = $todayWorkTime['end']
            ->subHours($timeBeforeSuspendingAddressDelivery->hour)
            ->subMinutes($timeBeforeSuspendingAddressDelivery->minute);

        return Carbon::now()->isAfter($dateTimeBeforeDeliveryIsSuspended);
    }

    public function hasDishes()
    {
        $this->reconnect($this);

        $countDishes = Dish::visible()->count();

        return $countDishes > 0;
    }

    /**
     * @param $locale
     * @return array
     * @deprecated use DishesRepository instead
     */
    public function getRestaurantDishes($locale = 'pl')
    {
        $allCategories = FoodCategory::orderBy('parent_id', 'asc')->orderBy('position', 'asc')->get();
        $allDishes = Dish::where('deleted_at', null)->with('category')->with('photos')->with('promotions')->orderBy('position')->get();
        $allAvailabilities = Availability::get();

        foreach ($allAvailabilities as $availability) {
            if ($currentDishId = $availability->dish_id) {
                self::setVisibility($allDishes, $currentDishId, $availability);
            } elseif ($currentFoodCategoryId = $availability->food_category_id) {
                self::setVisibility($allCategories, $currentFoodCategoryId, $availability);
            }
        }

        for ($i = 0; $i < $allCategories->count(); $i++) {
            if ($parentId = $allCategories[$i]->parent_id) {
                $parent = $allCategories->first(function ($value, $key) use ($parentId) {
                    return $value->id == $parentId;
                });

                $allCategories[$i]->visibility &= $parent ? $parent->visibility : 0;
            }
        }

        foreach ($allDishes as $currentDish) {
            if ($foodCategoryId = $currentDish->food_category_id) {
                $foodCategory = $allCategories->first(function ($value, $key) use ($foodCategoryId) {
                    return $value->id == $foodCategoryId;
                });
                $currentDish->visibility &= $foodCategory ? $foodCategory->visibility : false;
            }
        }

        $newDishes = $allDishes->where('visibility', true);

        $dishes = [];

        foreach ($newDishes as $newDish) {
//            $dish = DishHelper::getDishObject($newDish, $locale, true, true, true, true); // leave it just in case
            $dish = new DishResource($newDish);
            if (!empty($dish['promotion']) && !PromotionHelper::isPromotionValid($dish['promotion']['price']['discounted'])) {
                continue;
            }

            $dish['delivery'] = $newDish->hasDelivery();

            $dishes[] = $dish;
        }

        $result = [];
        foreach ($dishes as $dish) {
            if (in_array($dish['food_category_id'], $this->foodCategories)) {
                $result[] = $dish;
            }
        }

        return $result;
    }

    private static function setVisibility($allItems, $currentItemId, $availability)
    {
        $now = Carbon::now();
        $start = $availability->start_hour ?
            Carbon::createFromTimeString($availability->start_hour) :
            Carbon::today();
        $end = $availability->end_hour ?
            Carbon::createFromTimeString($availability->end_hour) :
            Carbon::today()->addDay();

        $weekdaysArray = [
            0 => 's', // Sunday
            1 => 'm', // Monday
            2 => 't', // Tuesday
            3 => 'w', // Wednesday
            4 => 'r', // Thursday
            5 => 'f', // Friday
            6 => 'u',  // Saturday
        ];
        $todayWeekdayNumeric = $now->weekday();

        $currentItem = $allItems->first(function ($value, $key) use ($currentItemId) {
            return $value->id == $currentItemId;
        });
        if ($currentItem) {
            $todayDayOfWeek = $weekdaysArray[$todayWeekdayNumeric];
            $currentItem->visibility &= $now->between($start, $end) && $availability->$todayDayOfWeek;
        }
    }

    public function getRestaurantFoodCategories($locale)
    {
        $categories = FoodCategory::all();
        $translatedCategories = [];
        $fallbackLocale = config('app.fallback_locale');
        /** @var FoodCategory $category */
        foreach ($categories as $category) {
            $translatedCategory = [...$category->toArray()];
            $translatedCategory['name'] = $category->getTranslation('name', $locale) ?? $category->getTranslation('name', $fallbackLocale) ?? $category->name;
            $translatedCategory['description'] = $category->getTranslation('description', $locale) ?? $category->getTranslation('description', $fallbackLocale) ?? $category->description;
            $translatedCategory['name_translation'] = $category->getTranslation('name', $locale) ?? $category->getTranslation('name', $fallbackLocale) ?? $category->name;
            $translatedCategory['description_translation'] = $category->getTranslation('description', $locale) ?? $category->getTranslation('description', $fallbackLocale) ?? $category->description;

            $translatedCategories[] = $translatedCategory;
            $photo = DB::connection('tenant')->select('select * from resources where resourcetable_type = "food_categories" and resourcetable_id = ' . $category->id);
            $category->photo = $photo[0]?->filename ?? '';
        }

        $this->foodCategories = $categories->pluck('id', 'id')->toArray();

        return $translatedCategories;
    }

    public function getRestaurantAttributesGroups()
    {
        $existsPrimaryGroup = AttributeGroup::where('is_primary', true)->exists();
        if (!$existsPrimaryGroup) {
            return collect([]);
        }

        return AttributeGroupResource::collection(AttributeGroup::with(['attributes'])->active()->notEmpty()->get());
    }

    public function getOrdersCount(?DateTime $start, ?DateTime $end)
    {
        return Cache::remember('restaurant_' . $this->name . '_getOrdersCount', 60 * 60 * 6, function () use ($start, $end) {
            try {
                $prevTenant = config('database.connections.tenant.database');

                /** @var Hostname $hostname */
                $hostname = Hostname::query()->where('id', $this->hostname_id)->first();
                app(Connection::class)->set($hostname);

                $q = Bill::query()->where('id', '>', 0);
                if ($start && $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }

                config(['database.connections.tenant.database' => $prevTenant]);
                DB::reconnect('system');
                DB::reconnect('tenant');

                return $q->count();
            } catch (Throwable $e) {
                return 0;
            }
        });
    }

    public function getDishesCount()
    {
        return Cache::remember('restaurant_' . $this->name . '_getDishesCount', 60 * 60 * 6, function () {
            try {
                $prevTenant = config('database.connections.tenant.database');

                /** @var Hostname $hostname */
                $hostname = Hostname::query()->where('id', $this->hostname_id)->first();
                app(Connection::class)->set($hostname);

                $q = Dish::query()->where('id', '>', 0);

                $count = $q->count();
                config(['database.connections.tenant.database' => $prevTenant]);
                DB::reconnect('system');

                return $count;
            } catch (Throwable $e) {
                return 0;
            }
        });
    }

    public function getPhotosJsonAttribute()
    {
        $array = [];
        if ($this->photo) {
            $array[] = [
                'source' => $this->photo->id,
                'options' => [
                    'type' => 'local',
                ],
            ];
        }

        return json_encode($array);
    }

    public function getSearchUrl(): string
    {
        return route('admin.restaurants.edit', ['restaurant' => $this->id]);
    }

    public function getSearchTitle(): string
    {
        return $this->name;
    }

    public function getSearchDescription(): string
    {
        return $this->hostname . '.' . env('TENANCY_DEFAULT_HOSTNAME' ?? 'localhost');
    }

    public function getSearchPhoto(): string
    {
        return Croppa::url($this->photo->getPhoto(false), 90, 50);
    }

    public function scopeVisible(Builder $builder): void
    {
        $builder->where('visibility', 1);
    }

    public function isTableReservationAvailable()
    {
        $this->reconnect($this);
        $count = DB::connection('tenant')->table('qr_codes')->where('object_type', 'table')->count();

        return $count > 0;
    }

    public function hasPromotions()
    {
        $this->reconnect($this);
        $currentDate = Carbon::now();
        $count = DB::connection('tenant')
            ->table('promotions')
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', $currentDate);
            })
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', $currentDate);
            })
            ->where('active', 1)
            ->count();

        return $count > 0;
    }

    public function hasFreeDelivery()
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

    public function searchByName(string $search)
    {
        $this->reconnect($this);

        $search = strtolower($search);
        $search = SearchHelper::replacePolishLetters($search);

        $restaurantName = strtolower($this->name);
        $restaurantName = SearchHelper::replacePolishLetters($restaurantName);

        $isInRestaurantName = str_contains($restaurantName, $search);

        return $isInRestaurantName || (Dish::whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.pl'))) LIKE ?", ['%' . strtolower($search) . '%'])->count() > 0);
    }

    public function getAveragePrice()
    {
        return Dish::whereNull('deleted_at')->get()->avg('price');
    }

    public function isDeliveryActive()
    {
        $settings = \DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first()?\DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first():\DB::connection('tenant')->table('settings')->where('key', 'delivery_types')->first(); // TODO FIX OLD WHEN SETTINGS KEYS CHANGES rodzaje_dostawy / delivery_types
        $value_active_delivery = json_decode($settings->value_active);
        $deliveryRange = DeliveryRange::all();

        return (isset($value_active_delivery->delivery_address) && $value_active_delivery->delivery_address == 1 && count($deliveryRange) > 0) ? 1 : 0;
    }
    public function broadcastOn(string $event):array
    {
        return match ($event) {
            'updated' => ['restaurant-updated'],
            default => []
        };
    }

    public static function getRestaurantEmail(): string
    {
        $settings = \DB::connection('tenant')->table('settings')->where('key', 'kontakt')->first();

        $json_settings = json_decode($settings->value, true);
        $restaurantEmail = $json_settings['email'];

        return $restaurantEmail;
    }
}
