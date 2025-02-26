<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PhoneNotification;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelOrderAutomatically implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function handle(): void
    {
        $visibleRestaurants = Restaurant::visible()->get();

        try {
            foreach ($visibleRestaurants as $visibleRestaurant) {
                $this->reconnect($visibleRestaurant);
                $bills = PhoneNotification::select('object_id')
                    ->where([
                        ['counter', '>=', 3],
                        ['object', 'bills'],
                        ['active', 1],
                        ['updated_at', '<', Carbon::now()->subMinutes(config('serversms.cancel_order_after_minutes'))],
                        ['created_at', '>', Carbon::now()->subMinutes(config('serversms.cancel_order_not_older_than_minutes'))],
                    ])
                    ->get()
                    ->pluck('object_id');

                if (!empty($bills)) {
                    $bills = Bill::whereIn('id', $bills)
                        ->where('status', Bill::STATUS_NEW)
                        ->get();
                    foreach ($bills as $bill) {
                        $bill->status = Bill::STATUS_CANCELED;
                        $bill->save();
                        FirebaseServiceV2::saveNotification($bill->user_id, __('Canceled automatically'), '/account/orders_history/'.$bill->id, $bill->id);
                    }
                }
            }
        } catch (\Throwable $e) {
            return;
        }
    }
}
