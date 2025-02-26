<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\AddressSystem;
use App\Models\User;
use App\Models\UserAddressSystem;
use App\Services\GeoServices\GeoService;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressManager
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
     * @return AddressSystem
     */
    public function create(Request $request): AddressSystem
    {
        $params = $this->getParams($request, ['company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'is_default', 'lat', 'lng']);

        if (!isset($params['lat']) || !isset($params['lng'])) {
            $coordinates = $this->generateCoordinates($params);
            $params['lat'] = $coordinates[0];
            $params['lng'] = $coordinates[1];
        }

        $user = User::find(auth()->user()->id);

        $params['phone'] = $user->phone;

        if (!empty($params['is_default'])) {
            $user_id = auth()->user()->id;
            DB::select('call ResetDefaultAddress(?)', [$user_id]);
        }

        $address = DB::transaction(function () use ($params) {
            $address = AddressSystem::create($params)->fresh();
            UserAddressSystem::updateOrCreate(['address_id' => $address->id, 'user_id' => auth()->user()->id]);

            return $address;
        });

        return $address;
    }

    /**
     * @param Request $request
     * @param AddressSystem $address
     *
     * @return AddressSystem
     */
    public function update(Request $request, AddressSystem $address): AddressSystem
    {
        $params = $this->getParams($request, ['company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'is_default', 'lat', 'lng']);

        if (!isset($params['lat']) || !isset($params['lng'])) {
            $coordinates = $this->generateCoordinates($params);
            $params['lat'] = $coordinates[0];
            $params['lng'] = $coordinates[1];
        }

        $user = User::find(auth()->user()->id);

        $params['phone'] = $user->phone;

        if (!empty($params['is_default'])) {
            $user_id = auth()->user()->id;
            DB::select('call ResetDefaultAddress(?)', [$user_id]);
        }

        if (!empty($params)) {
            DB::transaction(function () use ($params, $address) {
                $address->update($params);
                $address->fresh();
            });
        }

        return $address;
    }

    /**
     * @param AddressSystem $address
     *
     * @return AddressSystem
     */
    public function delete(AddressSystem $address): AddressSystem
    {
        DB::transaction(function () use ($address) {
            $address->delete();
        });

        return $address;
    }

    private function generateCoordinates(array $params)
    {
        $lat = null;
        $lng = null;
        $address = '';

        if (!empty($params['city'])) {
            $address = $params['city'];
        }

        if (!empty($params['postcode'])) {
            $address = $params['postcode'] . ' ' . $address;
        }

        if (!empty($params['street'])) {
            $address = $params['street'] . ' ' . $address;
        }

        if ($address) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address);

            if ($addressCoords) {
                $lat = $addressCoords->getLat();
                $lng = $addressCoords->getLng();
            } else {
                $lat = 0.1;
                $lng = 0.1;
            }
        }

        if (!empty($params['lat'])) {
            $lat = $params['lat'];
        }

        if (!empty($params['lng'])) {
            $lng = $params['lng'];
        }

        return [$lat, $lng];
    }
}
