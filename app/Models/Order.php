<?php

namespace App\Models;

use App\Repositories\MultiTentantRepositoryTrait;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use ModelTrait,
        Notifiable,
        UsesTenantConnection,
        MultiTentantRepositoryTrait;

    /**
     * @var int
     */
    public const STATUS_NEW = 0;

    /**
     * @var int
     */
    public const STATUS_ACCEPTED = 1;

    /**
     * @var int
     */
    public const STATUS_READY = 2;

    /**
     * @var int
     */
    public const STATUS_RELEASED = 3;

    /**
     * @var int
     */
    public const STATUS_CANCELED = 4;

    /**
     * @var int
     */
    public const STATUS_COMPLAINT = 5;

    /**
     * @var bool
     */
    public const PAID_NO = false;

    /**
     * @var bool
     */
    public const PAID_YES = true;

    /**
     * @var array
     */
    protected $fillable = [
        'quantity',
        'item_id',
        'item_name',
        'price',
        'status',
        'paid',
        'table_id',
        'bill_id',
        'tax',
        'discount',
        'customize',
        'accepted_at',
        'released_at',
        'user_id',
        'additions_price',
        'package_price',
        'type',
        'products_in_order',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'customize',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'customize' => 'array',
        'products_in_order' => 'array',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'price' => 0,
        'additions_price' => 0,
        'package_price' => 0,
        'status' => self::STATUS_NEW,
        'paid' => self::PAID_NO,
        'discount' => 0,
        'type' => 'dish',
    ];

    protected static $statusName = [
        self::STATUS_NEW => 'new',
        self::STATUS_ACCEPTED => 'accepted',
        self::STATUS_READY => 'ready',
        self::STATUS_RELEASED => 'released',
        self::STATUS_CANCELED => 'canceled',
        self::STATUS_COMPLAINT => 'complaint',
    ];

    /**
     * @return BelongsTo
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'item_id', 'id')->withDefault()->withTrashed();
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'item_id', 'id')->withDefault()->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $query = self::with('dish')->with('table')->with('bill')->limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['table'])) {
            if (isset($criteria['table']['user'])) {
                $query->with(['table' => function ($query) use ($criteria) {
                    $query->where('user_id', '=', $criteria['table']['user']);
                }]);
                unset($criteria['table']['user']);
            }
            if (! empty($criteria['table'])) {
                $query->whereIn('table_id', $criteria['table']);
            }
        }
        if (! empty($criteria['bill'])) {
            if (! empty($criteria['bill'])) {
                $query->whereIn('bill_id', $criteria['bill']);
            }
        }
        if (! empty($criteria['dish'])) {
            $query->whereIn('dish_id', $criteria['dish']);
        }
        if (! empty($criteria['status'])) {
            $query->whereIn('status', $criteria['status']);
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('user_id', $criteria['user']);
        }
        if (isset($criteria['paid']) && null !== $criteria['paid']) {
            $query->where('paid', '=', $criteria['paid']);
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    /**
     * @param int $dishId
     * @param int $billId
     *
     * @return Collection
     */
    public static function findGiftDishOnBill(int $dishId, int $billId): Collection
    {
        return self::where('bill_id', '=', $billId)
            ->where('dish_id', '=', $dishId)
            ->where('status', '!=', self::STATUS_CANCELED)
            ->where('paid', '=', self::PAID_NO)
            ->get();
    }

    /**
     * @param int  $dishId
     * @param int  $billId
     * @param bool $onlyNotEmptyDiscount
     *
     * @return int
     */
    public static function sumQuantityOrderDishOnBill(int $dishId, int $billId, bool $onlyNotEmptyDiscount = false)
    {
        $query = self::where('bill_id', '=', $billId)
            ->where('dish_id', '=', $dishId)
            ->where('status', '!=', self::STATUS_CANCELED)
            ->where('paid', '=', self::PAID_NO);
        if ($onlyNotEmptyDiscount) {
            $query->where('discount', '>', 0);
        }

        return $query->sum('quantity');
    }

    public static function getStatisticByDish(array $criteria = [], $addSelect = [], $group = [], array $order = []): Collection
    {
        $select = self::statisticByDish();
        if (! empty($criteria)) {
            $select->where($criteria);
        }
        if (! empty($addSelect)) {
            $select->addSelect($addSelect);
        }
        if (! empty($group)) {
            $select->groupBy($group);
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $select->orderBy(self::decamelize($column), $direction);
            }
        }

        return $select->get();
    }

    public static function getStatisticByDishDelay(array $criteria = [], $addSelect = [], $group = [], array $order = []): Collection
    {
        $select = self::statisticByDishDelay();
        if (! empty($criteria)) {
            $select->where($criteria);
        }
        if (! empty($addSelect)) {
            $select->addSelect($addSelect);
        }
        if (! empty($group)) {
            $select->groupBy($group);
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $select->orderBy(self::decamelize($column), $direction);
            }
        }

        return $select->get();
    }

    public static function getStatisticByTable(array $criteria = [], $addSelect = [], $group = [], array $order = []): Collection
    {
        $select = self::statisticByTable();
        if (! empty($criteria)) {
            $select->where($criteria);
        }
        if (! empty($addSelect)) {
            $select->addSelect($addSelect);
        }
        if (! empty($group)) {
            $select->groupBy($group);
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $select->orderBy(self::decamelize($column), $direction);
            }
        }

        return $select->get();
    }

    public static function getStatisticByTableDelay(array $criteria = [], $addSelect = [], $group = [], array $order = []): Collection
    {
        $select = self::statisticByTableDelay();
        if (! empty($criteria)) {
            $select->where($criteria);
        }
        if (! empty($addSelect)) {
            $select->addSelect($addSelect);
        }
        if (! empty($group)) {
            $select->groupBy($group);
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $select->orderBy(self::decamelize($column), $direction);
            }
        }

        return $select->get();
    }

    public function getAdditions($restaurant_id = null)
    {
        if (empty($this->customize['additions'])) {
            return new Collection();
        }
        if ($this->customize['additions'] instanceof Collection) {
            return $this->customize['additions'];
        }
        $additions = new Collection();
        /*if(\Request::ip() == '192.168.100.129'){
            dd($this);
        }*/

        foreach ($this->customize['additions'] as $addition) {
            try {
                if ($restaurant_id) {
                    $restaurant = Restaurant::where('id', $restaurant_id)->first();
                    if ($restaurant) {
                        $this->reconnect($restaurant);
                        $find = Addition::find($addition['id']) ?? new Addition();
                        $this->reset();
                    }
                } else {
                    $find = Addition::find($addition['id']) ?? new Addition();
                }

                $find->id = !empty($addition['id']) ? $addition['id'] : false;
                $find->name = !empty($addition['name']) ? $addition['name'] : false;
                $find->type = !empty($addition['type']) ? $addition['type'] : null;
                $find->price = !empty($addition['price']) ? $addition['price'] : '0.00';
                $find->quantity = !empty($addition['quantity']) ? $addition['quantity'] : false;

                $additions->push($find);
            } catch (\Exception $e) {
                Log::error('Błąd podczas przetwarzania dodatku: ' . $e->getMessage(), [
                    'addition' => $addition,
                    'restaurant_id' => $restaurant_id,
                ]);

                continue;
            }
        }

        return $additions;
    }
