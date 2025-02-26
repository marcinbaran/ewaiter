<?php
/*
     * Worktimes
     */
Route::prefix('worktimes')->group(function () {
    Route::get('/', 'Admin\WorktimeController@index')
        ->name('admin.worktimes.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Worktime');
    Route::get('/show/{worktime}', 'Admin\WorktimeController@show')
        ->where('worktime', '[0-9]+')
        ->name('admin.worktimes.show')
        ->middleware('can:view,App\\Models\\Worktime');
    Route::get('/add', 'Admin\WorktimeController@create')
        ->name('admin.worktimes.create')
        ->middleware('can:create,App\\Models\\Worktime');
    Route::post('/store', 'Admin\WorktimeController@store')
        ->name('admin.worktimes.store')
        ->middleware('can:create,App\\Models\\Worktime');
    Route::get('/edit/{worktime}', 'Admin\WorktimeController@edit')
        ->where('worktime', '[0-9]+')
        ->name('admin.worktimes.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Worktime');
    Route::post('/update/{worktime}', 'Admin\WorktimeController@update')
        ->where('worktime', '[0-9]+')
        ->name('admin.worktimes.update')
        ->middleware('can:update,App\\Models\\Worktime');
    Route::delete('/delete/{worktime}', 'Admin\WorktimeController@delete')
        ->where('worktime', '[0-9]+')
        ->name('admin.worktimes.delete')
        ->middleware('can:delete,App\\Models\\Worktime');
});
