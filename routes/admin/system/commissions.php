<?php

Route::prefix('commissions')->group(function () {
    Route::get('/', 'Admin\CommissionController@index')
        ->name('admin.commissions.index')
        ->middleware('can:view,App\\Models\\User,App\\Commission');
});
