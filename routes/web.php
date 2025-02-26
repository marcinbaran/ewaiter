<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FirebaseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', AuthController::class.'@create')->name('login');

Route::get('/register/{id?}', UserController::class.'@register')->name('admin.register');
Route::post('/register/{id?}', UserController::class.'@registerSave')->name('admin.register_save');
Route::get('/register_auth/{user}', UserController::class.'@registerAuth')->name('admin.register_auth');
Route::post('/register_auth_save', UserController::class.'@registerAuthSave')->name('admin.register_auth_save');
Route::post('/register_auth_again', UserController::class.'@registerAuthAgain')->name('admin.register_auth_again');

Route::get('/login_admin/{token}', AuthController::class.'@login_admin')->name('admin.auth.login_admin');

Route::get('callback_tpay/{h}', PaymentController::class.'@callback_tpay')->name('payments.callback_tpay');
Route::get('callback_p24/{h}', PaymentController::class.'@callback_p24')->name('payments.callback_p24');

Route::post('status_tpay', PaymentController::class.'@status_tpay')->name('payments.status_tpay');
Route::post('status_p24', PaymentController::class.'@status_p24')->name('payments.status_p24');

Route::post('firebase/store', FirebaseController::class.'@store')->name('firebase.store');

Route::post('firebase/delete', FirebaseController::class.'@delete')->name('firebase.delete');

Route::get('ticket', OrderController::class.'@ticket');

Route::get('/user/verify/{lang}/{token}', AuthController::class.'@verifyUser');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
