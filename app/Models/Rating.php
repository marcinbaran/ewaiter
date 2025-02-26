<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Rating extends Model
{
    use ModelTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'bill_id',
        'restaurant_id',
        'anonymous',
        'comment',
        'restaurant_comment',
        'r_delivery',
        'r_food',
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
        'anonymous' => 0,
        'visibility' => 0,
    ];

    /**
     * @return HasMany
     */
    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }

    /**
     * @return string
     */
    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function isAnonymous(): string
    {
        return $this->anonymous ? 'Yes' : 'No';
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
        if (! empty($criteria['restaurant_id'])) {
            $query->whereIn('restaurant_id', $criteria['restaurant_id']);
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
            $query->where('ratings.r_food', 'LIKE', '%'.$filter.'%')
                ->orWhere('ratings.r_delivery', 'LIKE', '%'.$filter.'%')
                ->orWhere('ratings.comment', 'LIKE', '%'.$filter.'%')
                ->orWhere('ratings.restaurant_comment', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['ratings.*']);
    }
}
