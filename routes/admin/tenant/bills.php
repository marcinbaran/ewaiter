<?php
/*
     * Bills
     */
Route::prefix('bills')->group(function () {
    Route::get('/', 'Admin\BillController@index')
        ->name('admin.bills.index')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Bill');
    Route::get('/show/{bill}', 'Admin\BillController@show')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.show')
        ->middleware('can:view,App\\Models\\User,App\\Models\\Bill');
    Route::get('/add', 'Admin\BillController@create')
        ->name('admin.bills.create')
        ->middleware('can:create,App\\Models\\Bill');
    Route::post('/store', 'Admin\BillController@store')
        ->name('admin.bills.store')
        ->middleware('can:create,App\\Models\\Bill');
    Route::get('/edit/{bill}', 'Admin\BillController@edit')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.edit')
        ->middleware('can:update,App\\Models\\Bill');
    Route::post('/update/{bill}', 'Admin\BillController@update')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.update')
        ->middleware('can:update,App\\Models\\Bill');
    Route::delete('/delete/{bill}', 'Admin\BillController@delete')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.delete')
        ->middleware('can:delete,App\\Models\\Bill');
    Route::post('/status_edit', 'Admin\BillController@status_edit')
        ->name('admin.bills.status_edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::post('/paid_edit', 'Admin\BillController@paid_edit')
        ->name('admin.bills.paid_edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::post('/time_wait_edit', 'Admin\BillController@time_wait_edit')
        ->name('admin.bills.time_wait_edit')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::post('/modal_table', 'Admin\BillController@modal_table')
        ->name('admin.bills.modal_table')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::get('/accept/{bill}', 'Admin\BillController@accept')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.accept')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::get('/ready/{bill}', 'Admin\BillController@ready')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.ready')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
    Route::post('/refund/{bill}', 'Admin\BillController@refund')
        ->where('bill', '[0-9]+')
        ->name('admin.bills.refund')
        ->middleware('can:update,App\\Models\\User,App\\Models\\Bill');
});
