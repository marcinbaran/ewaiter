<?php

/*
 * Settings
 */
Route::prefix('settings')->group(function () {
    Route::get('/', 'Admin\SettingsController@index')
        ->name('admin.settings.index')
        ->middleware('can:view,App\\Models\\User,App\\Settings');
    Route::get('/show/{settings}', 'Admin\SettingsController@show')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.show')
        ->middleware('can:view,settings');

    Route::get('/add', 'Admin\SettingsController@create')
        ->name('admin.settings.create')
        ->middleware('can:create,App\\Settings');
    Route::post('/store', 'Admin\SettingsController@store')
        ->name('admin.settings.store')
        ->middleware('can:create,App\\Settings');

    Route::get('/edit/{settings}', 'Admin\SettingsController@edit')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.edit')
        ->middleware('can:update,settings');
    Route::post('/update/{settings}', 'Admin\SettingsController@update')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.update')
        ->middleware('can:update,settings');

    Route::delete('/delete/{settings}', 'Admin\SettingsController@delete')
        ->where('settings', '[0-9]+')
        ->name('admin.settings.delete')
        ->middleware('can:delete,settings');
});
