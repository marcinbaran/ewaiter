<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelOrderAutomaticallyAfter25minutes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    const int TIME_TO_CANCEL_ORDER_IN_MINUTES = 25;

    public function handle(): void
    {
        $visibleRestaurants = Restaurant::visible()->get();

        try {
            foreach ($visibleRestaurants as $visibleRestaurant) {
                $this->reconnect($visibleRestaurant);
                $bills = Bill::where('status', Bill::STATUS_NEW)
                    ->where('created_at', '<=', Carbon::now()->subMinutes(self::TIME_TO_CANCEL_ORDER_IN_MINUTES));

                foreach ($bills->get() as $bill) {
                    $bill->status = Bill::STATUS_CANCELED;
                    $bill->save();
                    FirebaseServiceV2::saveNotification(
                        $bill->user_id,
                        __('Canceled automatically'),
                        '/account/orders_history/' . $bill->id,
                        $bill->id
                    );
                }
            }
        } catch (\Throwable $e) {
            return;
        }
    }
}
