<?php
/*
     * Reservations
     */
Route::prefix('reservations')->group(function () {
    Route::get('/', 'Admin\ReservationController@index')
        ->name('admin.reservations.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Reservation');

    Route::get('/show/{reservation}', 'Admin\ReservationController@show')
        ->where('reservation', '[0-9]+')
        ->name('admin.reservations.show')
        ->middleware('can:view,App\\Models\\Reservation');

    Route::get('/add', 'Admin\ReservationController@create')
        ->name('admin.reservations.create')
        ->middleware('can:create,App\\Models\\Reservation');

    Route::post('/store', 'Admin\ReservationController@store')
        ->name('admin.reservations.store')
        ->middleware('can:create,App\\Models\\Reservation');

    Route::get('/edit/{reservation}', 'Admin\ReservationController@edit')
        ->where('reservation', '[0-9]+')
        ->name('admin.reservations.edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Reservation');

    Route::post('/update/{reservation}', 'Admin\ReservationController@update')
        ->where('reservation', '[0-9]+')
        ->name('admin.reservations.update')
        ->middleware('can:update,App\\Models\\Reservation');

    Route::delete('/delete/{reservation}', 'Admin\ReservationController@delete')
        ->where('reservation', '[0-9]+')
        ->name('admin.reservations.delete')
        ->middleware('can:delete,App\\Models\\Reservation');

    Route::get('/tables/{id?}', 'Admin\ReservationController@tables')
        ->name('admin.reservations.tables');

    Route::get('/users/{id?}', 'Admin\ReservationController@users')
        ->name('admin.reservations.users');

    Route::get('/statuses/{id?}', 'Admin\ReservationController@statuses')
        ->name('admin.reservations.statuses');
});
