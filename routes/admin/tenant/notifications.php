<?php

use App\Http\Controllers\Admin\NotificationController;

/*
     * Notifications
     */
Route::prefix('notifications')->group(function () {
    Route::post('/complete/{id}', [NotificationController::class, 'complete'])
        ->where('id', '[0-9]+')
        ->name('admin.notifications.complete');
});
