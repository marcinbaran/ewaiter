<?php

///*
//     * Refunds
//     */
//Route::prefix('refunds')->group(function () {
//    Route::get('/', 'Admin\RefundController@index')
//        ->name('admin.refunds.index')
//        ->middleware('can:view,App\\Models\\User,App\\Models\\Refund');
//    Route::get('/show/{refund}', 'Admin\RefundController@show')
//        ->where('refund', '[0-9]+')
//        ->name('admin.refunds.show')
//        ->middleware('can:view,App\\Models\\User,App\\Models\\Refund');
//    Route::get('/add', 'Admin\RefundController@create')
//        ->name('admin.refunds.create')
//        ->middleware('can:create,App\\Models\\Refund');
//    Route::post('/store', 'Admin\RefundController@store')
//        ->name('admin.refunds.store')
//        ->middleware('can:create,App\\Models\\User,App\\Models\\Refund');
//    Route::get('/edit/{refund}', 'Admin\RefundController@edit')
//        ->where('refund', '[0-9]+')
//        ->name('admin.refunds.App\\Models\\User,App\\Models\\Refund')
//        ->middleware('can:update,App\\Models\\User,App\\Models\\Refund');
//    Route::post('/update/{refund}', 'Admin\RefundController@update')
//        ->where('refund', '[0-9]+')
//        ->name('admin.refunds.update')
//        ->middleware('can:update,App\\Models\\User,App\\Models\\Refund');
//    Route::delete('/delete/{refund}', 'Admin\RefundController@delete')
//        ->where('refund', '[0-9]+')
//        ->name('admin.refunds.delete')
//        ->middleware('can:delete,App\\Models\\Refund');
//    Route::get('/unlock_refund/{refund}', 'Admin\RefundController@unlock_refund')
//        ->where('refund', '[0-9]+')
//        ->name('admin.refunds.unlock_refund')
//        ->middleware('can:update,App\\Models\\User,App\\Models\\Refund');
//});
