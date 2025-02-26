<?php
/*
     * Settings
     */
Route::prefix('settings')->group(function () {
    Route::get('/', 'Admin\SettingsController@index')
        ->name('admin.settings.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Settings');

    Route::get('/show/{settings}', 'Admin\SettingsController@show')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.show')
        ->middleware('can:view,App\\Models\\Settings');

    Route::get('/add', 'Admin\SettingsController@create')
        ->name('admin.settings.create')
        ->middleware('can:create,App\\Models\\Settings');

    Route::post('/store', 'Admin\SettingsController@store')
        ->name('admin.settings.store')
        ->middleware('can:create,App\\Models\\Settings');

    Route::get('/edit/{settings}', 'Admin\SettingsController@edit')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.edit')
        ->middleware('can:edit,App\\Models\\Settings');

    Route::post('/update/{settings}', 'Admin\SettingsController@update')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.update')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Settings');

    Route::delete('/delete/{settings}', 'Admin\SettingsController@delete')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.delete')
        ->middleware('can:delete,App\\Models\\Settings');

    Route::post('/modal_delivery', 'Admin\SettingsController@modal_delivery')
        ->name('admin.settings.modal_delivery');

    Route::post('/delivery_store', 'Admin\SettingsController@delivery_store')
        ->name('admin.settings.delivery_store');

    Route::post('/createTpayAccount/{settings}', 'Admin\SettingsController@createAsTenant')
        ->name('admin.settings.createTpayAccount')
        ->where('settings', '[0-9]+')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Settings');
});
