<?php

Route::prefix('online_payment_provider_accounts')->group(function () {
    Route::get('/', 'Admin\OnlinePaymentProviderAccountController@index')
        ->name('admin.online_payment_provider_account.index')
        ->middleware('can:view,App\\Models\\OnlinePaymentProviderAccount');

    Route::get('/add', 'Admin\OnlinePaymentProviderAccountController@create')
        ->name('admin.online_payment_provider_account.create')
        ->middleware('can:create,App\\Models\\OnlinePaymentProviderAccount');

    Route::post('/store', 'Admin\OnlinePaymentProviderAccountController@store')
        ->name('admin.online_payment_provider_account.store')
        ->middleware('can:store,App\\Models\\OnlinePaymentProviderAccount');

    Route::get('/edit/{online_payment_provider_account}', 'Admin\OnlinePaymentProviderAccountController@edit')
        ->where('online_payment_provider_account', '[0-9]+')
        ->name('admin.online_payment_provider_account.edit')
        ->middleware('can:edit,App\\Models\\OnlinePaymentProviderAccount');

    Route::post('/update/{online_payment_provider_account}', 'Admin\OnlinePaymentProviderAccountController@update')
        ->where('online_payment_provider_account', '[0-9]+')
        ->name('admin.online_payment_provider_account.update')
        ->middleware('can:update,App\\Models\\OnlinePaymentProviderAccount');

    Route::delete('/delete/{online_payment_provider_account}', 'Admin\OnlinePaymentProviderAccountController@delete')
        ->where('online_payment_provider_account', '[0-9]+')
        ->name('admin.online_payment_provider_account.delete')
        ->middleware('can:delete,App\\Models\\OnlinePaymentProviderAccount');

    Route::get('/restaurants', 'Admin\OnlinePaymentProviderAccountController@restaurants')
        ->name('admin.online_payment_provider_account.restaurants')
        ->middleware('can:view,App\\Models\\OnlinePaymentProviderAccount');
});
