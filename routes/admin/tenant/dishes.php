<?php
/*
     * Dishes
     */
Route::prefix('dishes')->group(function () {
    Route::get('/', 'Admin\DishController@index')
        ->name('admin.dishes.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Dish');

    Route::get('/show/{dish}', 'Admin\DishController@show')
        ->where('dish', '[0-9]+')
        ->name('admin.dishes.show')
        ->middleware('can:view,App\\Models\\Dish');

    Route::get('/add', 'Admin\DishController@create')
        ->name('admin.dishes.create')
        ->middleware('can:create,App\\Models\\Dish');

    Route::post('/store', 'Admin\DishController@store')
        ->name('admin.dishes.store')
        ->middleware('can:create,App\\Models\\Dish');

    Route::get('/edit/{dish}', 'Admin\DishController@edit')
        ->where('dish', '[0-9]+')
        ->name('admin.dishes.edit')
        ->middleware('can:update,App\\Models\\Dish');

    Route::post('/update/{dish}', 'Admin\DishController@update')
        ->where('dish', '[0-9]+')
        ->name('admin.dishes.update')
        ->middleware('can:update,App\\Models\\Dish');

    Route::delete('/delete/{dish}', 'Admin\DishController@delete')
        ->where('dish', '[0-9]+')
        ->name('admin.dishes.delete')
        ->middleware('can:delete,App\\Models\\Dish');

    Route::get('/tags/{id?}', 'Admin\DishController@tags')
        ->name('admin.dishes.tags');

    Route::get('/additionGroups/{id?}', 'Admin\DishController@additionGroups')
        ->name('admin.dishes.additionGroups');

    Route::get('/categories/{id?}', 'Admin\DishController@categories')
        ->name('admin.dishes.categories');

    Route::get('/labels/{id?}', 'Admin\DishController@labels')
        ->name('admin.dishes.labels');

    Route::get('/attributes/{id?}', 'Admin\DishController@attributes')
        ->name('admin.dishes.attributes');
});
