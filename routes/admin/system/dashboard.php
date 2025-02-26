<?php

Route::prefix('dashboard')->group(function () {
    Route::get('/restaurant', 'Admin\DashboardController@statsRestaurant')->name('admin.dashboard.restaurant');
    Route::get('/restaurants', 'Admin\DashboardController@statsRestaurants')->name('admin.dashboard.restaurants');
});
