<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "admin" middleware group. Now create something great!
  |
 */

Route::get('/testy', 'Admin\DashboardController@testy');

Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard.index');

/*
 *  MARKETPLACE ROUTES
 */
Route::get('/marketplace', 'Admin\MarketplaceController@index')->name('admin.marketplace.index');
Route::get('/marketplace/taxon/{code}', 'Admin\MarketplaceController@products')->name('admin.marketplace.products');
Route::get('/marketplace/product/{code}', 'Admin\MarketplaceController@product')->name('admin.marketplace.product');
Route::get('/marketplace/cart', 'Admin\MarketplaceController@cart')->name('admin.marketplace.cart');
Route::post('/marketplace/add-to-cart', 'Admin\MarketplaceController@addToCart')->name('admin.marketplace.add_to_cart');
Route::post('/marketplace/update-cart', 'Admin\MarketplaceController@updateCart')->name('admin.marketplace.update_cart');
Route::post('/marketplace/remove-from-cart', 'Admin\MarketplaceController@removeFromCart')->name('admin.marketplace.remove_from_cart');
Route::get('/marketplace/remove-cart', 'Admin\MarketplaceController@removeCart')->name('admin.marketplace.remove_cart');
//Route::get('/marketplace/complete-order', 'Admin\MarketplaceController@completeOrder')->name('admin.marketplace.complete_order');
Route::get('/marketplace/address', 'Admin\MarketplaceController@address')->name('admin.marketplace.address');
Route::get('/marketplace/new-address', 'Admin\MarketplaceController@newAddress')->name('admin.marketplace.new-address');
Route::post('/marketplace/new-address', 'Admin\MarketplaceController@addAddres')->name('admin.marketplace.addAddress');
Route::post('/marketplace/addAddress', 'Admin\MarketplaceController@addAddress')->name('admin.marketplace.addAddress');
Route::post('/marketplace/deleteAddress', 'Admin\MarketplaceController@deleteAddress')->name('admin.marketplace.deleteAddress');
Route::get('/marketplace/edit-address/{id}', 'Admin\MarketplaceController@editAddress')->name('admin.marketplace.editAddress');
Route::post('/marketplace/updateAddress/{id}', 'Admin\MarketplaceController@updateAddress')->name('admin.marketplace.updateAddress');
Route::get('/marketplace/checkout', 'Admin\MarketplaceController@checkout')->name('admin.marketplace.checkout');
//Route::post('/marketplace/checkout', 'Admin\MarketplaceController@checkout')->name('admin.marketplace.checkout');

Route::get('/marketplace/notVerified', 'Admin\MarketplaceController@notVerified')->name('admin.marketplace.notVerified');
Route::get('/marketplace/orders-history', 'Admin\MarketplaceController@orderHistory')->name('admin.marketplace.orders_history');
Route::get('/marketplace/history/{orderId}', 'Admin\MarketplaceController@getOrderHistoryDetails')->name('admin.marketplace.order_history_order_details');



/*
 * Admin auth
 */
Route::get('/change-password/{token}', 'Admin\AuthController@changePassword')->name('admin.auth.change_password');
Route::post('/change-password-submit/{token}', 'Admin\AuthController@changePasswordSubmit')->name('admin.auth.change_password_submit');
Route::get('/login', 'Admin\AuthController@create')->name('admin.auth.login');
Route::post('/login', 'Admin\AuthController@store')->name('admin.auth.secure');
Route::get('/logout', 'Admin\AuthController@destroy')->name('admin.auth.logout');

//Translations
Route::prefix('translations')->group(function () {
    Route::post('/preview', 'Admin\TranslationController@preview')->name('translations.preview');
});

//Upload
Route::prefix('upload')->group(function () {
    Route::post('/process/{id}/{namespace}/{name?}/{type?}', 'Admin\UploadController@process')
        ->name('admin.upload.process');
    Route::get('/load/{namespace}/{id?}', 'Admin\UploadController@load')
        ->name('admin.upload.load');
    Route::delete('/revert/{namespace}', 'Admin\UploadController@revert')
        ->name('admin.upload.revert');
});

