<?php
/*
     * Promotions
     */
Route::prefix('promotions')->group(function () {
    Route::get('/', 'Admin\PromotionController@index')
        ->name('admin.promotions.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Promotion');
    Route::get('/show/{promotion}', 'Admin\PromotionController@show')
        ->where('promotion', '[0-9]+')
        ->name('admin.promotions.show')
        ->middleware('can:view,App\\Models\\Promotion');
    Route::get('/add/dish', 'Admin\PromotionController@createDish')
        ->name('admin.promotions.create.dish')
        ->middleware('can:create,App\\Models\\Promotion');
    Route::get('/add/category', 'Admin\PromotionController@createCategory')
        ->name('admin.promotions.create.category')
        ->middleware('can:create,App\\Models\\Promotion');
    Route::get('/add/bundle', 'Admin\PromotionController@createBundle')
        ->name('admin.promotions.create.bundle')
        ->middleware('can:create,App\\Models\\Promotion');
    Route::post('/store', 'Admin\PromotionController@store')
        ->name('admin.promotions.store')
        ->middleware('can:create,App\\Models\\Promotion');
    Route::get('/edit/{promotion}', 'Admin\PromotionController@edit')
        ->where('promotion', '[0-9]+')
        ->name('admin.promotions.edit')
        ->middleware('can:update,App\\Models\\Promotion');
    Route::post('/update/{promotion}', 'Admin\PromotionController@update')
        ->where('promotion', '[0-9]+')
        ->name('admin.promotions.update')
        ->middleware('can:update,App\\Models\\Promotion');
    Route::delete('/delete/{promotion}', 'Admin\PromotionController@delete')
        ->where('promotion', '[0-9]+')
        ->name('admin.promotions.delete')
        ->middleware('can:delete,App\\Models\\Promotion');
    Route::get('/dish/{id?}', 'Admin\PromotionController@dish')
        ->name('admin.promotions.dish');
    Route::get('/categories/{id?}', 'Admin\PromotionController@categories')
        ->name('admin.promotions.categories');
    Route::get('/dishes/{id?}', 'Admin\PromotionController@dishes')
    ->name('admin.promotions.dishes');
});
