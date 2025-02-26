<?php

namespace App\Models;

use App\Exceptions\ApiExceptions\Setting\SettingNotFoundException;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Settings extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    public const string WORK_TIME_SETTING_KEY = 'czas_pracy';
    public const string TIME_BEFORE_SUSPENDING_ADDRESS_DELIVERY_SETTING_KEY = 'czas_wylacz_dowoz';
    public const string TIME_BEFORE_SUSPENDING_ADDRESS_DELIVERY_VALUE_KEY = 'czas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'value_type',
        'value_active',
        'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
        'value_type' => 'array',
        'value_active' => 'array',
    ];

    /**
     * @param array $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        return self::getQueryRows($criteria, $order, $limit, $offset)->get();
    }

    public static function getQueryRows(array $criteria = [], array $order = [], int $limit = 20, int $offset = 0): Builder
    {
        $query = self::select('settings.*');

        if (!empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (!empty($criteria['key'])) {
            $query->whereIn('key', $criteria['key']);
        }

        if (!empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query;
    }

    public static function getRowsLang($locale = 'pl')
    {
        $generalSettings = self::select('id', 'key', 'value', 'created_at', 'updated_at', 'value_active')
            ->whereIn('key', ['restauracja', 'czas_pracy', 'czas_wylacz_dowoz', 'rodzaje_dostawy', 'sposoby_platnosci', 'konfiguracja_dostawy', 'kontakt', 'service_charge', 'koszt_opakowania'])
            ->get()
            ->map(function ($item) {
                if (empty($item->value_active)) {
                    $value_active = [];
                    foreach ($item->value as $k => $v) {
                        $value_active[$k] = '0';
                    }
                    $item->value_active = $value_active;
                }

                return $item;
            });

        $newDeliveryConfiguration = [];
        $deliveryRanges = \DB::connection('tenant')->table('delivery_ranges')->select()->first();

        $newDeliveryConfiguration['koszt_dostawy'] = (string)($deliveryRanges->cost ?? '0');
        $newDeliveryConfiguration['zasieg_dostawy'] = (string)($deliveryRanges->range_to ?? '0');
        $newDeliveryConfiguration['minimana_wartosc'] = (string)($deliveryRanges->min_value ?? '0');
        $newDeliveryConfiguration['darmowa_dostawa_od'] = (string)($deliveryRanges->free_from ?? '0');
        $newDeliveryConfiguration['darmowa_dostawa_do_km'] = (string)($deliveryRanges->free_from ?? '0');
        $newDeliveryConfiguration['dodatkowe_km_oplata'] = (string)($deliveryRanges->km_cost ?? '0');
        $newDeliveryConfiguration['dostawa_poza_zasieg'] = (string)($deliveryRanges->out_of_range ?? '0');

        $generalSettings->where('key', 'konfiguracja_dostawy')->first()['value'] = $newDeliveryConfiguration;

        return $generalSettings;
    }

    /**
     * @param string|null $filter
     * @param int $paginateSize
     * @param array $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null): LengthAwarePaginator
    {
        $query = self::orderBy('id', 'asc');

        if (!empty($filter)) {
            $query->where('key', 'LIKE', '%' . $filter . '%');
            $query->orWhere('description', 'LIKE', '%' . $filter . '%');
        }

        return $query->paginate($paginateSize);
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $check_active
     * @param bool $lang
     */
    public static function getSetting(string $key, string $value, bool $check_active = false, bool $lang = false, bool $check_bool = false)
    {
        $settings = Settings::where('key', $key)->first();
        if (!$settings) {
            return null;
        }

        if ($check_bool && isset($settings->value_active[$value])) {
            return (bool)$settings->value_active[$value];
        } elseif ($check_bool) {
            return null;
        }

        if (!isset($settings->value[$value]) || !isset($settings->value_active[$value])) {
            return null;
        }

        if ($check_active) {
            $active = $settings->value_active[$value] && $settings->value[$value];
        } else {
            $active = true;
        }

        if (isset($settings->value_active[$value]) && $active) {
            return $settings->value[$value];
        }
        return null;
    }

    /**
     * @throws SettingNotFoundException
     */
    public static function getDeliveryMethodsActivity(): array // TODO: make sure its good idea to have this method instead of using getSetting()
    {
        $deliveryMethods = self::where('key', 'rodzaje_dostawy')->first();

        if ($deliveryMethods === null) {
            throw new SettingNotFoundException(['missing_setting' => 'rodzaje_dostawy']);
        }

        return $deliveryMethods->value_active;
    }

    /**
     * @throws SettingNotFoundException
     */
    public static function getPaymentTypesActivity(): array // TODO: make sure its good idea to have this method instead of using getSetting()
    {
        $paymentTypes = self::where('key', 'sposoby_platnosci')->first();

        if ($paymentTypes === null) {
            throw new SettingNotFoundException(['missing_setting' => 'sposoby_platnosci']);
        }

        return $paymentTypes->value_active;
    }

    public function getJsonFile(string $key)
    {
        $array = [];
        if ($photo = $this->getPhotoByType($key)) {
            $array[] = [
                'source' => $photo->id,
                'options' => [
                    'type' => 'local',
                ],
            ];
        }

        return json_encode($array);
    }

    public function getPhotoByType(string $type)
    {
        foreach ($this->photos()->get() as $photo) {
            $fileType = $photo->additional['file_type'] ?? null;
            if ($fileType == $type) {
                return $photo;
            }
        }

        return null;
    }

    /**
     * Get all of the resources.
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourcetable');
    }

}