if (Hyn\Tenancy\Facades\TenancyFacade::website()) {
    require_once __DIR__ . '/admin/tenant/firebase_notifications.php';
    require_once __DIR__ . '/admin/tenant/notifications.php';
    require_once __DIR__ . '/admin/tenant/dashboard.php';
    require_once __DIR__ . '/admin/tenant/addition_groups.php';
    require_once __DIR__ . '/admin/tenant/attributes.php';
    require_once __DIR__ . '/admin/tenant/attribute_groups.php';
    require_once __DIR__ . '/admin/tenant/additions.php';
    require_once __DIR__ . '/admin/tenant/bills.php';
    require_once __DIR__ . '/admin/tenant/delivery_ranges.php';
    require_once __DIR__ . '/admin/tenant/dishes.php';
    require_once __DIR__ . '/admin/tenant/food_categories.php';
    require_once __DIR__ . '/admin/tenant/orders.php';
    require_once __DIR__ . '/admin/tenant/payments.php';
    require_once __DIR__ . '/admin/tenant/player_ids.php';
    require_once __DIR__ . '/admin/tenant/promotions.php';
    require_once __DIR__ . '/admin/tenant/qr_codes.php';
    require_once __DIR__ . '/admin/tenant/labels.php';
    require_once __DIR__ . '/admin/tenant/refunds.php';
    require_once __DIR__ . '/admin/tenant/reservations.php';
    require_once __DIR__ . '/admin/tenant/rooms.php';
    require_once __DIR__ . '/admin/tenant/settings.php';
    require_once __DIR__ . '/admin/tenant/tables.php';
    require_once __DIR__ . '/admin/tenant/tags.php';
    require_once __DIR__ . '/admin/tenant/users.php';
    require_once __DIR__ . '/admin/tenant/worktimes.php';
    require_once __DIR__ . '/admin/tenant/reviews.php';
    require_once __DIR__ .'/admin/tenant/marketplace.php';

    /*
     *Translate
    */
    Route::post('/translate', 'Admin\CommonController@translateString')
        ->name('admin.common.translate');
    /*
     * Logs
     */
    Route::get('/logs', 'Admin\LogController@index')
        ->name('admin.logs.index')
        ->middleware('can:logs-sys-conf');
    /*
     * Api logs
     */
    Route::get('/api-logs', 'Admin\ApiLogController@index')
        ->name('admin.api_log.index')
        ->middleware('can:logs-sys-conf');

    //Localization
    Route::get('localization/{locale}', function ($locale) {
        Session::put('custom_locale', $locale);

        return redirect()->back();
    });
} else {
    require_once __DIR__ . '/admin/system/dashboard.php';
    require_once __DIR__ . '/admin/system/ratings.php';
    require_once __DIR__ . '/admin/system/refunds.php';
    require_once __DIR__ . '/admin/system/restaurant_tags.php';
    require_once __DIR__ . '/admin/system/restaurants.php';
    require_once __DIR__ . '/admin/system/settings.php';
    require_once __DIR__ . '/admin/system/transactions.php';
    require_once __DIR__ . '/admin/system/commissions.php';
    require_once __DIR__ . '/admin/system/transactions_withdraws.php';
    require_once __DIR__ . '/admin/system/users.php';
    require_once __DIR__ . '/admin/system/vouchers.php';
    require_once __DIR__ . '/admin/system/online_payment_provider_accounts.php';

    Route::prefix('player-ids')->group(function () {
        Route::get('/', 'Admin\PlayerIdController@index')
            ->name('admin.playerIds.index')
            ->middleware('can:view,App\\Models\\User,App\\PlayerId');
    });

    /*
     * Bills
     */
    Route::prefix('bills')->group(function () {
        Route::post('/modal_table', 'Admin\BillController@modal_table')
            ->name('admin.bills.modal_table')
            ->middleware('can:update,App\\Models\\User,App\\Bill');
    });

    /*
     * Orders
     */
    Route::prefix('orders')->group(function () {
        Route::post('/modal_table_stats', 'Admin\OrderController@modal_table_stats')
            ->name('admin.orders.modal_table_stats')
            ->middleware('can:update,App\\Models\\User,App\\Bill');
    });

    /*
     * Logs
     */
    Route::get('/logs', 'Admin\LogController@index')
        ->name('admin.logs.index')
        ->middleware('can:logs-sys-conf');

    /*
     * Api logs
     */
    Route::get('/api-logs', 'Admin\ApiLogController@index')
        ->name('admin.api_log.index')
        ->middleware('can:logs-sys-conf');

    //Localization
    Route::get('localization/{locale}', function ($locale) {
        Session::put('custom_locale', $locale);

        return redirect()->back();
    });
}

/*
 * Editable
 */
Route::put('/editable', 'Admin\CommonController@editable')
    ->name('admin.common.editable');

Route::get('/generate-pdf/{restaurantId}', [App\Http\Controllers\Admin\ReportController::class, 'index'])
    ->name('admin.report.index');

Route::get('/send-report/{restaurantId}', [App\Http\Controllers\Admin\ReportSendEmailController::class, 'index'])
    ->name('admin.report.send.index');

Route::post('/translate', 'Admin\CommonController@translateString')
    ->name('admin.common.translate');
