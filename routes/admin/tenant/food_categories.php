<?php
/*
     * Food categories
     */
Route::prefix('food-categories')->group(function () {
    Route::get('/', 'Admin\FoodCategoryController@index')
        ->name('admin.categories.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\FoodCategory');

    Route::get('/show/{foodCategory}', 'Admin\FoodCategoryController@show')
        ->where('foodCategory', '[0-9]+')
        ->name('admin.categories.show')
        ->middleware('can:view,App\\Models\\User,App\\Models\\FoodCategory');

    Route::get('/add', 'Admin\FoodCategoryController@create')
        ->name('admin.categories.create')
        ->middleware('can:create,App\\Models\\FoodCategory');

    Route::get('/add-subcategory/{foodCategory}', 'Admin\FoodCategoryController@subcategory')
        ->where('foodCategory', '[0-9]+')
        ->name('admin.categories.subcategory')
        ->middleware('can:create,App\\Models\\FoodCategory');

    Route::post('/store', 'Admin\FoodCategoryController@store')
        ->name('admin.categories.store')
        ->middleware('can:create,App\\Models\\FoodCategory');

    Route::get('/edit/{foodCategory}', 'Admin\FoodCategoryController@edit')
        ->where('foodCategory', '[0-9]+')
        ->name('admin.categories.edit')
        ->middleware('can:update,App\\Models\\FoodCategory');

    Route::post('/update/{foodCategory}', 'Admin\FoodCategoryController@update')
        ->where('foodCategory', '[0-9]+')
        ->name('admin.categories.update')
        ->middleware('can:update,App\\Models\\FoodCategory');

    Route::delete('/delete/{foodCategory}', 'Admin\FoodCategoryController@delete')
        ->where('foodCategory', '[0-9]+')
        ->name('admin.categories.delete')
        ->middleware('can:delete,App\\Models\\FoodCategory');

    Route::get('/categories/{id?}', 'Admin\FoodCategoryController@categories')
        ->name('admin.categories.categories');
});
