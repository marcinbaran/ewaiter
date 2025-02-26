<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Translatable\HasTranslations;

class AdditionGroup extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additions_groups';

    /**
     * @var int
     */
    public const TYPE_SINGLE = 0;

    /**
     * @var int
     */
    public const TYPE_MULTIPLE = 1;

    private static $typeName = [
        self::TYPE_SINGLE => 'Single choice',
        self::TYPE_MULTIPLE => 'Multiple choice',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'mandatory',
        'visibility',
        'mandatory',
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
        'visibility' => 0,
        'type' => 0,
    ];

    /**
     * @return HasMany
     */
//    public function additions(): HasMany
//    {
//        return $this->hasMany(Addition::class, 'addition_group_id', 'id');
//    }

    /**
     * @return HasMany
     */
    public function additions_additions_groups(): HasMany
    {
        return $this->hasMany(AdditionAdditionGroup::class, 'addition_group_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function additions_groups_categories(): HasMany
    {
        return $this->hasMany(AdditionGroupCategory::class, 'addition_group_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function additions_groups_dishes(): HasMany
    {
        return $this->hasMany(AdditionGroupDish::class, 'addition_group_id', 'id');
    }

    public function getTypeName()
    {
        if (! isset(self::$typeName[$this->type])) {
            return 'Wrong type!';
        }

        return self::$typeName[$this->type];
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
        $query = self::select();
        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (null !== ($criteria['visibility'] ?? null)) {
            $visibility = $criteria['visibility'];
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
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize): LengthAwarePaginator
    {
        $query = self::distinct()
            ->orderBy('name', 'asc');

        if (! empty($filter)) {
            $query->where('name', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['*']);
    }
}
