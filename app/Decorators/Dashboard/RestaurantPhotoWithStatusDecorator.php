<?php

namespace App\Decorators\Dashboard;

use Illuminate\View\View;

class RestaurantPhotoWithStatusDecorator
{
    public function decorate(string $photoUrl, string $photoAlt, string $restaurantName, bool $restaurantIsActive): View
    {
        return view('admin.partials.decorators.dashboard.restaurant-photo-with-status-decorator', [
            'photoUrl' => $photoUrl,
            'photoAlt' => $photoAlt,
            'restaurantName' => $restaurantName,
            'restaurantIsActive' => $restaurantIsActive,
        ]);
    }
}
