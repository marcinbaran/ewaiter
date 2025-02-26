<?php

/*
 * Restaurant_tags
 */
Route::prefix('restaurant_tags')->group(function () {
    Route::get('/', 'Admin\RestaurantTagController@index')
        ->name('admin.restaurant_tags.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\RestaurantTag');

    Route::get('/show/{restaurant_tag}', 'Admin\RestaurantTagController@show')
        ->name('admin.restaurant_tags.show');
    //            ->middleware('can:view,dish');

    Route::get('/add', 'Admin\RestaurantTagController@create')
        ->name('admin.restaurant_tags.create')
        ->middleware('can:create,App\\Models\\RestaurantTag');

    Route::post('/store', 'Admin\RestaurantTagController@store')
        ->name('admin.restaurant_tags.store')
        ->middleware('can:create,App\\Models\\RestaurantTag');

    Route::get('/edit/{restaurant_tag}', 'Admin\RestaurantTagController@edit')
        ->where('restaurant_tag', '[0-9]+')
        ->name('admin.restaurant_tags.edit')
        ->middleware('can:update,App\\Models\\RestaurantTag');

    Route::post('/update/{restaurant_tag}', 'Admin\RestaurantTagController@update')
        ->where('restaurant_tag', '[0-9]+')
        ->name('admin.restaurant_tags.update')
        ->middleware('can:update,App\\Models\\RestaurantTag');

    Route::delete('/delete/{restaurant_tag}', 'Admin\RestaurantTagController@delete')
        ->where('restaurant_tag', '[0-9]+')
        ->name('admin.restaurant_tags.delete')
        ->middleware('can:delete,App\\Models\\RestaurantTag');
});
