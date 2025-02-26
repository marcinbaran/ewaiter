<?php
/*
     * Restaurants
     */

Route::prefix('restaurants')->group(function () {
    Route::get('/', 'Admin\RestaurantController@index')
        ->name('admin.restaurants.index')
        ->middleware('can:view,App\\Models\\User,App\\Restaurant');

    Route::get('/show/{restaurant}', 'Admin\RestaurantController@show')
        ->where('addition', '[0-9]+')
        ->name('admin.restaurants.show')
        ->middleware('can:view,restaurant');

    Route::get('/add', 'Admin\RestaurantController@create')
        ->name('admin.restaurants.create')
        ->middleware('can:create,App\\Restaurant');
    Route::post('/store', 'Admin\RestaurantController@store')
        ->name('admin.restaurants.store')
        ->middleware('can:create,App\\Restaurant');

    Route::get('/edit/{restaurant}', 'Admin\RestaurantController@edit')
        ->where('restaurant', '[0-9]+')
        ->name('admin.restaurants.edit');
    // ->middleware('can:update,restaurant');
    Route::post('/update/{restaurant}', 'Admin\RestaurantController@update')
        ->where('restaurant', '[0-9]+')
        ->name('admin.restaurants.update')
        ->middleware('can:update,restaurant');

    Route::delete('/delete/{restaurant}', 'Admin\RestaurantController@delete')
        ->where('restaurant', '[0-9]+')
        ->name('admin.restaurants.delete')
        ->middleware('can:delete,restaurant');

    Route::get('/login/{restaurant}', 'Admin\RestaurantController@login')
        ->where('restaurant', '[0-9]+')
        ->name('admin.restaurants.login')
        ->middleware('can:view,App\\Restaurant');

    Route::get('/restaurant_tags/{id?}', 'Admin\RestaurantController@restaurant_tags')
        ->name('admin.restaurants.restaurant_tags');
});
