<?php

namespace App\Models;

use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Translatable\HasTranslations;

class FoodCategory extends Model
{
    use NodeTrait,
        ModelTrait,
        HasTranslations;
    use UsesTenantConnection;

    /**
     * @var bool
     */
    public const VISIBILITY_YES = true;

    /**
     * @var bool
     */
    public const VISIBILITY_NO = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'visibility',
        'parent_id',
        'position',
        'name_translation',
        'description_translation',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        '_lft',
        '_rgt',
        'depth',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        '_lft' => 0,
        '_rgt' => 0,
        'position' => 0,
        'visibility' => self::VISIBILITY_NO,
        'parent_id' => null,
    ];

    protected $casts = [
        'name_translation' => 'json',
        'description_translation' => 'json',
        'name' => 'array',
        'description' => 'array',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany
     */
    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class, 'food_category_id', 'id');
    }

    public function visibleDishes(): HasMany
    {
        return $this->dishes()
            ->where('visibility', 1)
            ->visible();
    }

    public function dishesWithPosition(): HasMany
    {
        return $this->visibleDishes()
            ->where('visibility', 1)
            ->visible()
            ->where('position', '>', 0)
            ->orderBy('position');
    }

    public function dishesWithoutPosition(): HasMany
    {
        return $this->visibleDishes()
            ->where('visibility', 1)
            ->visible()
            ->whereIn('position', [0, null]);
    }

    /**
     * @return HasMany
     */
    public function child_categories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function additions_groups_categories(): HasMany
    {
        return $this->hasMany(AdditionGroupCategory::class, 'category_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function availability(): HasOne
    {
        return $this->hasOne(Availability::class, 'food_category_id', 'id');
    }

    /**
     * @return MorphOne
     */
    public function photo(): MorphOne
    {
        return $this->morphOne(Resource::class, 'resourcetable');
    }

    /**
     * @return HasOne
     */
    public function parentFoodCategory()
    {
        return $this->hasOne(self::class, 'parent_id', 'id');
    }

    /**
     * @param int $depth
     *
     * @return Collection
     */
    public static function getRowsWithDepth(int $depth = 0): Collection
    {
        return self::orderBy('name', 'asc')->withDepth()->having('depth', '=', $depth)->get();
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
        $query = self::select('food_categories.*')
            ->with('photo');
        //->limit($limit)
        //->offset($offset);

        $query->where(function ($query_ex) {
            $query_ex->whereExists(function ($query_e) {
                $today = Availability::getWeekDay(Carbon::now()->dayOfWeek);
                $query_e->select(\DB::raw('availabilities.food_category_id'))
                    ->from('availabilities')
                    ->whereRaw('( availabilities.food_category_id = food_categories.id  AND availabilities.'.$today.' = 1
                        AND ( availabilities.start_hour IS NULL OR ((availabilities.start_hour < availabilities.end_hour AND NOW() BETWEEN availabilities.start_hour AND availabilities.end_hour) OR (availabilities.end_hour < availabilities.start_hour AND NOW() < availabilities.start_hour AND NOW() < availabilities.end_hour) OR (availabilities.end_hour < availabilities.start_hour AND NOW() > availabilities.start_hour))))
                     ');
            })
            ->orWhereNotExists(function ($query_exx) {
                $query_exx->select(\DB::raw('null'))
                    ->from('availabilities')
                    ->whereRaw('availabilities.food_category_id = food_categories.id');
            });
        });

        $query->whereNotNull('position');

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (isset($criteria['visibility'])) {
            $query->where(function ($q) {
                $subQuery = self::select('visibility')
                    ->whereColumn('food_categories.id', '=', 'parent_id');

                $q->where(function ($sq) use ($subQuery) {
                    $sq->whereExists($subQuery->toBase())
                        ->where('visibility', 1);
                })->orWhereNull('parent_id');
            });

            $query->where('visibility', $criteria['visibility']);
        }

        if (isset($criteria['parent'])) {
            if (0 == $criteria['parent']) {
                $query->whereIsRoot();
            } else {
                $query->where(NestedSet::PARENT_ID, '=', $criteria['parent']);
            }
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if ($column == 'position') {
                    $query->orderByRaw('ISNULL(position), position '.$direction);
                } else {
                    $query->orderBy(self::decamelize($column), $direction);
                }
            }
        }

        if (isset($criteria['delivery']) && $criteria['delivery']) {
            $query->whereHas('dishes', function ($query) use ($criteria) {
                $query->where('delivery', $criteria['delivery']);
            })
                ->orWhereHas('child_categories.dishes', function ($query) use ($criteria) {
                    $query->where('delivery', $criteria['delivery']);
                });
        }

        $foodCategories = $query->get();

        foreach ($foodCategories as $fc) {
            $fc->name_translation = $fc->name_translation[$criteria['locale']] ??
                $fc->name_translation[config('app.fallback_locale')] ??
                $fc->name;
            $fc->description_translation = $fc->description_translation[$criteria['locale']] ??
                $fc->description_translation[config('app.fallback_locale')] ??
                $fc->description;
        }

        return $foodCategories;
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null): LengthAwarePaginator
    {
        $query = self::select('food_categories.*')
            ->with('photo');

        if ($filter) {
            $query->where('name', 'like', '%'.$filter.'%')
                ->orWhere('description', 'like', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['food_categories.*']);
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
