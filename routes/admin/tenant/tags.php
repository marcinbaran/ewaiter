<?php
/*
     * Tags
     */
Route::prefix('tags')->group(function () {
    Route::get('/', 'Admin\TagController@index')
        ->name('admin.tags.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Tag');

    Route::get('/show/{tag}', 'Admin\TagController@show')
        ->where('tag', '[0-9]+')
        ->name('admin.tags.show')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Tag');

    Route::get('/add', 'Admin\TagController@create')
        ->name('admin.tags.create')
        ->middleware('can:create,App\\Models\\User,App\\Models\\Tag');

    Route::post('/store', 'Admin\TagController@store')
        ->name('admin.tags.store')
        ->middleware('can:create,App\\Models\\User,App\\Models\\Tag');

    Route::get('/edit/{tag}', 'Admin\TagController@edit')
        ->where('tag', '[0-9]+')
        ->name('admin.tags.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Tag');

    Route::post('/update/{tag}', 'Admin\TagController@update')
        ->where('tag', '[0-9]+')
        ->name('admin.tags.update')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Tag');

    Route::delete('/delete/{tag}', 'Admin\TagController@delete')
        ->where('tag', '[0-9]+')
        ->name('admin.tags.delete')
        ->middleware('can:delete,App\\Models\\User,App\\Models\\Tag');
});
