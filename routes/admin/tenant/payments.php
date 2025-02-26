<?php
/*
     * Payments
     */

Route::prefix('payments')->group(function () {
    Route::get('/', 'Admin\PaymentController@index')
        ->name('admin.payments.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Payment');
    Route::get('/show/{payment}', 'Admin\PaymentController@show')
        ->where('payment', '[0-9]+')
        ->name('admin.payments.show')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Payment');
    Route::get('/add', 'Admin\PaymentController@create')
        ->name('admin.payments.create')
        ->middleware('can:create,App\\Models\\Payment');
    Route::post('/store', 'Admin\PaymentController@store')
        ->name('admin.payments.store')
        ->middleware('can:create,App\\Models\\Payment');
    Route::get('/edit/{payment}', 'Admin\PaymentController@edit')
        ->where('payment', '[0-9]+')
        ->name('admin.payments.edit')
        ->middleware('can:update,App\\Models\\Payment');
    Route::post('/update/{payment}', 'Admin\PaymentController@update')
        ->where('payment', '[0-9]+')
        ->name('admin.payments.update')
        ->middleware('can:update,App\\Models\\Payment');
    Route::delete('/delete/{payment}', 'Admin\PaymentController@delete')
        ->where('payment', '[0-9]+')
        ->name('admin.payments.delete')
        ->middleware('can:delete,App\\Models\\Payment');
});
