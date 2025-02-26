<?php

namespace App\Providers;

use App\Models\Addition;
use App\Models\AdditionGroup;
use App\Models\Address;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Bill;
use App\Models\DeliveryRange;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\OnlinePaymentProviderAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PlayerId;
use App\Models\Promotion;
use App\Models\QRCode;
use App\Models\Rating;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Review;
use App\Models\Room;
use App\Models\Settings;
use App\Models\Table;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\TrWithdrawal;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Worktime;
use App\Policies;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Models\Website;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Addition::class => Policies\AdditionPolicy::class,
        Attribute::class => Policies\AttributePolicy::class,
        AdditionGroup::class => Policies\AdditionGroupPolicy::class,
        AttributeGroup::class => Policies\AttributeGroupPolicy::class,
        Address::class => Policies\AddressPolicy::class,
        Notification::class => Policies\NotificationPolicy::class,
        Dish::class => Policies\DishPolicy::class,
        Friend::class => Policies\FriendPolicy::class,
        FoodCategory::class => Policies\FoodCategoryPolicy::class,
        Order::class => Policies\OrderPolicy::class,
        Payment::class => Policies\PaymentPolicy::class,
        PlayerId::class => Policies\PlayerIdPolicy::class,
        Promotion::class => Policies\PromotionPolicy::class,
        QRCode::class => Policies\QRCodePolicy::class,
        Room::class => Policies\RoomPolicy::class,
        Settings::class => Policies\SettingsPolicy::class,
        Table::class => Policies\TablePolicy::class,
        User::class => Policies\UserPolicy::class,
        Bill::class => Policies\BillPolicy::class,
        Review::class => Policies\ReviewPolicy::class,
        Restaurant::class => Policies\RestaurantPolicy::class,
        Transaction::class => Policies\TransactionPolicy::class,
        TrWithdrawal::class => Policies\TrWithdrawPolicy::class,
        Worktime::class => Policies\WorktimePolicy::class,
        Reservation::class => Policies\ReservationPolicy::class,
        Tag::class => Policies\TagPolicy::class,
        Rating::class => Policies\RatingPolicy::class,
        Refund::class => Policies\RefundPolicy::class,
        DeliveryRange::class => Policies\DeliveryRangePolicy::class,
        RestaurantTag::class => Policies\RestaurantTagPolicy::class,
        Voucher::class => Policies\VoucherPolicy::class,
        OnlinePaymentProviderAccount::class => Policies\OnlinePaymentProviderAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        //Passport::routes();

        // If value is not set, then unlimited (default one year)
        if ($api_token_ttl = config('app.api_token_ttl')) {
            Passport::tokensExpireIn(now()->addSeconds($api_token_ttl));
        }
        Gate::define('nav-dashboard', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_WAITER]);
        });

        Gate::define('nav-dashboard-top-dash', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-user', function ($user) {
            return $user->isOne([User::ROLE_ADMIN]);
        });

        Gate::define('nav-restaurant', function ($user) {
            return $user->isOne([User::ROLE_ADMIN]);
        });

        Gate::define('nav-sales-group', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_WAITER]);
        });

        Gate::define('nav-commissions', function ($user) {
            return $user->isOne([User::ROLE_ADMIN]);
        });

        Gate::define('nav-restaurant-group', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-category', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-dish', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-label', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-addition', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-attribute', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-attribute-groups', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-promotion', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-order', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_WAITER]);
        });

        Gate::define('nav-bill', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_WAITER]);
        });

        Gate::define('nav-payment', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-refund', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-table', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-room', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-qr_code', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('translation-sys-conf', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('logs-sys-conf', function ($user) {
            return $user->hasRole(User::ROLE_ADMIN);
        });

        Gate::define('nav-settings', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-voucher', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-online_payment_provider_account', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-worktime', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

        Gate::define('nav-reservation', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_WAITER]);
        });

        Gate::define('nav-tag', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });
        Gate::define('nav-rating', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });
        Gate::define('nav-transaction', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });
        Gate::define('nav-tr_withdraw', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });
        Gate::define('nav-delivery_ranges', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });
        Gate::define('nav-reviews', function ($user) {
            return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]);
        });

    }
}
