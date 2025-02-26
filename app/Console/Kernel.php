<?php

namespace App\Console;

use App\Jobs\CancelOrderAutomatically;
use App\Jobs\CancelOrderAutomaticallyAfter25minutes;
use App\Jobs\CronPhoneCallNotification;
use App\Jobs\FirebaseSendPushNotifications;
use App\Jobs\SendOrderReleasedNotification;
use App\Jobs\SetMaxDeliveryCoords;
use App\Jobs\UpdateRestaurantMinimalDeliveryPrice;
use App\Jobs\RestaurantUpdateService;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\FirebaseServiceV2;
use App\Services\TransactionService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use MultiTentantRepositoryTrait;

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new CancelOrderAutomatically())->everyMinute()->name('cronCancelUnansweredBills')->withoutOverlapping();
        $schedule->job(new RestaurantUpdateService())->daily()->name('restaurantUpdateService')->withoutOverlapping();
        $schedule->job(new CronPhoneCallNotification())->everyMinute()->name('cronPhoneCallNotification')->withoutOverlapping();
        $schedule->job(new FirebaseSendPushNotifications(new FirebaseServiceV2))->everyMinute()->name('firebasePushNotificationV2')->withoutOverlapping();
        $schedule->job(new SetMaxDeliveryCoords())->everyMinute()->name('Set max delivery coords for restaurants')->withoutOverlapping();
        $schedule->job(new SendOrderReleasedNotification())->everyMinute()->withoutOverlapping();
        $schedule->job(new UpdateRestaurantMinimalDeliveryPrice())->daily()->withoutOverlapping();
        $schedule->job(new CancelOrderAutomaticallyAfter25minutes())->everyMinute()->withoutOverlapping();

        /*
         * Transactions
         */
        $schedule->call(function () {
            (new TransactionService())->cronTransferPayments();
        })->hourly()->name('cronTransferPayments')->withoutOverlapping();

        $schedule->call(function () {
            (new TransactionService())->cronWithdraw();
        })->daily()->name('cronWithdraw')->withoutOverlapping();

        $schedule->command('restaurants:update-status')->everyMinute()->withoutOverlapping();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
