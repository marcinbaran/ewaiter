<?php

/*
 * Ratings
 */
Route::prefix('ratings')->group(function () {
    Route::get('/', 'Admin\RatingController@index')
        ->name('admin.ratings.index')
        ->middleware('can:view,App\\Models\\User,App\\Rating');
    Route::get('/show/{rating}', 'Admin\RatingController@show')
        ->where('addition', '[0-9]+')
        ->name('admin.ratings.show')
        ->middleware('can:view,rating');

    //        Route::get('/add', 'Admin\RatingController@create')
    //            ->name('admin.ratings.create')
    //            ->middleware('can:create,App\\Rating');
    Route::post('/store', 'Admin\RatingController@store')
        ->name('admin.ratings.store')
        ->middleware('can:create,App\\Rating');

    Route::get('/edit/{rating}', 'Admin\RatingController@edit')
        ->where('rating', '[0-9]+')
        ->name('admin.ratings.edit')
        ->middleware('can:update,rating');
    Route::post('/update/{rating}', 'Admin\RatingController@update')
        ->where('rating', '[0-9]+')
        ->name('admin.ratings.update')
        ->middleware('can:update,rating');

    Route::delete('/delete/{rating}', 'Admin\RatingController@delete')
        ->where('rating', '[0-9]+')
        ->name('admin.ratings.delete')
        ->middleware('can:delete,rating');
});
