<?php

Route::prefix('dashboard')->group(function () {
    Route::get('/orders-fullscreen', 'Admin\DashboardController@ordersFullscreen')->name('admin.dashboard.ordersFullscreen');
    Route::get('/notifications', 'Admin\DashboardController@getNotifications')->name('admin.dashboard.notifications');
    Route::put('/notifications/{id}/mark-as-read', 'Admin\DashboardController@markNotificationAsRead')->name('admin.dashboard.markNotificationAsRead');
    Route::put('/notifications/mark-all-as-read', 'Admin\DashboardController@markAllNotificationAsRead')->name('admin.dashboard.markAllNotificationAsRead');
    Route::get('/dish', 'Admin\DashboardController@statsDish')->name('admin.dashboard.dish');
    Route::get('/table', 'Admin\DashboardController@statsTable')->name('admin.dashboard.table');
    Route::get('/dish-date', 'Admin\DashboardController@statsDishDate')->name('admin.dashboard.dishDate');
    Route::get('/table-date', 'Admin\DashboardController@statsTableDate')->name('admin.dashboard.tableDate');
    Route::get('/dish-delay', 'Admin\DashboardController@statsDishDelay')->name('admin.dashboard.dishDelay');
    Route::get('/table-delay', 'Admin\DashboardController@statsTableDelay')->name('admin.dashboard.tableDelay');
    Route::get('/restaurant', 'Admin\DashboardController@statsRestaurants')->name('admin.dashboard.restaurant');
    Route::get('/new-orders', 'Admin\DashboardController@getNewOrders')->name('admin.dashboard.newOrders');
    Route::put('/accept-order', 'Admin\DashboardController@acceptBill')->name('admin.dashboard.acceptOrder');
    Route::put('/cancel-order/{id}', 'Admin\DashboardController@cancelBill')->name('admin.dashboard.cancelOrder');
    Route::get('/actual-orders', 'Admin\DashboardController@getActualOrders')->name('admin.dashboard.actualOrders');
    Route::put('/change-bill-status', 'Admin\DashboardController@updateStatus')->name('admin.dashboard.changeBillStatus');
    Route::get('/server-time', 'Admin\DashboardController@getServerTime')->name('admin.dashboard.serverTime');
    Route::put('/update-wait-time/{id}', 'Admin\DashboardController@updateWaitTime')->name('admin.dashboard.updateWaitTime');
});
