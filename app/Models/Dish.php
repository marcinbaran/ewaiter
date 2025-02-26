<?php

namespace App\Models;

use App\Decorators\MoneyDecorator;
use App\Enum\AttributeGroupInputType;
use App\Helpers\MoneyFormatter;
use App\Http\Helpers\SearchHelper;
use App\Http\Resources\Api\AdditionGroupDishResource;
use App\Services\GlobalSearch\Searchable;
use Bkwld\Croppa\Facades\Croppa;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Dish extends Model implements Searchable, Auditable
{
    use ModelTrait, UsesTenantConnection, SoftDeletes, HasTranslations, AuditableTrait;

    public $translatable = [
        'name', 'description',
    ];

    protected $fillable = [
        'name',
        'description',
        'food_category_id',
        'time_wait',
        'price',
        'tax',
        'discount',
        'visibility',
        'delivery',
        'position',
        'name_translation',
        'description_translation',
    ];

    protected $hidden = [
        'food_category_id',
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'price' => 0,
        'visibility' => true,
        'delivery' => false,
        'discount' => 0,
        'time_wait' => 5,
        'position' => 0,
    ];

    protected $casts = [
        'name_translation' => 'json',
        'description_translation' => 'json',
    ];

    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $dishes = self::getQueryRows($criteria, $order, $limit, $offset)->get();

        foreach ($dishes as $dish) {
            $dish->name_translation = $dish->name_translation[$criteria['locale']] ??
                $dish->name_translation[config('app.fallback_locale')] ?? $dish->name;

            $dish->description_translation = $dish->description_translation[$criteria['locale']] ??
                $dish->description_translation[config('app.fallback_locale')] ?? $dish->description;
        }

        return $dishes;
    }

    public static function getQueryRows(array $criteria = [], array $order = [], int $limit = 20, int $offset = 0): Builder
    {
        $query = self::with(['promotions', 'promotions_category', 'attributes'])->with('category:id,name,visibility')
            ->with('photos');

        self::filterByAttributes($query, $criteria);
        self::filterByPrice($query, $criteria);

        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }

        $query->where(function ($query_ex) {
            $query_ex->whereExists(function ($query_e) {
                $today = Availability::getWeekDay(Carbon::now()->dayOfWeek);
                $query_e->select(\DB::raw('availabilities.dish_id'))
                    ->from('availabilities')
                    ->whereRaw('( availabilities.dish_id = dishes.id  AND availabilities.'.$today.' = 1
                        AND ( availabilities.start_hour IS NULL OR ((availabilities.start_hour < availabilities.end_hour AND NOW() BETWEEN availabilities.start_hour AND availabilities.end_hour) OR (availabilities.end_hour < availabilities.start_hour AND NOW() < availabilities.start_hour AND NOW() < availabilities.end_hour) OR (availabilities.end_hour < availabilities.start_hour AND NOW() > availabilities.start_hour))))
                     ');
            })
                ->orWhereNotExists(function ($query_exx) {
                    $query_exx->select(\DB::raw('null'))
                        ->from('availabilities')
                        ->whereRaw('availabilities.dish_id = dishes.id');
                });
        });

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }

        if (! empty($criteria['search'])) {
            $search = strtolower($criteria['search']);
            $search = SearchHelper::replacePolishLetters($search);
            $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.pl'))) LIKE ?", ['%'.strtolower($search).'%']);
        }

        if (! empty($criteria['onlyWithPromotions'])) {
            $query->whereHas('promotions')
                ->orWhereHas('promotions_category');
        }

        if (! empty($criteria['delivery'])) {
            $query->where('delivery', 1);
        }

        if (! empty($criteria['category'])) {
            $categories = $criteria['category'];
            $strictCategory = $criteria['strictCategory'];

            $query->whereHas('category', function ($query) use ($categories, $strictCategory) {
                $query->whereIn('id', $categories);
                ($strictCategory) ?: $query->orWhereDescendantOf($categories);
            });
        }

        if (null !== ($criteria['visibility'] ?? null)) {
            $query->where('visibility', '=', $criteria['visibility']);
        }

        $query->whereNotNull('food_category_id');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if ($column == 'position') {
                    $query->orderByRaw('ISNULL(dishes.position), dishes.position '.$direction);
                } else {
                    $query->orderBy(self::decamelize($column), $direction);
                }
            }
        }

        if (! empty($criteria['withAdditions'])) {
            $query->with('additions:id,name');
        }

        return $query;
    }

    private static function filterByAttributes(Builder $query, array $criteria): void
    {
        if (isset($criteria['attribute_filters']) && is_array($criteria['attribute_filters'])) {
            foreach ($criteria['attribute_filters'] as $attributeGroup) {
                $attributesId = array_column($attributeGroup['attributes'], 'id');
                $inputType = AttributeGroupInputType::from($attributeGroup['input_type']);

                if (in_array($inputType, [AttributeGroupInputType::MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE, AttributeGroupInputType::SINGLE_OPTION_CHOICE])) {
                    self::conjunctiveAttributeFilter($query, $attributesId);
                }

                if ($inputType === AttributeGroupInputType::MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE) {
                    self::alternativeAttributeFilter($query, $attributesId);
                }
            }
        }
    }

    private static function conjunctiveAttributeFilter(Builder $query, array $attributesId): void
    {
        foreach ($attributesId as $attributeId) {
            $query->whereHas('attributes', function ($query) use ($attributeId) {
                return $query->active()->where('attribute_id', $attributeId);
            });
        }
    }

    private static function alternativeAttributeFilter(Builder $query, array $attributesId): void
    {
        $query->whereHas('attributes', function ($query) use ($attributesId) {
            return $query->active()->whereIn('attribute_id', $attributesId);
        });
    }

    private static function filterByPrice(Builder $query, array $criteria): void
    {
        if (isset($criteria['price_range']['min'])) {
            $query->where('price', '>=', MoneyFormatter::format($criteria['price_range']['min'])); // FIXME: consider promotions?
        }

        if (isset($criteria['price_range']['max'])) {
            $query->where('price', '<=', MoneyFormatter::format($criteria['price_range']['max'])); // FIXME: consider promotions?
        }
    }

    public static function getRowsTest(): Collection
    {
        return self::getQueryTest()->get();
    }

    public static function getQueryTest(): Builder
    {
        return self::select();
    }

    public static function findForPhrase(string $phrase = ''): Collection
    {
        if (! TenancyFacade::website()) {
            return new Collection([]);
        }

        return self::query()
            ->where('name', 'like', '%'.$phrase.'%')
            ->orWhere('description', 'like', '%'.$phrase.'%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getSearchGroupName(): string
    {
        return __('admin.Dishes');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id', 'id');
    }

    public function availability(): HasOne
    {
        return $this->hasOne(Availability::class, 'dish_id', 'id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(DishTag::class, 'dish_id', 'id');
    }

    public function additions(): HasMany
    {
        return $this->hasMany(AdditionDish::class, 'dish_id', 'id');
    }

    public function additions_groups_dishes(): HasMany
    {
        return $this->hasMany(AdditionGroupDish::class, 'dish_id', 'id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'dish_label');
    }

    // >>>>>>>>>>>>>>>>>>>>>>> LAST DITCH EFFORT, REPLACE WITH SOMETHING NICE ASAP <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

    public function dish_label(): HasMany
    {
        return $this->hasMany(DishLabel::class, 'dish_id', 'id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'dish_attribute');
    }

    public function dish_attribute(): HasMany
    {
        return $this->hasMany(DishAttribute::class, 'dish_id', 'id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'order_dish_id', 'id')
            ->where('promotions.active', 1)
            ->where(function ($query) {
                $query->whereNull('promotions.start_at')
                    ->orWhere('promotions.start_at', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('promotions.end_at')
                    ->orWhere('promotions.end_at', '>=', Carbon::now());
            });
    }

    public function promotions_category(): HasMany
    {
        return $this->hasMany(Promotion::class, 'order_category_id', 'food_category_id')
            ->where('promotions.active', 1)
            ->where(function ($query) {
                $query->whereNull('promotions.start_at')
                    ->orWhere('promotions.start_at', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('promotions.end_at')
                    ->orWhere('promotions.end_at', '>=', Carbon::now());
            });
    }

    public function bundle_promotions(): HasMany
    {
        return $this->hasMany(PromotionDish::class, 'dish_id', 'id')
            ->whereHas('promotion', function ($query) {
                return $query->where('promotions.active', 1)
                    ->where(function ($query) {
                        $query->whereNull('promotions.start_at')
                            ->orWhere('promotions.start_at', '<=', Carbon::now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('promotions.end_at')
                            ->orWhere('promotions.end_at', '>=', Carbon::now());
                    });
            });
    }

    public function isAvailableNow() : bool
    {
        $now   = Carbon::now();
        $today = Availability::getWeekDay($now->dayOfWeek);

        return $this->availability()
                ->where($today, true)
                ->where(function ($query) use ($now) {
                    $query->where(function ($query) use ($now) {
                        $query->whereNull('start_hour')
                            ->whereNull('end_hour');
                    })
                        ->orWhere(function ($query) use ($now) {
                            $query->whereTime('start_hour', '<=', $now)
                                ->whereTime('end_hour', '>=', $now);
                        });
                })
                ->count() > 0;
    }

    public function isAvailable()
    {
        return true;
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourcetable');
    }

    public function getPhotosJsonAttribute()
    {
        $array = [];
        foreach ($this->photos as $photo) {
            $array[] = [
                'source' => $photo->id,
                'options' => [
                    'type' => 'local',
                ],
            ];
        }

        return json_encode($array);
    }

    public function getCurrentPromotion(?string $locale): array
    {
        return PromotionHelper::getPromotionForDish($this, $locale);
    }

    public function getSearchUrl(): string
    {
        return route('admin.dishes.edit', ['dish' => $this->id]);
    }

    public function getSearchTitle(): string
    {
        return $this->name;
    }

    public function getSearchDescription(): string
    {
        return sprintf(
            '%s, %s',
            $this->category->name,
            (new MoneyDecorator())->decorate($this->price, 'PLN')
        );
    }

    public function getSearchPhoto(): string
    {
        return $this->photos->first() ? Croppa::url($this->photos->first()->getPhoto(true), 90, 50) : '';
    }

    public function getAdditionGroups()
    {
        $mandatory = [];
        $notMandatory = [];
        foreach ($this->additions_groups_dishes as $addition_group_dish) {
            if ($addition_group_dish->addition_group->visibility && $addition_group_dish->addition_group->additions_additions_groups->count() > 0) {
                if ($addition_group_dish->addition_group->mandatory) {
                    $mandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                } else {
                    $notMandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                }
            }
        }
        foreach ($this->category->additions_groups_categories as $addition_group_dish) {
            if ($addition_group_dish->addition_group->visibility && $addition_group_dish->addition_group->additions_additions_groups->count() > 0) {
                if ($addition_group_dish->addition_group->mandatory) {
                    $mandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                } else {
                    $notMandatory[$addition_group_dish->id] = new AdditionGroupDishResource($addition_group_dish);
                }
            }
        }

        return $mandatory + $notMandatory;
    }

    public function scopeVisible($query)
    {
        return $query->where('visibility', true)
            ->whereHas('availability', function (Builder $query) {
                $today = Availability::getWeekDay(Carbon::now()->dayOfWeek);
                $now = Carbon::now();

                $query->where($today, true);
                $query->where(function ($query) use ($now) {
                    $query->where(function ($query) use ($now) {
                        $query->whereNull('start_hour')
                            ->whereNull('end_hour');
                    })
                        ->orWhere(function ($query) use ($now) {
                            $query->whereTime('start_hour', '<=', $now)
                                ->whereTime('end_hour', '>=', $now);
                        });
                });
            })
            ->orWhereDoesntHave('availability');
    }

    public function hasDelivery()
    {
        $settings = Settings::getSetting('rodzaje_dostawy', 'delivery_address', true, false, true);

        $deliveryRange = DeliveryRange::all();

        return $this->delivery && $settings && count($deliveryRange) > 0 ? 1 : 0;
    }
}
