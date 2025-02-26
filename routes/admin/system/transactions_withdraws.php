<?php

/*
 * Transactions withdraws
 */
Route::prefix('tr_withdraws')->group(function () {
    Route::get('/', 'Admin\TrWithdrawController@index')
        ->name('admin.tr_withdraws.index')
        ->middleware('can:view,App\\Models\\User,App\\TrWithdraw');
    Route::get('/show/{tr_withdraw}', 'Admin\TrWithdrawController@show')
        ->where('addition', '[0-9]+')
        ->name('admin.tr_withdraws.show')
        ->middleware('can:view,tr_withdraw');

    Route::get('/add', 'Admin\TrWithdrawController@create')
        ->name('admin.tr_withdraws.create')
        ->middleware('can:create,App\\TrWithdraw');
    Route::post('/store', 'Admin\TrWithdrawController@store')
        ->name('admin.tr_withdraws.store')
        ->middleware('can:create,App\\TrWithdraw');

    Route::get('/edit/{tr_withdraw}', 'Admin\TrWithdrawController@edit')
        ->where('tr_withdraw', '[0-9]+')
        ->name('admin.tr_withdraws.edit')
        ->middleware('can:update,tr_withdraw');
    Route::post('/update/{tr_withdraw}', 'Admin\TrWithdrawController@update')
        ->where('tr_withdraw', '[0-9]+')
        ->name('admin.tr_withdraws.update')
        ->middleware('can:update,tr_withdraw');

    Route::delete('/delete/{tr_withdraw}', 'Admin\TrWithdrawController@delete')
        ->where('tr_withdraw', '[0-9]+')
        ->name('admin.tr_withdraws.delete')
        ->middleware('can:delete,tr_withdraw');

    Route::get('/login/{tr_withdraw}', 'Admin\TrWithdrawController@login')
        ->where('tr_withdraw', '[0-9]+')
        ->name('admin.tr_withdraws.login')
        ->middleware('can:view,App\\TrWithdraw');
});
