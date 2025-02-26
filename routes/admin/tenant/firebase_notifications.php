<?php
/*
     * Notifications
     */
Route::prefix('firebase_notifications')->group(function () {
    Route::get('/show/{notification}', 'Admin\FirebaseNotificationController@show')
        ->where('notification', '[0-9]+')
        ->name('admin.firebase_notifications.show');
    Route::get('/read', 'Admin\FirebaseNotificationController@read')
        ->name('admin.firebase_notifications.read');
    Route::get('/reload', 'Admin\FirebaseNotificationController@reload')
        ->name('admin.firebase_notifications.reload');
    Route::get('/refresh', 'Admin\FirebaseNotificationController@refresh')
        ->name('admin.firebase_notifications.refresh');
});
