<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\SerwerSMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronPhoneCallNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function handle()
    {
        $visibleRestaurants = Restaurant::visible()->get();

        foreach ($visibleRestaurants as $visibleRestaurant) {
            $this->reconnect($visibleRestaurant);
            (new SerwerSMSService())->cronCall();
        }
    }
}
