<?php
/*
     * Tables
     */

use Illuminate\Support\Facades\Route;

Route::prefix('tables')->group(function () {
    Route::get('/', 'Admin\TableController@index')
        ->name('admin.tables.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Table');
    Route::get('/show/{table}', 'Admin\TableController@show')
        ->where('table', '[0-9]+')
        ->name('admin.tables.show')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Table');
    Route::get('/add', 'Admin\TableController@create')
        ->name('admin.tables.create')
        ->middleware('can:create,App\\Models\\Table');
    Route::post('/store', 'Admin\TableController@store')
        ->name('admin.tables.store')
        ->middleware('can:create,App\\Models\\Table');
    Route::get('/edit/{table}', 'Admin\TableController@edit')
        ->where('table', '[0-9]+')
        ->name('admin.tables.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Table');
    Route::post('/update/{table}', 'Admin\TableController@update')
        ->where('table', '[0-9]+')
        ->name('admin.tables.update')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Table');
    Route::delete('/delete/{table}', 'Admin\TableController@delete')
        ->where('table', '[0-9]+')
        ->name('admin.tables.delete')
        ->middleware('can:delete,App\\Models\\Table');
    Route::get('/create_form_types', 'Admin\TableController@createFormTypes')
        ->name('admin.tables.create_form_types');
});
