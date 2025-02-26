<?php

namespace App\Models;

use App\Services\GeoServices\GeoService;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrWithdraw extends Model
{
    use ModelTrait;
    use UsesSystemConnection;

    protected $table = 'tr_withdrawals';

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'account_number',
        'restaurant_id',
        'status',
        'visibility',
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

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'tw_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset, array $search): Collection
    {
        $query = self::select();
        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }
        $address = '';
        $lat = null;
        $lng = null;
        if (! empty($criteria['city'])) {
            $address = $criteria['city'];
        }
        if (! empty($criteria['postcode'])) {
            $address = $criteria['postcode'].' '.$address;
        }
        if (! empty($criteria['street'])) {
            $address = $criteria['street'].' '.$address;
        }
        if ($address) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address);

            if ($addressCoords) {
                $lat = $addressCoords->getLat();
                $lng = $addressCoords->getLng();
            }
        }
        if (! empty($criteria['lat'])) {
            $lat = $criteria['lat'];
        }
        if (! empty($criteria['lng'])) {
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

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $column == 'distance' ? $query->orderBy(self::decamelize($column), $direction) : $query->orderBy('restaurants.'.self::decamelize($column), $direction);
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
                \DB::reconnect('tenant');
                $settings = \DB::connection('tenant')->table('settings')->where('key', 'konfiguracja_dostawy')->first();
                $value = json_decode($settings->value);
                $value_active = json_decode($settings->value_active);
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

                $settings = \DB::connection('tenant')->table('settings')->where('key', 'rodzaje_dostawy')->first();
                $value_active_delivery = json_decode($settings->value_active);
                $restaurants[$key]->is_delivery_active = (isset($value_active_delivery->$locale->delivery_address) && $value_active_delivery->$locale->delivery_address == 1) ? 1 : 0;

                if (! empty($search)) {
                    if (! empty($search['delivery_address'])) {
                        $value_active = json_decode($settings->value_active);
                        if (! (isset($value_active->$locale->delivery_address) && $value_active->$locale->delivery_address == $search['delivery_address'])) {
                            $restaurants->forget($key);
                        }
                        if ($search['delivery_address']) {
                            $dishes = \DB::connection('tenant')->table('dishes')->where('delivery', 1)->get();
                            if (! count($dishes)) {
                                $restaurants->forget($key);
                            }
                        }
                    }
                }
                \DB::purge('tenant');
            }

            return $restaurants;
        }

        return $query->get();
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param bool        $onlyGroup
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null, array $search = null): LengthAwarePaginator
    {
        $query = self::distinct()->select(['*']);
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        } else {
            $query->orderBy('name');
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'LIKE', '%'.$filter.'%')
                    ->orWhere('hostname', 'LIKE', '%'.$filter.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$filter.'%')
                    ->orWhere('provision', 'LIKE', '%'.$filter.'%');
            });
        }

        return $query->paginate($paginateSize, ['restaurants.*']);
    }
}
