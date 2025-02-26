<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrWithdrawal extends Model
{
    use ModelTrait;
    use UsesSystemConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'account_number',
        'restaurant_id',
        'status',
        'visibility',
        'pack_id',
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

        if (null !== ($criteria['visibility'] ?? null)) {
            $query->where('visibility', '=', $criteria['visibility']);
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $column == 'distance' ? $query->orderBy(self::decamelize($column), $direction) : $query->orderBy('restaurants.'.self::decamelize($column), $direction);
            }
        }

        return $query;
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
        $query = self::distinct()->select('tr_withdrawals.*')
            ->with('restaurant')
            ->leftJoin('restaurants', 'restaurants.id', '=', 'tr_withdrawals.restaurant_id')
            ->addSelect('restaurants.name as restaurants_name');
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
                    ->orWhere('tr_withdrawals.created_at', 'LIKE', '%'.$filter.'%')
                    ->orWhere('tr_withdrawals.account_number', 'LIKE', '%'.$filter.'%')
                    ->orWhere('tr_withdrawals.amount', 'LIKE', '%'.$filter.'%');
            });
        }

        return $query->paginate($paginateSize, ['tr_withdrawals.*']);
    }
}