//
//    public function getRoast()
//    {
//        return $this->customize['roast'] ?? null;
//    }

    /**
     * @return Builder
     */
    private static function statisticByTable(): Builder
    {
        return self::selectRaw('table_id,
            COUNT(distinct dish_id) as dish_count,
            SUM(quantity) as quantity_sum,
            CAST(AVG(price) as DECIMAL(10,2)) as price_avg,
            SUM(price*quantity) as price_sum')
            ->with('table:id,name')
            ->where('status', '!=', self::STATUS_CANCELED)
            ->whereNotNull('table_id')
            ->groupBy('table_id');
    }

    /**
     * @return Builder
     */
    private static function statisticByDish(): Builder
    {
        return self::selectRaw('dish_id,
            COUNT(distinct table_id) as table_count,
            SUM(quantity) as quantity_sum,
            CAST(AVG(price) as DECIMAL(10,2)) as price_avg,
            SUM(price*quantity) as price_sum')
            ->with('dish:id,name')
            ->where('status', '!=', self::STATUS_CANCELED)
            ->groupBy('dish_id');
    }

    /**
     * @return Builder
     */
    private static function statisticByTableDelay(): Builder
    {
        return self::selectRaw('table_id,
            COUNT(distinct dish_id) as dish_count,
            ROUND(AVG(TIMESTAMPDIFF(MINUTE, accepted_at, released_at) - dishes.time_wait)) as delay')
            ->leftJoin('dishes', 'dishes.id', '=', 'orders.dish_id')
            ->with('table:id,name')
            ->where('status', '=', self::STATUS_RELEASED)
            ->whereNotNull('accepted_at')
            ->whereNotNull('released_at')
            ->whereNotNull('table_id')
            ->groupBy('table_id');
    }

    /**
     * @return Builder
     */
    private static function statisticByDishDelay(): Builder
    {
        return self::selectRaw('dish_id,
            COUNT(distinct table_id) as table_count,
            ROUND(AVG(TIMESTAMPDIFF(MINUTE, accepted_at, released_at) - dishes.time_wait)) as delay')
            ->leftJoin('dishes', 'dishes.id', '=', 'orders.dish_id')
            ->with('dish:id,name')
            ->where('status', '=', self::STATUS_RELEASED)
            ->whereNotNull('accepted_at')
            ->whereNotNull('released_at')
            ->whereNotNull('dish_id')
            ->groupBy('dish_id');
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param bool        $onlyGroup
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null): LengthAwarePaginator
    {
        $query = self::distinct()->select('orders.*')
            ->addSelect('orderDish.name as oname')
            ->addSelect('orderTable.name as tname')
            ->leftJoin('dishes as orderDish', 'orderDish.id', '=', 'orders.dish_id')
            ->leftJoin('tables as orderTable', 'orderTable.id', '=', 'orders.table_id')
            ->with('dish:id,name')
            ->with('table:id,name')
            ->with('bill:id');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if (in_array($column, ['orderDish', 'orderTable', 'price'])) {
                    $query->leftJoin('ltm_translations as t'.$column, function (JoinClause $join) use ($column) {
                        $join->on('t'.$column.'.key', '=', 'price' == $column ? 'orders.'.$column : $column.'.name')
                            ->where('t'.$column.'.locale', '=', app()->getLocale())
                            ->where('t'.$column.'.status', '=', 0);
                    })
                        ->addSelect('t'.$column.'.value');
                }
                $query->orderBy(in_array($column, ['orderDish', 'orderTable', 'price']) ?
                                new Expression('IFNULL(`t'.$column.'`.`value`,`'.('price' == $column ? 'orders`.`price' : $column.'`.`name').'`)') : self::decamelize($column), $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->leftJoin('ltm_translations as t', function (JoinClause $join) {
                $join->on(function (JoinClause $join) {
                    $join->whereRaw('t.key = orderTable.name OR t.key = orderDish.name');
                })
                    ->whereIn('t.group', ['orders', 'dishes', 'tables'])
                    ->where('t.locale', '=', app()->getLocale())
                    ->where('t.status', '=', 0);
            });
            $query->where(function ($q) use ($filter) {
                $q->where('t.value', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orders.price', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orders.id', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orders.created_at', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orders.bill_id', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orderTable.name', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orderDish.name', 'LIKE', '%'.$filter.'%')
                    ->orWhere('orders.status', 'LIKE', '%'.$filter.'%');
            });
        }

        return $query->paginate($paginateSize, ['orders.*']);
    }

    public static function getStatusName(int $status)
    {
        throw_if(! isset(self::$statusName[$status]), new \Exception('Nnot found this status!'));

        return self::$statusName[$status];
    }
}
