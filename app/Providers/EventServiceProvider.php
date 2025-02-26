<?php

namespace App\Providers;

use App\Events\BillStatusEvent;
use App\Events\BillStatusMobileEvent;
use App\Listeners\BillStatusListener;
use App\Listeners\BillStatusMobileListener;
use App\Listeners\TenantConfigurationModifications;
use App\Models\Addition;
use App\Models\Address;
use App\Models\Bill;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PlayerId;
use App\Models\Promotion;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\ResourceSystem;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserSystem;
use App\Models\Worktime;
use App\Observers\AdditionObserver;
use App\Observers\AddressObserver;
use App\Observers\BillObserver;
use App\Observers\DishObserver;
use App\Observers\FoodCategoryObserver;
use App\Observers\NotificationObserver;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\PlayerIdObserver;
use App\Observers\PromotionObserver;
use App\Observers\RefundObserver;
use App\Observers\ReservationObserver;
use App\Observers\ResourceObserver;
use App\Observers\ResourceSystemObserver;
use App\Observers\RestaurantObserver;
use App\Observers\TableObserver;
use App\Observers\TagObserver;
use App\Observers\UserObserver;
use App\Observers\UserSystemObserver;
use App\Observers\WorktimeObserver;
use Hyn\Tenancy\Events\Database\ConfigurationLoaded;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ConfigurationLoaded::class => [
            TenantConfigurationModifications::class,
        ],
        BillStatusMobileEvent::class => [
            BillStatusMobileListener::class,
        ],
        BillStatusEvent::class => [
            BillStatusListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Addition::observe(AdditionObserver::class);
        Address::observe(AddressObserver::class);
        Bill::observe(BillObserver::class);
        Dish::observe(DishObserver::class);
        FoodCategory::observe(FoodCategoryObserver::class);
        Notification::observe(NotificationObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        PlayerId::observe(PlayerIdObserver::class);
        Promotion::observe(PromotionObserver::class);
        Refund::observe(RefundObserver::class);
        Resource::observe(ResourceObserver::class);
        ResourceSystem::observe(ResourceSystemObserver::class);
        Table::observe(TableObserver::class);
        User::observe(UserObserver::class);
        UserSystem::observe(UserSystemObserver::class);
        Restaurant::observe(RestaurantObserver::class);
        Reservation::observe(ReservationObserver::class);
        Worktime::observe(WorktimeObserver::class);
        Tag::observe(TagObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
