<?php

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

use App\Http\Controllers\Api;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\ContactFormController;


//\Illuminate\Support\Facades\Log::info(request()->path());

Route::post('auth', 'App\Http\Controllers\Api\AuthController@authenticate');
Route::post('auth_guest', 'App\Http\Controllers\Api\AuthController@authenticate_guest');
Route::post('refresh-token', 'App\Http\Controllers\Api\AuthController@refreshToken');
Route::post('remind-password', 'App\Http\Controllers\Api\AuthController@remindPassword');
Route::post('/login-external', 'App\Http\Controllers\Api\AuthController@loginExternal')->name('admin.auth.login-external');


Route::post('/users_auth_code/{user}', 'App\Http\Controllers\Api\UserController@users_auth_code')->where('user', '[0-9]+')->name('admin.users_auth_code');

Route::delete('users/delete-user', 'App\Http\Controllers\Api\UserController@deleteAccount');

Route::post('/users_auth_code_again/{user}', 'App\Http\Controllers\Api\UserController@users_auth_code_again')
    ->where('user', '[0-9]+')
    ->name('admin.users_auth_code_again');

// Password Recovery
Route::prefix('reset-password')->group(function () {
    Route::post('/', 'App\Http\Controllers\Api\PasswordRecoveryController@requestSMSCode');
    Route::put('/', 'App\Http\Controllers\Api\PasswordRecoveryController@setNewPassword');
});

//Users
Route::get('users/me', Api\UserController::class . '@me');
Route::post('users/set-phone', Api\UserController::class . '@setPhone');
Route::post('users/verify-auth-code', Api\UserController::class . '@verifyAuthCode');
Route::resource('users', Api\UserController::class, ['only' => ['index', 'show', 'store', 'store', 'update', 'destroy']]);

//Addresses
Route::resource('addresses', Api\AddressController::class, ['only' => ['index', 'show', 'store', 'store', 'update', 'destroy']]);

/* CQRS Actions */
Route::post('review', App\Http\Controllers\Actions\Api\Review\CreateReviewCommandAction::class);
Route::get('review', App\Http\Controllers\Actions\Api\Review\GetReviewsQueryAction::class);
Route::delete('review', App\Http\Controllers\Actions\Api\Review\DeleteReviewCommandAction::class);
Route::put('review', App\Http\Controllers\Actions\Api\Review\UpdateReviewCommandAction::class);

