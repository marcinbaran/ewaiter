<?php
/*
     * Users
     */
Route::prefix('users')->group(function () {
    Route::get('/', 'Admin\UserController@index')
        ->name('admin.users.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\User');

    Route::get('/show/{user}', 'Admin\UserController@show')
        ->where('user', '[0-9]+')
        ->name('admin.users.show')
        ->middleware('can:view,App\\Models\\User');

    Route::get('/add', 'Admin\UserController@create')
        ->name('admin.users.create')
        ->middleware('can:create,App\\Models\\User');

    Route::post('/store', 'Admin\UserController@store')
        ->name('admin.users.store')
        ->middleware('can:create,App\\Models\\User');

    Route::get('/edit/{user}', 'Admin\UserController@edit')
        ->where('user', '[0-9]+')
        ->name('admin.users.edit')
        ->middleware('can:update,App\\Models\\User');

    Route::post('/update/{user}', 'Admin\UserController@update')
        ->where('user', '[0-9]+')
        ->name('admin.users.update')
        ->middleware('can:update,App\\Models\\User');

    Route::delete('/delete/{user}', 'Admin\UserController@delete')
        ->where('user', '[0-9]+')
        ->name('admin.users.delete')
        ->middleware('can:delete,App\\Models\\User,App\\Models\\User');

    Route::get('/profile', 'Admin\UserController@profile')
        ->name('admin.users.profile');

    Route::post('/profile/update', 'Admin\UserController@profile_update')
        ->name('admin.users.profile.update');

    Route::get('/show_system/{user}', 'Admin\UserController@show_system')
        ->where('user', '[0-9]+')
        ->name('admin.users.show_system');

    Route::get('/roles/{id?}', 'Admin\UserController@roles')
        ->name('admin.users.roles');
});
