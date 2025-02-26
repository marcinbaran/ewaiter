<?php
/*
     * Rooms
     */
Route::prefix('rooms')->group(function () {
    Route::get('/', 'Admin\RoomController@index')
        ->name('admin.rooms.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Room');
    Route::get('/show/{room}', 'Admin\RoomController@show')
        ->where('room', '[0-9]+')
        ->name('admin.rooms.show')
        ->middleware('can:view,App\\Models\\Room');
    Route::get('/add', 'Admin\RoomController@create')
        ->name('admin.rooms.create')
        ->middleware('can:create,App\\Models\\Room');
    Route::post('/store', 'Admin\RoomController@store')
        ->name('admin.rooms.store')
        ->middleware('can:create,App\\Models\\Room');
    Route::get('/edit/{room}', 'Admin\RoomController@edit')
        ->where('room', '[0-9]+')
        ->name('admin.rooms.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Room');
    Route::post('/update/{room}', 'Admin\RoomController@update')
        ->where('room', '[0-9]+')
        ->name('admin.rooms.update')
        ->middleware('can:update,App\\Models\\Room');
    Route::delete('/delete/{room}', 'Admin\RoomController@delete')
        ->where('room', '[0-9]+')
        ->name('admin.rooms.delete')
        ->middleware('can:delete,App\\Models\\Room');
});
