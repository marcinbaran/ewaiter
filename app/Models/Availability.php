<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * @var array
     */
    protected $fillable = [
        'dish_id',
        'food_category_id',
        'm',
        't',
        'w',
        'r',
        'f',
        'u',
        's',
        'start_hour',
        'end_hour',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    private static $weekDay = [
        0 => 's',
        1 => 'm',
        2 => 't',
        3 => 'w',
        4 => 'r',
        5 => 'f',
        6 => 'u',
    ];

    public static function getWeekDay(int $day)
    {
        throw_if(! isset(self::$weekDay[$day]), new \Exception('Wrong type of day!'));

        return self::$weekDay[$day];
    }

    /**
     * @return BelongsTo
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function food_category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id', 'id');
    }
}
