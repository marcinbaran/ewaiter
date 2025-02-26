<?php

/*
 * Transactions
 */
Route::prefix('transactions')->group(function () {
    Route::get('/', 'Admin\TransactionController@index')
        ->name('admin.transactions.index')
        ->middleware('can:view,App\\Models\\User,App\\Transaction');
    Route::get('/show/{transaction}', 'Admin\TransactionController@show')
        ->where('addition', '[0-9]+')
        ->name('admin.transactions.show')
        ->middleware('can:view,transaction');

    Route::get('/add', 'Admin\TransactionController@create')
        ->name('admin.transactions.create')
        ->middleware('can:create,App\\Transaction');
    Route::post('/store', 'Admin\TransactionController@store')
        ->name('admin.transactions.store')
        ->middleware('can:create,App\\Transaction');

    Route::get('/edit/{transaction}', 'Admin\TransactionController@edit')
        ->where('transaction', '[0-9]+')
        ->name('admin.transactions.edit')
        ->middleware('can:update,transaction');
    Route::post('/update/{transaction}', 'Admin\TransactionController@update')
        ->where('transaction', '[0-9]+')
        ->name('admin.transactions.update')
        ->middleware('can:update,transaction');

    Route::delete('/delete/{transaction}', 'Admin\TransactionController@delete')
        ->where('transaction', '[0-9]+')
        ->name('admin.transactions.delete')
        ->middleware('can:delete,transaction');

    Route::get('/login/{transaction}', 'Admin\TransactionController@login')
        ->where('transaction', '[0-9]+')
        ->name('admin.transactions.login')
        ->middleware('can:view,App\\Transaction');
});
