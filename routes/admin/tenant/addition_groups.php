<?php
/*
     * Additions groups
     */
Route::prefix('additions-groups')->group(function () {
    Route::get('/', 'Admin\AdditionGroupController@index')
        ->name('admin.additions_groups.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\AdditionGroup');

    Route::get('/show/{addition_group}', 'Admin\AdditionGroupController@show')
        ->where('addition_group', '[0-9]+')
        ->name('admin.additions_groups.show')
        ->middleware('can:view,App\\Models\\AdditionGroup');

    Route::get('/add', 'Admin\AdditionGroupController@create')
        ->name('admin.additions_groups.create')
        ->middleware('can:create,App\\Models\\AdditionGroup');

    Route::post('/store', 'Admin\AdditionGroupController@store')
        ->name('admin.additions_groups.store')
        ->middleware('can:create,App\\Models\\AdditionGroup');

    Route::get('/edit/{addition_group}', 'Admin\AdditionGroupController@edit')
        ->where('addition_group', '[0-9]+')
        ->name('admin.additions_groups.edit')
        ->middleware('can:update,App\\Models\\AdditionGroup');

    Route::post('/update/{addition_group}', 'Admin\AdditionGroupController@update')
        ->where('addition_group', '[0-9]+')
        ->name('admin.additions_groups.update')
        ->middleware('can:update,App\\Models\\AdditionGroup');

    Route::get('/duplicate/{addition_group}', 'Admin\AdditionGroupController@duplicate')
        ->where('addition_group', '[0-9]+')
        ->name('admin.additions_groups.duplicate')
        ->middleware('can:update,App\\Models\\AdditionGroup');

    Route::delete('/delete/{addition_group}', 'Admin\AdditionGroupController@delete')
        ->where('addition_group', '[0-9]+')
        ->name('admin.additions_groups.delete')
        ->middleware('can:delete,App\\Models\\AdditionGroup');

    Route::get('/categories/{id?}', 'Admin\AdditionGroupController@categories')
        ->name('admin.additions_groups.categories');

    Route::get('/dishes/{id?}', 'Admin\AdditionGroupController@dishes')
        ->name('admin.additions_groups.dishes');

    Route::get('/addition_group_types/{id?}', 'Admin\AdditionGroupController@additionGroupTypes')
        ->name('admin.additions_groups.addition_group_types');
});
