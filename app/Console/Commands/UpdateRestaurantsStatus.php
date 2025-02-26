<?php

namespace App\Console\Commands;

use App\Events\Restaurants\RestaurantsUpdated;
use App\Helpers\DateTimeHelper;
use App\Models\Restaurant;
use App\Models\Worktime;
use App\Repositories\MultiTentantRepositoryTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateRestaurantsStatus extends Command
{
    use MultiTentantRepositoryTrait;

    protected static $dayOfWeek = [
        1 => 'poniedzialek',
        2 => 'wtorek',
        3 => 'sroda',
        4 => 'czwartek',
        5 => 'piatek',
        6 => 'sobota',
        0 => 'niedziela',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update restaurant is_opened status';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $this->cronUpdateRestaurantStatus();
    }

    public function cronUpdateRestaurantStatus()
    {
        try {
            $restaurants = Restaurant::all();
            $updatedRestaurants = collect();

            foreach ($restaurants as $restaurant) {
                if($restaurant->visibility)
                {
                    $currentStatus = $restaurant->is_opened;
                    $isOpened = $this->isOpened($restaurant);
                    $restaurant->update(['is_opened' => $isOpened]);
                    if ($currentStatus != $restaurant->is_opened) {
                        $updatedRestaurants->push([
                            'restaurant_id' => $restaurant->id,
                            'is_opened' => $isOpened
                        ]);
                    }
                }
            }
            try {
                broadcast(new RestaurantsUpdated($updatedRestaurants));
            } catch (Throwable $e) {
                Log::error('Error broadcasting restaurants updated: ' . $e->getMessage());
            }
        } catch (Throwable $e) {
            Log::error('Error updating restaurants: ' . $e->getMessage());
        }
    }


    public function isOpened(Restaurant $restaurant)
    {
        try {
            $this->reconnect($restaurant);
            $forcedWorkTimes = Worktime::todayAndYesterdayActiveWorkTimes()->get();
            foreach ($forcedWorkTimes as $forcedWorkTime) {
                $forcedWorkTimeDateTime = DateTimeHelper::getDateTimeRangeFromTimeStrings($forcedWorkTime->start, $forcedWorkTime->end, $forcedWorkTime->date);
                $forcedWorkTimeIsNow = Carbon::now()->between($forcedWorkTimeDateTime['start'], $forcedWorkTimeDateTime['end']->addMinute());

                if ($forcedWorkTime->type == Worktime::TYPE_CLOSED && $forcedWorkTimeIsNow) {
                    return false;
                }

                if ($forcedWorkTime->type == Worktime::TYPE_OPENED && $forcedWorkTimeIsNow) {
                    return true;
                }
            }
            $day_name = self::$dayOfWeek[Carbon::now()->dayOfWeek];
            $settings = DB::connection('tenant')->table('settings')->where('key', 'czas_pracy')->first();
            if (!$settings) {
                return false;
            }

            $settings->value = json_decode($settings->value, true);
            $settings->value_active = json_decode($settings->value_active, true);

            if (isset($settings->value_active[$day_name]) && !$settings->value_active[$day_name]) {
                return false;
            }

            $worktime_settings = $settings->value[$day_name] ?? null;
            if (!$worktime_settings) {
                return false;
            }

            list($worktime_start, $worktime_end) = explode('-', $worktime_settings);
            $current_time = Carbon::now();
            $start_time = Carbon::parse("{$current_time->toDateString()} $worktime_start");
            $end_time = Carbon::parse("{$current_time->toDateString()} $worktime_end");

            if ($start_time->greaterThan($end_time)) {
                $end_time->addDay();
            }

            return $current_time->between($start_time, $end_time);

        } catch (Throwable $e) {
            throw new \Exception("Error checking if restaurant is opened: " . $e->getMessage());
        }
    }

}
