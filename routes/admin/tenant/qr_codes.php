<?php
/*
     * QR Codes
     */
Route::prefix('qr_codes')->group(function () {
    Route::get('/', 'Admin\QRCodeController@index')
        ->name('admin.qr_codes.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\QRCode');

    Route::get('/show/{qr_code}', 'Admin\QRCodeController@show')
        ->where('qr_code', '[0-9]+')
        ->name('admin.qr_codes.show')
        ->middleware('can:view,App\\Models\\QRCode');

    Route::get('/add', 'Admin\QRCodeController@create')
        ->name('admin.qr_codes.create')
        ->middleware('can:create,App\\Models\\QRCode');

    Route::post('/store', 'Admin\QRCodeController@store')
        ->name('admin.qr_codes.store')
        ->middleware('can:create,App\\Models\\User,App\\Models\\QRCode');

    Route::get('/edit/{qr_code}', 'Admin\QRCodeController@edit')
        ->where('qr_code', '[0-9]+')
        ->name('admin.qr_codes.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\QRCode');

    Route::post('/update/{qr_code}', 'Admin\QRCodeController@update')
        ->where('qr_code', '[0-9]+')
        ->name('admin.qr_codes.update')
        ->middleware('can:update,App\\Models\\User,App\\Models\\QRCode');

    Route::delete('/delete/{qr_code}', 'Admin\QRCodeController@delete')
        ->where('qr_code', '[0-9]+')
        ->name('admin.qr_codes.delete')
        ->middleware('can:delete,App\\Models\\QRCode');

    Route::get('/rooms/{id?}', 'Admin\QRCodeController@rooms')
        ->name('admin.qr_codes.rooms');

    Route::get('/tables/{id?}', 'Admin\QRCodeController@tables')
        ->name('admin.qr_codes.tables');
});