$website = Hyn\Tenancy\Facades\TenancyFacade::website();
if ($website) {
    /* CQRS Actions */
    // Orders
    Route::post('place_order', App\Http\Controllers\Actions\Api\Order\PlaceOrderCommandAction::class);
    Route::post('validate_cart', App\Http\Controllers\Actions\Api\Order\ValidateCartCommandAction::class);

    /* Attributes */
    Route::post('attribute', App\Http\Controllers\Actions\Api\Attributes\CreateAttributeCommandAction::class);
    Route::get('attribute', App\Http\Controllers\Actions\Api\Attributes\GetAttributesQueryAction::class);
    Route::put('attribute', App\Http\Controllers\Actions\Api\Attributes\UpdateAttributeCommandAction::class);
    Route::delete('attribute', App\Http\Controllers\Actions\Api\Attributes\DeleteAttributeCommandAction::class);

    /* Attributes Groups */
    Route::post('attribute-group', App\Http\Controllers\Actions\Api\AttributeGroups\CreateAttributeGroupCommandAction::class);
    Route::get('attribute-group', App\Http\Controllers\Actions\Api\AttributeGroups\GetAttributeGroupsQueryAction::class);
    Route::put('attribute-group', App\Http\Controllers\Actions\Api\AttributeGroups\UpdateAttributeGroupCommandAction::class);
    Route::delete('attribute-group', App\Http\Controllers\Actions\Api\AttributeGroups\DeleteAttributeGroupCommandAction::class);

    /* Restaurant */
    Route::post('save-visit', App\Http\Controllers\Actions\Api\Restaurant\SaveVisitAction::class);

    /* Controllers */
    //Settings
    Route::resource('settings', Api\SettingsController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Tables
    Route::resource('tables', Api\TableController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Notifications
    Route::resource('notifications', Api\NotificationController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Bills
    Route::resource('bills', Api\BillController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Dishes
    Route::post('filter_dishes', Api\DishController::class . '@filter'); // TODO: THIS IS HOTFIX AND NEEDS TO BE REFACTORED
    Route::resource('dishes', Api\DishController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Additions
    Route::resource('additions', Api\AdditionController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    // TODO: Controller not exist check if needed?
    //Additions groups
    Route::resource('additions-groups', Api\AdditionGroupController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Food Categories
    Route::prefix('food-categories')->group(function () {
        Route::get('/all-with-dishes', Api\FoodCategoryController::class . '@allWithDishes')->name('api.food-categories.all-with-dishes');
    });
    Route::resource('food-categories', Api\FoodCategoryController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Player ids
    Route::resource('player-ids', Api\PlayerIdController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Orders
    Route::resource('orders', Api\OrderController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Promotion
    Route::resource('promotions', Api\PromotionController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Worktime
    Route::resource('worktimes', Api\WorktimeController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Reservation
    Route::resource('reservations', Api\ReservationController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Tags
    Route::resource('tags', Api\TagController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Refunds
    Route::resource('refunds', Api\RefundController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Delivery ranges
    Route::resource('delivery_ranges', Api\DeliveryRangeController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Transactions
//    Route::resource('transactions', Api\TransactionController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //TrWithdrawsDatatable
//    Route::resource('tr_withdraws', Api\TrWithdrawController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //reservation
    Route::prefix('reservation')->group(function () {
        Route::get('/free', 'App\Http\Controllers\Api\ReservationController@free')
            ->middleware('can:view,App\\Models\\User,App\\Models\\Reservation');
    });

    //Register payments
    Route::prefix('payments')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\PaymentController@index')
            ->middleware('can:view,App\\User,App\\Payment');
        Route::post('/', 'App\Http\Controllers\Api\PaymentController@store')
            ->middleware('can:create,App\\Payment');
        Route::get('/bank_list', 'App\Http\Controllers\Api\PaymentController@bank_list')
            ->middleware('can:view,App\\User,App\\Payment');
    });

    //settings
    Route::prefix('manage_settings')->group(function () {
        Route::post('/close_restaurant', 'App\Http\Controllers\Api\SettingsController@close_restaurant')
            ->middleware('can:update,App\\User,App\\Settings')
            ->name('api.manage_settings.close_restaurant');
        Route::post('/address_delivery', 'App\Http\Controllers\Api\SettingsController@address_delivery')
            ->middleware('can:update,App\\User,App\\Settings')
            ->name('api.manage_settings.address_delivery');
    });

    Route::prefix('bill')->group(function () {
        Route::get('/current', 'App\Http\Controllers\Api\RestaurantBillsController@getCurrentOrders');
        Route::get('/new', 'App\Http\Controllers\Api\RestaurantBillsController@getNewOrders');
        Route::post('/accept-new', 'App\Http\Controllers\Api\RestaurantBillsController@acceptBill');
        Route::post('/cancel', 'App\Http\Controllers\Api\RestaurantBillsController@cancelBill');
        Route::post('/update-status', 'App\Http\Controllers\Api\RestaurantBillsController@updateStatus');
        Route::post('/update-time-wait', 'App\Http\Controllers\Api\RestaurantBillsController@updateWaitTime');
    });

    Route::get('/restaurant/notifications', 'App\Http\Controllers\Api\RestaurantBillsController@getNotifications');
    Route::post('/restaurant/notification/mark-as-read', 'App\Http\Controllers\Api\RestaurantBillsController@markNotificationAsRead');
} else {
    //Settings
    Route::resource('settings', Api\SettingsController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Restaurants
    Route::resource('restaurants', Api\RestaurantController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Ratings
    Route::resource('ratings', Api\RatingController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Bills
    Route::prefix('bills')->group(function () {
        Route::get('/', 'App\Http\Controllers\Api\BillController@index_all')->name('bills.index_all');
        Route::get('/{bill}', 'App\Http\Controllers\Api\BillController@show_all')->name('bills.show_all');
    });

    //Reservations
    Route::get('reservations', Api\ReservationController::class . '@index_all');

    //Sending points
    Route::get('send_points', Api\UserController::class . '@send_points');

    //Friends
    Route::resource('friends', Api\FriendController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //Dashboard Tiles
    Route::get('dashboard-tiles', Api\DashboardTileController::class . '@index');

    //Versions
    Route::get('/check-version', VersionController::class);

    // Password Change
    Route::put('change-password', 'App\Http\Controllers\Api\UserController@changePassword');

    Route::prefix('main-screen')->group(function () {
        Route::get('latest-restaurants', 'App\Http\Controllers\Api\MainScreenController@latest');
        Route::get('closest-restaurants', 'App\Http\Controllers\Api\MainScreenController@closest');
        Route::get('most-popular-restaurants', 'App\Http\Controllers\Api\MainScreenController@mostPopular');
    });
}

//Vouchers
Route::prefix('vouchers')->group(function () {
    Route::post('/redeem', Api\UserController::class . '@voucherRedeem');
});

/**
 * @OA\Get(
 *     path="/localization/{locale}",
 *     operationId="setLocalization",
 *     tags={"Localization"},
 *     summary="Set the application's locale",
 *     description="Set the application's locale by passing a locale parameter. The locale is stored in the session.",
 *     @OA\Parameter(
 *         name="locale",
 *         in="path",
 *         required=true,
 *         description="The locale code to set (e.g., 'en', 'pl')",
 *         @OA\Schema(type="string", example="en")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Locale set successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="locale", type="string", description="The set locale", example="en")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid locale",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", description="Error message", example="Invalid locale")
 *         )
 *     )
 * )
 */
//Localization
Route::get('localization/{locale}', function ($locale) {
    Session::put('custom_locale', $locale);

    return ['locale' => $locale];
});

/**
 * @OA\Get(
 *     path="/localization/locales/get",
 *     operationId="getAvailableLocales",
 *     tags={"Localization"},
 *     summary="Get available locales",
 *     description="Retrieve a list of all available locales supported by the application.",
 *     @OA\Response(
 *         response=200,
 *         description="List of available locales",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 description="Locale code",
 *                 example="en"
 *             )
 *         )
 *     )
 * )
 */

//Localization locales
Route::get('localization/locales/get', function () {
    return App\User::getLocales();
});

//Translations
Route::prefix('translations')->group(function () {
    Route::get('/', 'App\Http\Controllers\Api\TranslationController@index');
    Route::post('/preview', 'App\Http\Controllers\Api\TranslationController@preview')->name('api.translations.preview'); //TODO: CHECK EXITENCE OF THIS ROUTE
});

Route::post('contact', ContactFormController::class)->middleware('throttle:10,1');
Route::post('fcm_token', Api\FCMToken\CreateUserFCMToken::class);
Route::delete('fcm_token/{id}', Api\FCMToken\DeleteUserFCMToken::class);





// For authorizing private channels in websockets
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;

//Route::post('/broadcasting/auth', function (Request $request) {
//    $user = Auth::guard('api')->user(); // Uzyskanie zalogowanego użytkownika za pomocą Laravel Passport
//
//    if ($user) {
//        $socketId = $request->input('socket_id'); // Uzyskaj socket_id z żądania
//        $channelName = $request->input('channel_name'); // Uzyskaj nazwę kanału
//
//        // Dane kanału (opcjonalne)
//        $channelData = json_encode(['user_id' => $user->id]);
//
//        // Klucz i sekret aplikacji
//        $appKey = env('REVERB_APP_KEY');
//        $appSecret = env('REVERB_APP_SECRET');
//
//        // Generowanie podpisu
//        $stringToSign = $socketId . ':' . $channelName . ':' . $channelData;
//        $signature = hash_hmac('sha256', $stringToSign, $appSecret);
//
//        // Zwrócenie auth tokena
//        return response()->json([
//            'auth' => $appKey . ':' . $signature,
//            'channel_data' => $channelData
//        ]);
//    }
//
//    return response()->json(['error' => 'Unauthorized'], 403);
//});
