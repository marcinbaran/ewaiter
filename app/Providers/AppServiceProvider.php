<?php

namespace App\Providers;

use App\Collections\SettingsCollection;
use App\Models\DeliveryRange;
use App\Models\Settings;
use App\Observers;
use App\Repositories\Eloquent\AddressRepository;
use App\Repositories\Eloquent\BillRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Interfaces\AddressRepositoryInterface;
use App\Repositories\Interfaces\BillRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\GeolocationService;
use App\Services\GeoServices\GeoService;
use App\Services\GoogleDistanceService;
use App\Services\OneSignalService;
use App\Services\Przelewy24Service;
use App\Services\ReferringUserService;
use App\Services\TpayNotificationService;
use App\Services\TpayService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Tpay\OriginApi\Utilities\Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MigrationCreator::class, function ($app) {
            return new \App\Migrations\MigrationCreator($app['files'], null);
        });

        $this->app->bind('GeolocationService', function () {
            return new GeolocationService();
        });

        $this->app->bind(GeoService::class, function () {
            $mapProvider = app(config('geo_services.map_providers.'.config('geo_services.active_provider')));

            return new GeoService($mapProvider);
        });

        $this->app->bind(GoogleDistanceService::class, function () {
            return new GoogleDistanceService(config('google-distance.api_key'));
        });

        $this->app->bind('ReferringUserService', function () {
            return new ReferringUserService();
        });

        Relation::morphMap(config('upload.mapping'));

        /* Observers */
        \App\Models\Addition::observe(Observers\AdditionObserver::class);
        \App\Models\Address::observe(Observers\AddressObserver::class);
        \App\Models\Bill::observe(Observers\BillObserver::class);
        \App\Models\Dish::observe(Observers\DishObserver::class);
        \App\Models\FoodCategory::observe(Observers\FoodCategoryObserver::class);
        \App\Models\Notification::observe(Observers\NotificationObserver::class);
        \App\Models\Order::observe(Observers\OrderObserver::class);
        \App\Models\Payment::observe(Observers\PaymentObserver::class);
        \App\Models\PlayerId::observe(Observers\PlayerIdObserver::class);
        \App\Models\Promotion::observe(Observers\PromotionObserver::class);
        \App\Models\Refund::observe(Observers\RefundObserver::class);
        \App\Models\Resource::observe(Observers\ResourceObserver::class);
        \App\Models\ResourceSystem::observe(Observers\ResourceSystemObserver::class);
        \App\Models\Table::observe(Observers\TableObserver::class);
        \App\Models\User::observe(Observers\UserObserver::class);
        \App\Models\UserSystem::observe(Observers\UserSystemObserver::class);
        \App\Models\Restaurant::observe(Observers\RestaurantObserver::class);
        \App\Models\Reservation::observe(Observers\ReservationObserver::class);
        \App\Models\Worktime::observe(Observers\WorktimeObserver::class);
        \App\Models\Tag::observe(Observers\TagObserver::class);

        /* Repositories */
        $this->app->singleton(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->singleton(BillRepositoryInterface::class, BillRepository::class);
        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);


//        if(TenancyFacade::website()) {
//            $this->app->singleton(UserProvider::class, function () {
//                return new EloquentUserProvider(app('hash'), config('auth.providers.users.model'));
//            });
//        }

        $this->loadViewsFrom(resource_path('views').'/translation-manager', 'translation-manager');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(SettingsCollection::class, SettingsCollection::class);

        $this->app->bind(OneSignalService::class, function ($app) {
            return new OneSignalService($app['config']['services']['onesignal']['key'], $app['config']['services']['onesignal']['secret'], $app['config']['services']['onesignal']['code_iso639']);
        });

        $this->app->bind(TpayService::class, function () {
            return TpayService::getTpayServiceForCurrentContext();
        });

        $this->app->bind(TpayNotificationService::class, function () {
            return TpayNotificationService::getTpayNotificationServiceForCurrentContext();
        });

        Logger::setLogPath(storage_path('logs/tpay.log'));

        /* TODO: delete this block of code */
        $this->app->bind(Przelewy24Service::class, function ($app) {
            $PRZELEWY24_MERCHANT_ID = $app['config']['services']['przelewy24']['merchant_id'];
            $PRZELEWY24_POS_ID = $app['config']['services']['przelewy24']['pos_id'];
            $PRZELEWY24_CRC = $app['config']['services']['przelewy24']['crc'];
            $PRZELEWY24_API_KEY = $app['config']['services']['przelewy24']['api_key'];

            $PRZELEWY24_MERCHANT_ID_S = Settings::getSetting('przelewy24', 'PRZELEWY24_MERCHANT_ID', true, false);
            if ($PRZELEWY24_MERCHANT_ID_S) {
                $PRZELEWY24_MERCHANT_ID = $PRZELEWY24_MERCHANT_ID_S;
            }
            $PRZELEWY24_POS_ID_S = Settings::getSetting('przelewy24', 'PRZELEWY24_POS_ID', true, false);
            if ($PRZELEWY24_POS_ID_S) {
                $PRZELEWY24_POS_ID = $PRZELEWY24_POS_ID_S;
            }
            $PRZELEWY24_CRC_S = Settings::getSetting('przelewy24', 'PRZELEWY24_CRC', true, false);
            if ($PRZELEWY24_CRC_S) {
                $PRZELEWY24_CRC = $PRZELEWY24_CRC_S;
            }
            $PRZELEWY24_API_KEY_S = Settings::getSetting('przelewy24', 'PRZELEWY24_API_KEY', true, false);
            if ($PRZELEWY24_API_KEY_S) {
                $PRZELEWY24_API_KEY = $PRZELEWY24_API_KEY_S;
            }

            return new Przelewy24Service(
                $PRZELEWY24_MERCHANT_ID,
                $PRZELEWY24_POS_ID,
                $PRZELEWY24_CRC,
                $app['config']['services']['przelewy24']['test'],
                $app['config']['services']['przelewy24']['version'],
                $PRZELEWY24_API_KEY
            );
        });
        Blade::component('package-alert', Alert::class);
        Paginator::useBootstrap();

        DeliveryRange::observe(Observers\DeliveryRangeObserver::class);
    }
}
