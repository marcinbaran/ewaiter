<?php

namespace App\Managers;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\ParametersTrait;
use App\Models\Settings;
use App\Models\Worktime;
use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorktimeManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    private $todayWorkTimeSetting;

    private $worktimes;

    private $now;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->now = Carbon::now();
        $this->todayWorkTimeSetting = $this->getTodayWorkTimeSettings();
        $this->getTodayWorktimes();
    }

    /**
     * @param Request $request
     *
     * @return Worktime
     */
    public function create(Request $request): Worktime
    {
        $params = $this->getParams($request, ['start', 'end', 'visibility' => 0, 'type', 'date']);
        $worktime = DB::connection('tenant')->transaction(function () use ($params) {
            if (empty($params['start'])) {
                $params['start'] = null;
            }
            if (empty($params['end'])) {
                $params['end'] = null;
            }
            $worktime = Worktime::create(Worktime::decamelizeArray($params))->fresh();

            return $worktime;
        });

        return $worktime;
    }

    /**
     * @param Request $request
     * @param Worktime    $worktime
     *
     * @return Worktime
     */
    public function update(Request $request, Worktime $worktime): Worktime
    {
        $params = $this->getParams($request, ['start', 'end', 'visibility' => 0, 'type', 'date']);
        DB::connection('tenant')->transaction(function () use ($params, $worktime) {
            if (empty($params['start'])) {
                $params['start'] = null;
            }
            if (empty($params['end'])) {
                $params['end'] = null;
            }
            if (! empty($params)) {
                $worktime->update($params);
            }
        });

        return $worktime;
    }

    /**
     * @param Worktime $worktime
     *
     * @return Worktime
     */
    public function delete(Worktime $worktime): Worktime
    {
        DB::connection('tenant')->transaction(function () use ($worktime) {
            $worktime->delete();
        });

        return $worktime;
    }

    public function getTodayWorkTime(): ?array
    {
        // no settings no worktimes
        if (! $this->hasTodayWorkTimeSettings() && ! $this->hasWorktimes()) {
            return null;
        }

        // settings
        if ($this->hasTodayWorkTimeSettings()) {
            // settings and no worktimes
            if (! $this->hasWorktimes()) {
                return $this->todayWorkTimeSetting;
            }

            if ($this->worktimes->type == Worktime::TYPE_OPENED) {
                return [
                    'start' => $this->worktimes->start,
                    'end' => $this->worktimes->end,
                ];
            } else {
                return $this->ifWorktimeTypeClosed();
            }

            //  no settings
        } else {
            // no settings and worktimes
            if ($this->worktimes && $this->worktimes->type == Worktime::TYPE_OPENED) {
                return [
                    'start' => $this->worktimes->start,
                    'end' => $this->worktimes->end,
                ];
                // no settingsow and no worktimes (no force open wroktimes)
            } else {
                return null;
            }
        }
    }

    private function isClosedAtTheMiddleOfTheDay()
    {
        return $this->todayWorkTimeSetting['start'] <= $this->worktimes->start && $this->todayWorkTimeSetting['end'] >= $this->worktimes->end;
    }

    private function hasTodayWorkTimeSettings() : bool
    {
        return $this->todayWorkTimeSetting !== null;
    }

    private function hasWorktimes() : bool
    {
        return $this->worktimes !== null;
    }

    private function closedAtThemiddleOftheDayHours()
    {
        // if now is in the first part of the day
        if ($this->now <= $this->worktimes->start && $this->now <= $this->worktimes->end) {
            $todayModifiedWorktime['start'] = $this->todayWorkTimeSetting['start'];
            $todayModifiedWorktime['end'] = $this->worktimes->start;

            return $todayModifiedWorktime;
        }

        // if now is in the second part of the day
        if ($this->now <= $this->todayWorkTimeSetting['end'] && $this->now >= $this->worktimes->end) {
            $todayModifiedWorktime['start'] = $this->worktimes->start;
            $todayModifiedWorktime['end'] = $this->todayWorkTimeSetting['end'];

            return $todayModifiedWorktime;
        }

        return $this->todayWorkTimeSetting;
    }

    private function ifWorktimeTypeClosed()
    {
        $todayModifiedWorktime = $this->todayWorkTimeSetting;

        if ($this->outOfOpeningHours()) {
            return $todayModifiedWorktime;
        }

        // if closed at the begining of the day
        if ($this->isClosedAtTheBeginingOfTheDay()) {
            $todayModifiedWorktime['start'] = $this->worktimes->end;
            $todayModifiedWorktime['end'] = $this->todayWorkTimeSetting['end'];

            return $todayModifiedWorktime;
        }

        // if closed at the end of the day
        if ($this->isClosedAtTheEndOfTheDay()) {
            $todayModifiedWorktime['start'] = $this->todayWorkTimeSetting['start'];
            $todayModifiedWorktime['end'] = $this->worktimes->start;

            return $todayModifiedWorktime;
        }

        // if closed at the middle of the day
        if ($this->isClosedAtTheMiddleOfTheDay()) {
            return $this->closedAtThemiddleOftheDayHours();
        }

        return $todayModifiedWorktime;
    }

    private function isClosedAtTheBeginingOfTheDay()
    {
        return $this->todayWorkTimeSetting['start'] >= $this->worktimes->start && $this->todayWorkTimeSetting['end'] >= $this->worktimes->end;
    }

    private function outOfOpeningHours()
    {
        $after = $this->todayWorkTimeSetting['start'] <= $this->worktimes->start && $this->todayWorkTimeSetting['end'] <= $this->worktimes->end;
        $before = $this->todayWorkTimeSetting['start'] >= $this->worktimes->start && $this->todayWorkTimeSetting['end'] >= $this->worktimes->end;

        return $after || $before;
    }

    private function isClosedAtTheEndOfTheDay()
    {
        return $this->todayWorkTimeSetting['start'] <= $this->worktimes->start && $this->todayWorkTimeSetting['end'] <= $this->worktimes->end;
    }

    private function getTodayWorkTimeSettings()
    {
        $currentDayNameInPolish = $this->now->locale('pl')->dayName;
        $currentDayNameInPolishWithoutDiacritics = Str::transliterate($currentDayNameInPolish);

        $settings = Settings::getSetting(Settings::WORK_TIME_SETTING_KEY, $currentDayNameInPolishWithoutDiacritics, true);

        if ($settings) {
            $todaySettingsWorktimes = [
                'start' => Str::before($settings, '-'),
                'end' => Str::after($settings, '-'),
            ];

            return DateTimeHelper::getDateTimeRangeFromTimeStrings($todaySettingsWorktimes['start'], $todaySettingsWorktimes['end'], Carbon::now()->toDateString());
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getTodayWorktimes()
    {
        $worktimes = Worktime::todayActiveWorkTimes()->first();

        if ($worktimes) {
            $this->worktimes = $worktimes;
            $this->worktimes->start = Carbon::parse($worktimes->start);
            $this->worktimes->end = Carbon::parse($worktimes->end);
        }

        return $this->worktimes;
    }
}
