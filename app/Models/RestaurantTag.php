<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="RestaurantTag",
 *     type="object",
 *     title="Restaurant Tag",
 *     description="Model representing a tag associated with a restaurant",
 *     @OA\Property(
 *         property="key",
 *         type="string",
 *         description="The key of the tag"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="The value associated with the tag, can be different for each locale"
 *     )
 * )
 */
class RestaurantTag extends Model
{
    use ModelTrait;

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'restaurant_id',
        'tag_id',
    ];

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function restaurant_restaurant_tags()
    {
        return $this->hasMany(RestaurantsRestaurantTag::class, 'tag_id', 'id');
    }

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurants_restaurant_tags', 'tag_id', 'restaurant_id');
    }

    public static function getRows(array $criteria = ['locale' => 'pl'])
    {
        $query = self::select();

        if (! empty($criteria['id'])) {
            $query->where('id', '=', $criteria['id']);
        }
        if (! empty($criteria['restaurantId'])) {
            $query->join('restaurants_restaurant_tags', 'restaurant_tags.id', '=', 'restaurants_restaurant_tags.tag_id')->where('restaurants_restaurant_tags.restaurant_id', '=', $criteria['restaurantId']);
        }
        $locale = 'pl';
        if (! empty($criteria['locale'])) {
            $locale = $criteria['locale'];
        }

        $rows = $query->get();

        foreach ($rows as $key => $value) {
            $rows[$key]->value = $value->value[$locale];
        }

        return $rows;
    }

    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null, array $search = null)
    {
        $query = self::select();
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query = self::distinct();
            $query->orderBy('id');
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->where(
                function ($q) use ($filter) {
                    $q->where('key', 'LIKE', '%'.$filter.'%')->orWhere('value', 'LIKE', '%'.$filter.'%');
                }
            );
        }

        return $query->paginate($paginateSize, ['restaurant_tags.*']);
    }

    public static function assignNewTags(Restaurant $restaurant, array $tags)
    {
        \DB::table('restaurants_restaurant_tags')->where('restaurant_id', '=', $restaurant->id)->delete();
        $restaurant_tags = self::select()->whereIn('id', $tags)->get();
        foreach ($restaurant_tags as $restaurant_tag) {
            \DB::table('restaurants_restaurant_tags')->insert([
                'restaurant_id' => $restaurant->id,
                'tag_id' => $restaurant_tag->id,
            ]);
        }
    }
}
