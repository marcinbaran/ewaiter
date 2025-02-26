<?php
/*
 * Attribute groups
 */
Route::prefix('attribute-groups')->group(function () {
    Route::get('/', 'Admin\AttributeGroupController@index')
        ->name('admin.attribute_groups.index')
        ->middleware('can:view,App\\Models\\AttributeGroup');
    Route::get('/add', 'Admin\AttributeGroupController@create')
        ->name('admin.attribute_groups.create')
        ->middleware('can:create,App\\Models\\AttributeGroup');
    Route::post('/store', 'Admin\AttributeGroupController@store')
        ->name('admin.attribute_groups.store')
        ->middleware('can:create,App\\Models\\AttributeGroup');
    Route::get('/edit/{attribute_group}', 'Admin\AttributeGroupController@edit')
        ->where('attribute_group', '[0-9]+')
        ->name('admin.attribute_groups.edit')
        ->middleware('can:update,App\\Models\\AttributeGroup');
    Route::post('/update/{attribute_group}', 'Admin\AttributeGroupController@update')
        ->where('attribute_group', '[0-9]+')
        ->name('admin.attribute_groups.update')
        ->middleware('can:update,App\\Models\\AttributeGroup');
    Route::delete('/delete/{attribute_group}', 'Admin\AttributeGroupController@delete')
        ->where('attribute_group', '[0-9]+')
        ->name('admin.attribute_groups.delete')
        ->middleware('can:delete,App\\Models\\AttributeGroup');
    Route::get('/input_types/{id?}', 'Admin\AttributeGroupController@input_types')
        ->name('admin.attribute_groups.input_types');
});
