<?php
/*
     * Additions
     */
Route::prefix('additions')->group(function () {
    Route::get('/', 'Admin\AdditionController@index')
        ->name('admin.additions.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Addition');
    Route::get('/show/{addition}', 'Admin\AdditionController@show')
        ->where('addition', '[0-9]+')
        ->name('admin.additions.show')
        ->middleware('can:view,App\\Models\\Addition');
    Route::get('/add', 'Admin\AdditionController@create')
        ->name('admin.additions.create')
        ->middleware('can:create,App\\Models\\Addition');
    Route::post('/store', 'Admin\AdditionController@store')
        ->name('admin.additions.store')
        ->middleware('can:create,App\\Models\\Addition');
    Route::get('/edit/{addition}', 'Admin\AdditionController@edit')
        ->where('addition', '[0-9]+')
        ->name('admin.additions.edit')
        ->middleware('can:update,App\\Models\\Addition');
    Route::post('/update/{addition}', 'Admin\AdditionController@update')
        ->where('addition', '[0-9]+')
        ->name('admin.additions.update')
        ->middleware('can:update,App\\Models\\Addition');
    Route::delete('/delete/{addition}', 'Admin\AdditionController@delete')
        ->where('addition', '[0-9]+')
        ->name('admin.additions.delete')
        ->middleware('can:delete,App\\Models\\Addition');
    Route::post('/modal_action', 'Admin\AdditionController@modal_action')
        ->name('admin.addition.modal_action');
    Route::post('/modal_store', 'Admin\AdditionController@modal_store')
        ->name('admin.addition.modal_store');
    Route::post('/modal_store_existing', 'Admin\AdditionController@modal_store_existing')
        ->name('admin.addition.modal_store_existing');
    Route::post('/modal_table', 'Admin\AdditionController@modal_table')
        ->name('admin.addition.modal_table');
    Route::get('/addition_groups/{id?}', 'Admin\AdditionController@addition_groups')
        ->name('admin.additions.addition_groups');
});
