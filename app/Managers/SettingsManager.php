<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Settings;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class SettingsManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return Settings
     */
    public function create(Request $request): Settings
    {
        $params = $this->getParams($request, [
            'key',
            'value' => '',
            'value_type' => '',
            'value_active' => '',
            'description',
        ]);

        if (isset($params['value'], $params['value']['logo'])) {
            if (isset($params['value']) && is_a($params['value'], 'Illuminate\Http\UploadedFile')) {
                $file = $params['value'];
                $filename = $file->getClientOriginalName();
                $file->storeAs('settings', $filename);

                $file->move(public_path('settings'), $filename);

                $params['value'] = URL::asset('settings/'.$filename);
            }
        }

        $settings = DB::connection('tenant')->transaction(function () use ($params) {
            $settings = Settings::create(Settings::decamelizeArray($params))->fresh();

            return $settings;
        });

        return $settings;
    }

    /**
     * @param Request $request
     * @param Settings $settings
     *
     * @return Settings
     */
    public function update(Request $request, Settings $settings): Settings
    {
        $params = $this->getParams($request, [
            'key',
            'value' => '',
            'value_active' => '',
            'description',
        ]);

        if ($settings->key === 'service_charge') {
            $isServiceChargeMoneyActive = (int) ($request->value_active && $request->typeValue === 'money');
            $isServiceChargePercentageActive = (int) ($request->value_active && $request->typeValue === 'procent');

            $params['value'] = ['service_charge' => (string) $request->money_value, 'service_charge_procent' => (string) $request->percentage_value];
            $params['value_active'] = ['service_charge' => (string) $isServiceChargeMoneyActive, 'service_charge_procent' => (string) $isServiceChargePercentageActive];
        }
        if ($settings->key === 'konto_tpay') {  //TODO: refactor
            $params['value'] = [
                'comment'=>(string) $request->comment,
                'login' =>  Crypt::encryptString($request->login),
                'password' =>  Crypt::encryptString($request->password),
                'api_key' =>  Crypt::encryptString($request->api_key),
                'api_password' => Crypt::encryptString($request->api_password),
                ];
        }

        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $settings) {
                $settings->update(Settings::decamelizeArray($params));
                $settings->fresh();
            });
        }

        return $settings;
    }

    /**
     * @param Settings $settings
     *
     * @return Settings
     */
    public function delete(Settings $settings): Settings
    {
        DB::connection('tenant')->transaction(function () use ($settings) {
            $settings->delete();
        });

        return $settings;
    }
}
