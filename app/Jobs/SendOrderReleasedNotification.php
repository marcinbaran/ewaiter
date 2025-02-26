<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\FireBaseNotificationV2;
use App\Models\Restaurant;
use App\Models\Review;
use App\Repositories\MultiTentantRepositoryTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderReleasedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    private const HOURS = [2, 24, 47];

    public function handle(): void
    {
        $restaurants = Restaurant::all();
        $reviewBillIds = Review::pluck('bill_id')->toArray();

        foreach ($restaurants as $restaurant) {
            $this->reconnect($restaurant);

            foreach (self::HOURS as $hours) {
                $this->sendNotificationsForBillsReleasedAt(Carbon::now()->subHours($hours), $reviewBillIds);
            }

            $this->reset();
        }
    }

    private function sendNotificationsForBillsReleasedAt(Carbon $time, array $reviewBillIds): void
    {
        $formattedTime = $time->format('Y-m-d H:i:00');
        $bills = Bill::whereRaw('DATE_FORMAT(released_at, "%Y-%m-%d %H:%i:00") = ?', [$formattedTime])->get();

        foreach ($bills as $bill) {
            if ($time->diffInHours(Carbon::now()) === 2 || !in_array($bill->id, $reviewBillIds)) {
                $this->sendFirebasePushNotification($bill);
            }
        }
    }

    private function sendFirebasePushNotification(Bill $bill): void
    {
        $currentRestaurantHostname = Restaurant::getCurrentRestaurant()->hostname;

        FireBaseNotificationV2::create([
            'user_id' => $bill->user_id,
            'title' => __('firebase.E-waiter'),
            'body' => __('firebase.review_bill'),
            'data' => json_encode([
                'title' => __('firebase.E-waiter'),
                'body' => __('firebase.review_bill'),
                'url' => '/account/orders_history/' . $bill->id,
                'object_id' => $bill->id,
                'hostname' => $currentRestaurantHostname,
            ]),
        ]);
    }
}
