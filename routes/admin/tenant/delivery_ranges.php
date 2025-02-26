<?php
/*
     * DeliveryRanges
     */
Route::prefix('delivery_ranges')->group(function () {
    Route::get('/', 'Admin\DeliveryRangeController@index')
        ->name('admin.delivery_ranges.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\DeliveryRange');
    Route::get('/show/{delivery_range}', 'Admin\DeliveryRangeController@show')
        ->where('delivery_range', '[0-9]+')
        ->name('admin.delivery_ranges.show')
        ->middleware('can:view,App\\Models\\DeliveryRange');
    Route::get('/add', 'Admin\DeliveryRangeController@create')
        ->name('admin.delivery_ranges.create')
        ->middleware('can:create,App\\Models\\DeliveryRange');
    Route::post('/store', 'Admin\DeliveryRangeController@store')
        ->name('admin.delivery_ranges.store')
        ->middleware('can:create,App\\Models\\DeliveryRange');
    Route::get('/edit/{delivery_range}', 'Admin\DeliveryRangeController@edit')
        ->where('delivery_range', '[0-9]+')
        ->name('admin.delivery_ranges.edit')
        ->middleware('can:update,App\\Models\\DeliveryRange');
    Route::post('/update/{delivery_range}', 'Admin\DeliveryRangeController@update')
        ->where('delivery_range', '[0-9]+')
        ->name('admin.delivery_ranges.update')
        ->middleware('can:update,App\\Models\\DeliveryRange');
    Route::delete('/delete/{delivery_range}', 'Admin\DeliveryRangeController@delete')
        ->where('delivery_range', '[0-9]+')
        ->name('admin.delivery_ranges.delete')
        ->middleware('can:delete,App\\Models\\DeliveryRange');
    Route::get('/coordinates', 'Admin\DeliveryRangeController@getDeliveryRangesCoordinatesArray')
        ->name('admin.delivery_ranges.coordinates')
        ->middleware('can:view,App\\Models\\User,App\\Models\\DeliveryRange');

});
