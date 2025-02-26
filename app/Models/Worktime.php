<?php

namespace App\Models;

use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
/**
 * @OA\Schema(
 *     schema="WorktimeModel",
 *     type="object",
 *     title="Worktime",
 *     description="Model representing worktime periods for a restaurant",
 *     required={"type", "date"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the worktime entry"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="integer",
 *         description="Type of worktime (0 for closed, 1 for opened)"
 *     ),
 *     @OA\Property(
 *         property="start",
 *         type="string",
 *         format="date-time",
 *         description="Start time of the worktime period"
 *     ),
 *     @OA\Property(
 *         property="end",
 *         type="string",
 *         format="date-time",
 *         description="End time of the worktime period"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         description="Date for which the worktime period is applicable"
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="boolean",
 *         description="Visibility status of the worktime period"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp of the worktime entry"
 *     ),
 *     @OA\Property(
 *         property="typeName",
 *         type="string",
 *         description="The name of the type (e.g., 'Forced close', 'Forced open')"
 *     )
 * )
 */
class Worktime extends Model
{
    use ModelTrait,
        Notifiable;
    use UsesTenantConnection;

    /**
     * @var int
     */
    public const TYPE_CLOSED = 0;

    /**
     * @var int
     */
    public const TYPE_OPENED = 1;

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'start',
        'end',
        'date',
        'visibility',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'type' => self::TYPE_CLOSED,
    ];

    /**
     * @var array
     */
    protected static $typeName = [
        self::TYPE_CLOSED => 'Forced close',
        self::TYPE_OPENED => 'Forced open',
    ];

    /**
     * @var array
     */
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
     * @return string
     */
    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }

    /**
     * @param array $date
     *
     * @return array
     */
    public static function getRows(array $date = []): \Illuminate\Support\Collection
    {
        $worktime_array = [];

        $czas_wylacz_dowoz = Settings::getSetting('czas_wylacz_dowoz', 'czas', true, false);

        if (! count($date)) {
            $date[] = Carbon::now()->format('Y-m-d');
        }
        foreach ($date as $d) {
            $worktime_settings = self::getSettingsWorktime($d);

            $worktime_array[] = $worktime_settings;
        }
        if (count($worktime_array) == 1 && empty($worktime_array[0])) {
            $worktime_array = null;
        }

        return collect(['data' => ['czas_wylacz_dowoz' => $czas_wylacz_dowoz, 'days' => $worktime_array]]);
    }

    public static function getSettingsWorktime($d)
    {
        $forced = self::where('date', $d)->where('visibility', 1)->first();
        if ($forced && $forced->type == self::TYPE_CLOSED) {
            return [];
        } elseif ($forced && $forced->type == self::TYPE_OPENED) {
            $d = Carbon::parse($forced->date)->format('Y-m-d');
            $compare = Carbon::parse($forced->start)->lessThan(Carbon::parse($forced->end));
            if ($compare) {
                $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d.' '.$forced->start))->format('Y-m-d H:i:s');
                $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d.' '.$forced->end))->format('Y-m-d H:i:s');
            } else {
                $d_tomorrow = Carbon::parse($forced->date)->addDay()->format('Y-m-d');
                $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d.' '.$forced->start))->format('Y-m-d H:i:s');
                $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d_tomorrow.' '.$forced->end))->format('Y-m-d H:i:s');
            }

            return ['start' => $worktime_settings_start, 'end' => $worktime_settings_end];
        }

        $day_number = Carbon::parse($d)->dayOfWeek;
        $day_name = self::$dayOfWeek[$day_number];
        $settings = Settings::where('key', 'czas_pracy')->first();
        if (! $settings) {
            return ['Missing worktime settings!'];
        }
        if (isset($settings->value['pl'][$day_name]) && $settings->value['pl'][$day_name]) {
            $worktime_settings = $settings->value['pl'][$day_name];
        }
        if (isset($settings->value_active['pl'][$day_name]) && ! $settings->value_active['pl'][$day_name]) {
            return null;
        }
        if (! isset($worktime_settings)) {
            return ['Missing worktime time settings!'];
        }
        $worktime_settings = explode('-', $worktime_settings);
        $worktime_settings_start = $worktime_settings[0];
        $worktime_settings_end = $worktime_settings[1];
        $compare = Carbon::parse($worktime_settings_start)->lessThan(Carbon::parse($worktime_settings_end));
        if ($compare) {
            $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d.' '.$worktime_settings_start.':00'))->format('Y-m-d H:i:s');
            $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d.' '.$worktime_settings_end.':00'))->format('Y-m-d H:i:s');
        } else {
            $d_tomorrow = Carbon::parse($d)->addDay()->format('Y-m-d');
            $worktime_settings_start = Carbon::createFromTimestamp(strtotime($d.' '.$worktime_settings_start.':00'))->format('Y-m-d H:i:s');
            $worktime_settings_end = Carbon::createFromTimestamp(strtotime($d_tomorrow.' '.$worktime_settings_end.':00'))->format('Y-m-d H:i:s');
        }
        $worktime_settings_final = [
            'start' => $worktime_settings_start,
            'end' => $worktime_settings_end,
        ];

        return $worktime_settings_final;
    }

    public function getTypeName()
    {
        throw_if(! isset(self::$typeName[$this->type]), new \Exception('Not found this type!'));

        return self::$typeName[$this->type];
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param bool        $onlyGroup
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null): LengthAwarePaginator
    {
        $query = self::select('worktimes.*');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('start', 'LIKE', '%'.$filter.'%')
                    ->orWhere('end', 'LIKE', '%'.$filter.'%');
            });
        }

        return $query->paginate($paginateSize);
    }

    public function scopeTodayAndYesterdayActiveWorkTimes(Builder $builder): void
    {
        $builder->where(function ($query) {
            $query->whereDate('date', Carbon::now()->toDateString())
                ->orWhereDate('date', Carbon::yesterday()->toDateString());
        })->where('visibility', 1);
    }

    public function scopeTodayActiveWorkTimes(Builder $builder): void
    {
        $builder->where(['visibility' => 1, 'date'=> Carbon::now()->toDateString()]);
    }
}
