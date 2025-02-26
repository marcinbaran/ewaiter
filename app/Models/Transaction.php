<?php

namespace App\Models;

use App\Services\GeoServices\GeoService;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use ModelTrait;
    use UsesSystemConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'restaurant_id',
        'withdrawal',
        'tw_id',
        'amount',
        'status',
        'visibility',
        'type',
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
     * @return HasOne
     */
    public function withdraw(): HasOne
    {
        return $this->hasOne(TrWithdrawal::class, 'id', 'tw_id');
    }

    /**
     * @return HasOne
     */
    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }

    /**
     * @return string
     */
    public function isWithdraw(): string
    {
        return $this->withdrawal ? 'Yes' : 'No';
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
        $query = self::distinct()->select('transactions.*')
            ->with('withdraw')
            ->with('restaurant')
            ->leftJoin('restaurants', 'restaurants.id', '=', 'transactions.restaurant_id')
            ->leftJoin('tr_withdrawals', 'tr_withdrawals.id', '=', 'transactions.tw_id')
            ->addSelect('restaurants.name as restaurants_name')
            ->addSelect('tr_withdrawals.created_at as withdrawal_at');
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
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
                $q->where('restaurants.name', 'LIKE', '%'.$filter.'%')
                    ->orWhere('transactions.created_at', 'LIKE', '%'.$filter.'%')
                    ->orWhere('transactions.amount', 'LIKE', '%'.$filter.'%')
                    ->orWhere('tr_withdrawals.created_at', 'LIKE', '%'.$filter.'%');
            });
        }

        return $query->paginate($paginateSize, ['transactions.*']);
    }
}
