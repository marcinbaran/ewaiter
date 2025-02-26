<?php
/*
     * PalyerIds
     */
Route::prefix('player-ids')->group(function () {
    Route::get('/', 'Admin\PlayerIdController@index')
        ->name('admin.playerIds.index')
        ->middleware('can:view,App\\Models\\User,App\\PlayerId');
    Route::get('/show/{playerId}', 'Admin\PlayerIdController@show')
        ->where('playerId', '[0-9]+')
        ->name('admin.playerIds.show')
        ->middleware('can:view,playerId');

    Route::get('/add', 'Admin\PlayerIdController@create')
        ->name('admin.playerIds.create')
        ->middleware('can:create,App\\PlayerId');
    Route::post('/store', 'Admin\PlayerIdController@store')
        ->name('admin.playerIds.store')
        ->middleware('can:create,App\\PlayerId');

    Route::get('/edit/{player-id}', 'Admin\PlayerIdController@edit')
        ->where('player-id', '[0-9]+')
        ->name('admin.playerIds.edit')
        ->middleware('can:update,playerId');
    Route::post('/update/{player-id}', 'Admin\PlayerIdController@update')
        ->where('player-id', '[0-9]+')
        ->name('admin.playerIds.update')
        ->middleware('can:update,playerId');

    Route::delete('/delete/{player-id}', 'Admin\PlayerIdController@delete')
        ->where('player-id', '[0-9]+')
        ->name('admin.playerIds.delete')
        ->middleware('can:delete,playerId');
});
