<?php

namespace App\Models;

use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Translatable\HasTranslations;

class Promotion extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;
    use SoftDeletes;

    /**
     * @var int
     */
    public const TYPE_ON_DISH = 0;

    /**
     * @var int
     */
    public const TYPE_ON_BILL = 1;

    /**
     * @var int
     */
    public const TYPE_ON_CATEGORY = 2;

    /**
     * @var int
     */
    public const TYPE_ON_BUNDLE = 3;

    /**
     * @var int
     */
    public const TYPE_VALUE_PERCEENT = 0;

    /**
     * @var int
     */
    public const TYPE_VALUE_PRICE = 1;

    /**
     * @var int
     */
    public const MERGE_YES = true;

    /**
     * @var int
     */
    public const MERGE_NO = false;

    /**
     * @var int
     */
    public const ACTIVE_YES = true;

    /**
     * @var int
     */
    public const ACITVE_NO = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type', // typ promocji: na danie, na zamówienie
        'type_value', // typ rabatu: procent, kwota
        'value', // wartość liczbowa rabatu
        'order_dish_id', //ID produktu, na którym robimy promocję
        'gift_dish_id', // ID produktu oferowanego w promocji
        'order_category_id', // ID kategorii produktów w promocji
        'min_quantity_order_dish', // min liczba produktu w zamówieniu uwzgledniająca promocję
        'min_price_bill', // min cena rachunku uwzgledniająca promocję
        'max_quantity_gift_dish', // max liczba produktu oferowanego w promocji na zamówieniu
        'description', // opis promocji
        'merge', //czy promocja łączy się z innymi
        'start_at', // od kiedy startuje promocja
        'end_at', // do kiedy ważna jest promocja
        'active', // czy promocja jest aktywna
        'box', // nr kontenera w układzie promocyjnym
    ];

    public $translatable = ['name', 'description'];

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
        'type' => self::TYPE_ON_DISH,
        'type_value' => self::TYPE_VALUE_PERCEENT,
        'min_quantity_order_dish' => 1,
        'min_price_bill' => 0,
        'merge' => false,
        'active' => true,
        'box' => 1,
    ];

    private static $boxes = [1, 2, 3];

    private static $typeName = [
        self::TYPE_ON_DISH => 'On dish',
        self::TYPE_ON_BILL => 'On bill',
        self::TYPE_ON_CATEGORY => 'On category',
        self::TYPE_ON_BUNDLE => 'On bundle',
    ];

    /**
     * @return BelongsTo
     */
    public function orderCategory(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'order_category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function orderDish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'order_dish_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function giftDish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'gift_dish_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function promotion_dishes(): HasMany
    {
        return $this->hasMany(PromotionDish::class, 'promotion_id', 'id')->withTrashed();
    }

    /**
     * @return MorphOne
     */
    public function photo(): MorphOne
    {
        return $this->morphOne(Resource::class, 'resourcetable');
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        return self::getQueryRows($criteria, $order, $limit, $offset)->get();
    }

    /**
     * @param Order $order
     *
     * @return Collection
     */
    public static function findActiveOnOrderDish(Order $order): Collection
    {
        $query1 = self::getQueryActiveOnOrderCategory($order);

        $query2 = self::getQueryActiveOnOrderDish($order)
            ->union($query1);

        return $query2->get();
    }

    /**
     * @param Bill $bill
     *
     * @return Collection
     */
    public static function findActiveOnBill(Bill $bill): Collection
    {
        $query1 = self::getQueryActiveOnBill($bill);

        $query2 = self::getQueryActiveOnBillBundle($bill)
            ->union($query1);

        return $query2->get();
    }

    /**
     * @param Bill $bill
     *
     * @return Collection
     */
    public static function findActiveForBillAndOrderDish(Bill $bill): Collection
    {
        $query1 = self::getQueryActiveOnOrderDish()
            ->where('orders.bill_id', '=', $bill->id);

        $query2 = self::getQueryActiveOnOrderCategory()
            ->where('orders.bill_id', '=', $bill->id);

        $query4 = self::getQueryActiveOnBillBundle($bill);

        $query3 = self::getQueryActiveOnBill($bill)
            ->union($query1)
            ->union($query2)
            ->union($query4);

        return $query3->get();
    }

    /**
     * @param string|null $filter
     * @param int $paginateSize
     * @param array $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null): LengthAwarePaginator
    {
        $query = self::distinct()->select('promotions.*')
            ->addSelect('orderCategory.name as fname')
            ->addSelect('orderDish.name as oname')
            ->addSelect('giftDish.name as gname')
            ->leftJoin('dishes as orderDish', 'orderDish.id', '=', 'promotions.order_dish_id')
            ->leftJoin('dishes as giftDish', 'giftDish.id', '=', 'promotions.gift_dish_id')
            ->leftJoin('food_categories as orderCategory', 'orderCategory.id', '=', 'promotions.order_category_id')
            ->with('orderDish:id,name,description')
            ->with('giftDish:id,name,description')
            ->with('orderCategory:id,name,description')
            ->with('photo');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if (in_array($column, ['orderDish', 'giftDish', 'orderCategory', 'description'])) {
                    $query->leftJoin('ltm_translations as t'.$column, function (JoinClause $join) use ($column) {
                        $join->on('t'.$column.'.key', '=', 'description' == $column ? 'promotions.'.$column : $column.'.name')
                            ->where('t'.$column.'.group', '=', 'description' == $column ? 'promotions' : ('orderCategory' == $column ? 'food_categories' : 'dishes'))
                            ->where('t'.$column.'.locale', '=', app()->getLocale())
                            ->where('t'.$column.'.status', '=', 0);
                    })
                        ->addSelect('t'.$column.'.value');
                }
                $query->orderBy(in_array($column, ['orderDish', 'giftDish', 'orderCategory', 'description']) ?
                    new Expression('IFNULL(`t'.$column.'`.`value`,`'.('description' == $column ? 'promotions`.`description' : $column.'`.`name').'`)') : self::decamelize($column), $direction);
            }
        }

        if (! empty($filter)) {
            $query->leftJoin('ltm_translations as t', function (JoinClause $join) {
                $join->on(function (JoinClause $join) {
                    $join->whereRaw('t.key = promotions.description OR t.key = giftDish.name OR orderDish.name OR orderCategory.name ');
                })
                    ->whereIn('t.group', ['promotions', 'dishes', 'food_categories'])
                    ->where('t.locale', '=', app()->getLocale())
                    ->where('t.status', '=', 0);
            });
            $query->where('t.value', 'LIKE', '%'.$filter.'%');
            $query->orWhere('promotions.description', 'LIKE', '%'.$filter.'%')
                ->orWhere('orderDish.name', 'LIKE', '%'.$filter.'%')
                ->orWhere('giftDish.name', 'LIKE', '%'.$filter.'%')
                ->orWhere('orderCategory.name', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['promotions.*']);
    }

    public static function boxes()
    {
        return self::$boxes;
    }

    public static function getTypeName(int $type)
    {
        throw_if(! isset(self::$typeName[$type]), new \Exception('Wrong type of promotion!'));

        return self::$typeName[$type];
    }

    private static function getQueryRows(array $criteria, array $order, int $limit, int $offset): Builder
    {
        $query = self::limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['orderDish'])) {
            $query->whereIn('order_dish_id', $criteria['orderDish']);
        }
        if (! empty($criteria['giftDish'])) {
            $query->whereIn('gift_dish_id', $criteria['giftDish']);
        }
        if (null !== ($criteria['type'] ?? null)) {
            $query->where('type', '=', $criteria['type']);
        }
        if (null !== ($criteria['typeValue'] ?? null)) {
            $query->where('type_value', '=', $criteria['typeValue']);
        }
        if (null !== ($criteria['merge'] ?? null)) {
            $query->where('merge', '=', $criteria['merge']);
        }
        if (null !== ($criteria['active'] ?? null)) {
            $query->where('active', '=', $criteria['active']);
            if ($criteria['active']) {
                $query->where(function ($query) {
                    $query->whereNull('start_at')
                        ->orWhere('start_at', '<=', Carbon::now());
                })
                    ->where(function ($query) {
                        $query->whereNull('end_at')
                            ->orWhere('end_at', '>=', Carbon::now());
                    });
            } else {
                $query->where(function ($query) {
                    $query->whereNotNull('start_at')
                        ->orWhere('start_at', '>', Carbon::now());
                })
                    ->where(function ($query) {
                        $query->whereNotNull('end_at')
                            ->orWhere('end_at', '<', Carbon::now());
                    });
            }
        }
        if (! empty($criteria['box'])) {
            $query->whereIn('box', $criteria['box']);
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query;
    }

    /**
     * @param Bill $bill
     *
     * @return Builder
     */
    private static function getQueryActiveOnBill(Bill $bill): Builder
    {
        return self::where('promotions.type', '=', self::TYPE_ON_BILL)
            ->where('promotions.active', '=', true)
            ->where(function ($query) use ($bill) {
                $query->whereNull('promotions.start_at')
                    ->orWhere('promotions.start_at', '<=', $bill->payment_at ? $bill->payment_at : Carbon::now());
            })
            ->where(function ($query) use ($bill) {
                $query->whereNull('promotions.end_at')
                    ->orWhere('promotions.end_at', '>=', $bill->payment_at ? $bill->payment_at : Carbon::now());
            })
            ->where('promotions.min_price_bill', '<=', $bill->price);
    }

    /**
     * @param Order $order
     *
     * @return Builder
     */
    private static function getQueryActiveOnOrderDish(Order $order = null): Builder
    {
        $query = self::select('promotions.*')->distinct()
            ->join('orders', 'promotions.order_dish_id', '=', 'orders.dish_id')
            ->join('bills', 'bills.id', '=', 'orders.bill_id')
            ->where('orders.status', '!=', Order::STATUS_CANCELED)
            ->where('orders.paid', '=', Order::PAID_NO)
            ->where('promotions.type', '=', self::TYPE_ON_DISH)
            ->where('promotions.active', '=', true)
            ->where(function ($query) {
                $query->whereNull('promotions.start_at')
                    ->orWhere('promotions.start_at', '<=', new Expression('IFNULL(`bills`.`payment_at`, "'.Carbon::now().'")'));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.end_at')
                    ->orWhere('promotions.end_at', '>=', new Expression('IFNULL(`bills`.`payment_at`, "'.Carbon::now().'")'));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.min_quantity_order_dish')
                    ->orWhere('promotions.min_quantity_order_dish', '<=', new Expression(
                        '(SELECT SUM(`o`.`quantity`) FROM `orders` AS `o` '
                        .'WHERE `orders`.`bill_id`=`o`.`bill_id` '
                        .'AND `orders`.`dish_id`=`o`.`dish_id` '
                        .'AND `o`.`status` != '.Order::STATUS_CANCELED.' AND `o`.`paid`='.(int) Order::PAID_NO.')'
                    ));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.min_price_bill')
                    ->orWhere('promotions.min_price_bill', '<=', new Expression('`bills`.`price`'));
            });
        if ($order) {
            $query->where('promotions.order_dish_id', '=', $order->dish_id)
                ->where('orders.bill_id', '=', $order->bill_id);
        }

        return $query;
    }

    /**
     * @param Bill $bill
     *
     * @return Builder
     */
    private static function getQueryActiveOnBillBundle(Bill $bill = null): Builder
    {
        $ordered_dishes = $bill->orders->pluck('dish_id');
        //$query = self::select('promotions.*')->whereNull('id');
        foreach ($bill->orders as $order) {
            $query = self::
            where('promotions.type', '=', self::TYPE_ON_BUNDLE)
                ->where('promotions.active', '=', true)
                ->where(function ($query) {
                    $query->whereNull('promotions.start_at')
                        ->orWhere('promotions.start_at', '<=', Carbon::now());
                })
                ->where(function ($query) {
                    $query->whereNull('promotions.end_at')
                        ->orWhere('promotions.end_at', '>=', Carbon::now());
                });

            if (isset($query_promotions)) {
                $query_promotions = $query_promotions->union($query);
            } else {
                $query_promotions = $query;
            }
        }
        if (isset($query_promotions) && count($query_promotions->get())) {
            foreach ($query_promotions->get() as $key => $promotion) {
                $promotion_dishes = $promotion->promotion_dishes->pluck('dish_id');
                $contains = count($promotion_dishes->intersect($ordered_dishes)) == count($promotion_dishes);
                if (! $contains) {
                    $query->where('promotions.id', '!=', $promotion->id);
//                    $promotion_valid = Promotion::where('id',$promotion->id);
//                    if(isset($query_promotions_valid))
//                        $query_promotions_valid = $query_promotions_valid->union($promotion_valid);
//                    else
//                        $query_promotions_valid = $promotion_valid;
                }
//                else {
//                }
            }
        }

        return $query;
    }

    /**
     * @param Order $order
     *
     * @return Builder
     */
    private static function getQueryActiveOnOrderCategory(Order $order = null): Builder
    {
        $query = self::select('promotions.*')->distinct()
            ->join('food_categories', 'promotions.order_category_id', '=', 'food_categories.id')
            ->join('dishes', function (JoinClause $join) {
                $join->on(function ($query) {
                    $query->whereRaw(new Expression('`dishes`.`food_category_id` IN( '
                        .'SELECT `c`.`id` FROM  `food_categories` AS `c` '
                        .'WHERE `c`.`id` = `food_categories`.`id` or `c`.`_lft` between `food_categories`.`_lft` and `food_categories`.`_rgt`)'));
                });
            })
            ->join('orders', 'dishes.id', '=', 'orders.dish_id')
            ->join('bills', 'bills.id', '=', 'orders.bill_id')
            ->where('orders.status', '!=', Order::STATUS_CANCELED)
            ->where('orders.paid', '=', Order::PAID_NO)
            ->where('promotions.type', '=', self::TYPE_ON_CATEGORY)
            ->where('promotions.active', '=', true)
            ->where(function ($query) {
                $query->whereNull('promotions.start_at')
                    ->orWhere('promotions.start_at', '<=', new Expression('IFNULL(`bills`.`payment_at`, "'.Carbon::now().'")'));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.end_at')
                    ->orWhere('promotions.end_at', '>=', new Expression('IFNULL(`bills`.`payment_at`, "'.Carbon::now().'")'));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.min_quantity_order_dish')
                    ->orWhere('promotions.min_quantity_order_dish', '<=', new Expression(
                        '(SELECT SUM(`o`.`quantity`) FROM `orders` AS `o` '
                        .'WHERE `orders`.`bill_id`=`o`.`bill_id` '
                        .'AND `orders`.`dish_id`=`o`.`dish_id` '
                        .'AND `o`.`status` != '.Order::STATUS_CANCELED.' AND `o`.`paid`='.(int) Order::PAID_NO.')'
                    ));
            })
            ->where(function ($query) {
                $query->whereNull('promotions.min_price_bill')
                    ->orWhere('promotions.min_price_bill', '<=', new Expression('`bills`.`price`'));
            });

        if ($order) {
            $query
                ->where('orders.dish_id', '=', $order->dish_id)
                ->where('orders.bill_id', '=', $order->bill_id);
        }

        return $query;
    }

    /**
     * @param Bill $bill
     *
     * @return float
     */
    public static function onBundleCalculatePrice(Bill $bill, self $promotion): float
    {
        $ordered_dishes = $bill->orders->pluck('dish_id');
        $promotion_dishes = $promotion->promotion_dishes->pluck('dish_id');

        $discount = 0;
        $contains = (count($promotion_dishes->intersect($ordered_dishes)) == count($ordered_dishes));
        if ($contains) {
            $quantity = $bill->orders->whereIn('dish_id', $ordered_dishes)->min('quantity');
            $discount = $quantity * $promotion->value;
        }

        return number_format((float) $discount, 2, '.', '');
    }

    /**
     * @param Bill $bill
     *
     * @return float
     */
    public static function onBundleCalculatePercentage(Bill $bill, self $promotion): float
    {
        $ordered_dishes = $bill->orders->pluck('dish_id');
        $promotion_dishes = $promotion->promotion_dishes->pluck('dish_id');

        $discount = 0;
        $contains = (count($promotion_dishes->intersect($ordered_dishes)) == count($ordered_dishes));
        if ($contains) {
            $quantity = $bill->orders->whereIn('dish_id', $ordered_dishes)->min('quantity');
            foreach ($bill->orders->whereIn('dish_id', $ordered_dishes) as $order) {
                if ($promotion_dishes->contains($order->dish_id)) {
                    $discount += (($quantity * $order->price * $promotion->value) / 100);
                }
            }
        }

        return number_format((float) $discount, 2, '.', '');
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
}
