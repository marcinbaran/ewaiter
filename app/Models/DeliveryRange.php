<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DeliveryRange extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'range_from',
        'range_to',
        'range_polygon',
        'min_value',
        'free_from',
        'cost',
        'km_cost',
        'out_of_range',
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
    ];

    public function getRangeFrom()
    {
        if ($this->id) {
            return $this->range_from;
        }
        $range_db = self::orderBy('range_to', 'desc')->where('id', '!=', $this->id)->first();

        return $range_db ? $range_db->range_to : 0;
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
        $query = self::limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
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
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, $filter_columns = null): LengthAwarePaginator
    {
        $query = self::distinct();

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
            $query->where('delivery_ranges.r_food', 'LIKE', '%'.$filter.'%')
                ->orWhere('delivery_ranges.r_delivery', 'LIKE', '%'.$filter.'%')
                ->orWhere('delivery_ranges.comment', 'LIKE', '%'.$filter.'%')
                ->orWhere('delivery_ranges.restaurant_comment', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['delivery_ranges.*']);
    }

    public function scopeInDistance(Builder $builder, int|float $distance)
    {
        return $builder->where('range_from', '<=', $distance)->where('range_to', '>', $distance);
    }

    public function scopePreviousWithFixedDeliveryCost(Builder $builder, self $deliveryRange)
    {
        return $builder
            ->where('range_to', '<=', $deliveryRange->range_from)
            ->where('cost', '>', 0)
            ->where('out_of_range', 0)
            ->orderBy('range_to', 'desc')
            ->take(1);
    }

    public function scopeOutOfRange(Builder $builder)
    {
        $builder->where('out_of_range', 1)->first();
    }
}
