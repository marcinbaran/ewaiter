<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Spatie\Translatable\HasTranslations;
/**
 * @OA\Schema(
 *     schema="AdditionModel",
 *     type="object",
 *     title="Addition",
 *     description="Model representing an addition to a dish",
 *     required={"name", "price", "type"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the addition",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="dish_id",
 *         type="integer",
 *         description="The ID of the dish this addition belongs to",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the addition",
 *         example="Extra Cheese"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the addition",
 *         example=0.50
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="integer",
 *         description="The type of the addition (0 for checkbox, 1 for radio)",
 *         enum={0, 1},
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the addition was created",
 *         example="2024-07-30T15:03:01.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the addition was last updated",
 *         example="2024-07-30T15:03:01.000000Z"
 *     ),
 *     @OA\Property(
 *         property="translatable",
 *         type="array",
 *         @OA\Items(
 *             type="string"
 *         ),
 *         description="Translatable fields",
 *         example={"name"}
 *     ),
 *     @OA\Property(
 *         property="dish",
 *         ref="#/components/schemas/AdditionDish",
 *         description="The dish associated with this addition"
 *     )
 * )
 */
class Addition extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    /**
     * @var int
     */
    public const TYPE_CHECKBOX = 0;

    /**
     * @var int
     */
    public const TYPE_RADIO = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dish_id',
        'name',
        'price',
        'type',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'dish_id',
        'updated_at',
        'created_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'price' => 0,
    ];

    public $translatable = [
        'name',
    ];

    /**
     * @return HasMany
     */
    public function dish(): HasMany
    {
        return $this->hasMany(AdditionDish::class, 'addition_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function additions_additions_groups(): HasMany
    {
        return $this->hasMany(AdditionAdditionGroup::class, 'addition_id', 'id');
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
        $query = self::select()->with('dish:*');
        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['dish'])) {
            $dishes = $criteria['dish'];
            $query->whereHas('dish', function ($query) use ($dishes) {
                $query->whereIn('id', $dishes);
            });
        }
        if (null !== ($criteria['visibility'] ?? null)) {
            $visibility = $criteria['visibility'];
            $query->whereHas('dish', function ($query) use ($visibility) {
                $query->where('visibility', '=', $visibility);
            });
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
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, int $query_category = null, int $query_addition_group_id = null): LengthAwarePaginator
    {
        $query = self::distinct('additions.id')->select('additions.*');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if (in_array($column, ['name', 'dish'])) {
                    $query->leftJoin('ltm_translations as t'.$column, function (JoinClause $join) use ($column) {
                        $join->on('t'.$column.'.key', '=', ('dish' == $column ? 'dishes.' : 'additions.').self::decamelize('dish' == $column ? 'name' : $column))
                            ->where('t'.$column.'.group', '=', 'dish' == $column ? 'dishes' : 'additions')
                            ->where('t'.$column.'.locale', '=', app()->getLocale())
                            ->where('t'.$column.'.status', '=', 0);
                    })
                        ->addSelect('t'.$column.'.value');
                    ('dish' !== $column ?: $query->addSelect('dishes.name')); // distinct
                }
                $query->orderBy(in_array($column, ['name', 'dish']) ? new Expression('IFNULL(`t'.$column.'`.`value`,`'.('dish' == $column ? 'dishes' : 'additions').'`.`'.self::decamelize('dish' == $column ? 'name' : $column).'`)') : self::decamelize($column), $direction);
            }
        }

        if (! empty($filter)) {
            $query->leftJoin('ltm_translations as t', function (JoinClause $join) {
                $join->on(function (JoinClause $join) {
                    $join->whereRaw('t.key = additions.name');
                })
                    ->whereIn('t.group', ['additions'])
                    ->where('t.locale', '=', app()->getLocale())
                    ->where('t.status', '=', 0);
            });
            $query->where('t.value', 'LIKE', '%'.$filter.'%');
            $query->orWhere('additions.name', 'LIKE', '%'.$filter.'%');
        }

        if ($query_addition_group_id) {
            $query->whereDoesntHave('additions_additions_groups', function ($subQuery) use ($query_addition_group_id) {
                $subQuery->where('addition_group_id', '=', $query_addition_group_id);
            })->get();
        }

        return $query->paginate($paginateSize, ['additions.id']);
    }
}
