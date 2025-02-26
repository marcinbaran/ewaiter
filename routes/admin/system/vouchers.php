<?php

use Illuminate\Support\Facades\Route;

Log::info(request()->path());
Route::prefix('vouchers')->group(function () {
    Route::get('/', 'Admin\VoucherController@index')
        ->name('admin.vouchers.index')
        ->middleware('can:view,App\\Models\\Voucher');
    Route::get('/add', 'Admin\VoucherController@create')
        ->name('admin.vouchers.create')
        ->middleware('can:create,App\\Models\\Voucher');
    Route::post('/store', 'Admin\VoucherController@store')
        ->name('admin.vouchers.store')
        ->middleware('can:store,App\\Models\\Voucher');
    Route::get('/edit/{voucher}', 'Admin\VoucherController@edit')
        ->where('voucher', '[0-9]+')
        ->name('admin.vouchers.edit')
        ->middleware('can:edit,App\\Models\\Voucher');
    Route::post('/update/{voucher}', 'Admin\VoucherController@update')
        ->where('voucher', '[0-9]+')
        ->name('admin.vouchers.update')
        ->middleware('can:update,App\\Models\\Voucher');
    Route::delete('/delete/{voucher}', 'Admin\VoucherController@delete')
        ->where('voucher', '[0-9]+')
        ->name('admin.vouchers.delete')
        ->middleware('can:delete,App\\Models\\Voucher');
    Route::get('/adding_types', 'Admin\VoucherController@addingTypes')
        ->name('admin.vouchers.adding_types');
});
