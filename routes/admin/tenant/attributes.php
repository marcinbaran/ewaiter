<?php
/*
     * Additions
     */
Route::prefix('attributes')->group(function () {
    Route::get('/', 'Admin\AttributeController@index')
        ->name('admin.attributes.index')
        ->middleware('can:view,App\\Models\\Attribute');
    Route::get('/add', 'Admin\AttributeController@create')
        ->name('admin.attributes.create')
        ->middleware('can:create,App\\Models\\Attribute');
    Route::post('/store', 'Admin\AttributeController@store')
        ->name('admin.attributes.store')
        ->middleware('can:create,App\\Models\\Attribute');
    Route::get('/edit/{attribute}', 'Admin\AttributeController@edit')
        ->where('attribute', '[0-9]+')
        ->name('admin.attributes.edit')
        ->middleware('can:update,App\\Models\\Attribute');
    Route::post('/update/{attribute}', 'Admin\AttributeController@update')
        ->where('attribute', '[0-9]+')
        ->name('admin.attributes.update')
        ->middleware('can:update,App\\Models\\Attribute');
    Route::delete('/delete/{attribute}', 'Admin\AttributeController@delete')
        ->where('attribute', '[0-9]+')
        ->name('admin.attributes.delete')
        ->middleware('can:delete,App\\Models\\Attribute');
    Route::get('/attribute_group/{id?}', 'Admin\AttributeController@attribute_group')
        ->name('admin.attributes.attribute_group');
});
