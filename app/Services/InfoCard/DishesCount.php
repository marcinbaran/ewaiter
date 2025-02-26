<?php

namespace App\Services\InfoCard;

use App\Models\Restaurant;
use Carbon\Carbon;

class DishesCount extends AbstractCard
{
    private ?Carbon $start;

    private ?Carbon $end;

    /**
     * @param \Carbon\Carbon|null $start
     * @param \Carbon\Carbon|null $end
     */
    public function __construct(?Carbon $start = null, ?Carbon $end = null)
    {
        $this->start = $start;
        $this->end = $end;

        $this->title = __('admin.Dishes count');
        $this->color = 'danger';
        $this->icon = 'database';
    }

    public function setValue()
    {
        $count = 0;
//        $restaurants = Restaurant::query()->get();
        $restaurants = Restaurant::query()->where('name', 'like', '%105%')->get();
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant) {
            $count += $restaurant->getDishesCount();
        }

        $this->value = $count;
    }
}
