<?php

/*
 * Refunds
 */
Route::prefix('refunds')->group(function () {
    Route::get('/show/{refund}', 'Admin\RefundController@show')
        ->where('refund', '[0-9]+')
        ->name('admin.refunds.show')
        ->middleware('can:view,refund');
});
