<?php
/*
     * Orders
     */
Route::prefix('orders')->group(function () {
    Route::post('/status_edit', 'Admin\OrderController@status_edit')
        ->name('admin.orders.status_edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Order');
    Route::get('/', 'Admin\OrderController@index')
        ->name('admin.orders.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Order');
    Route::get('/show/{order}', 'Admin\OrderController@show')
        ->where('order', '[0-9]+')
        ->name('admin.orders.show')
        ->middleware('can:view,App\\Models\\Order');
    Route::get('/add', 'Admin\OrderController@create')
        ->name('admin.orders.create')
        ->middleware('can:create,App\\Models\\Order');
    Route::post('/store', 'Admin\OrderController@store')
        ->name('admin.orders.store')
        ->middleware('can:create,App\\Models\\Order');
    Route::get('/edit/{order}', 'Admin\OrderController@edit')
        ->where('order', '[0-9]+')
        ->name('admin.orders.edit')
        ->middleware('can:update,App\\Models\\Order');
    Route::post('/update/{order}', 'Admin\OrderController@update')
        ->where('order', '[0-9]+')
        ->name('admin.orders.update')
        ->middleware('can:update,App\\Models\\Order');
    Route::delete('/delete/{order}', 'Admin\OrderController@delete')
        ->where('order', '[0-9]+')
        ->name('admin.orders.delete')
        ->middleware('can:delete,App\\Models\\Order');
    Route::post('/modal_table', 'Admin\OrderController@modal_table')
        ->name('admin.orders.modal_table');
    Route::post('/modal_table_stats', 'Admin\OrderController@modal_table_stats')
        ->name('admin.orders.modal_table_stats')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
});
