<?php
/*
     * Dish labels
     */
Route::prefix('labels')->group(function () {
    Route::get('/', 'Admin\LabelController@index')
        ->name('admin.labels.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Label');
    Route::get('/add', 'Admin\LabelController@create')
        ->name('admin.labels.create')
        ->middleware('can:create,App\\Models\\User,App\\Models\\Label');
    Route::post('/store', 'Admin\LabelController@store')
        ->name('admin.labels.store')
        ->middleware('can:create,App\\Models\\User,App\\Models\\Label');
    Route::get('/edit/{id}', 'Admin\LabelController@edit')
        ->where('id', '[0-9]+')
        ->name('admin.labels.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Label');
    Route::post('/update/{id}', 'Admin\LabelController@update')
        ->where('id', '[0-9]+')
        ->name('admin.labels.update')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Label');
    Route::delete('/delete/{id}', 'Admin\LabelController@delete')
        ->where('id', '[0-9]+')
        ->name('admin.labels.delete')
        ->middleware('can:delete,App\\Models\\User,App\\Models\\Label');
});
