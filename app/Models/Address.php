<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     required={"city", "postcode"},
 *     @OA\Property(property="company_name", type="string", description="Company name associated with the address"),
 *     @OA\Property(property="nip", type="string", description="Company tax identification number (NIP)"),
 *     @OA\Property(property="name", type="string", description="First name of the contact person"),
 *     @OA\Property(property="surname", type="string", description="Last name of the contact person"),
 *     @OA\Property(property="city", type="string", description="City", example="Warsaw"),
 *     @OA\Property(property="postcode", type="string", description="Postal code", example="00-001"),
 *     @OA\Property(property="street", type="string", description="Street address"),
 *     @OA\Property(property="building_number", type="string", description="Building number"),
 *     @OA\Property(property="house_number", type="string", description="House number"),
 *     @OA\Property(property="floor", type="string", description="Floor number"),
 *     @OA\Property(property="phone", type="string", description="Contact phone number"),
 * )
 */
class Address extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'company_name',
        'nip',
        'name',
        'surname',
        'city',
        'postcode',
        'street',
        'building_number',
        'house_number',
        'floor',
        'phone',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return BelongsTo
     */

    /**
     * @OA\Property(
     *     property="user",
     *     ref="#/components/schemas/User",
     *     description="User associated with the address"
     * )
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'address_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'address_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'address_id', 'id');
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
}
