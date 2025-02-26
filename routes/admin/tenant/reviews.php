<?php

/*
 * Reviews
 */
Route::prefix('reviews')->group(function () {

    Route::get('/', 'Admin\ReviewController@index')
        ->name('admin.reviews.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Review');

    Route::get('/show/{review}', 'Admin\ReviewController@show')
        ->where('review', '[0-9]+')
        ->name('admin.reviews.show')
        ->middleware('can:view,App\\Models\\User');

    Route::patch('/update/{review}', 'Admin\ReviewController@update')
        ->where('review', '[0-9]+')
        ->name('admin.reviews.update')
        ->middleware('can:update,App\\Models\\User');
});

?>
