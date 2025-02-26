<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\DTO\PointMapCoordinatesDTO;
use App\Helpers\PolygonHelper;
use App\Http\Controllers\ParametersTrait;
use App\Models\Address;
use App\Models\AddressSystem;
use App\Models\DeliveryRange;
use App\Models\Report;
use App\Models\Resource;
use App\Models\ResourceSystem;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\Settings;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\GeoServices\GeoService;
use App\Services\TranslationService;
use App\Services\UploadService;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Svg\Tag\Polygon;

class RestaurantManager
{
    use ParametersTrait, MultiTentantRepositoryTrait;

    /**
     * @var TranslationService
     */
    public $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     * @param Restaurant $restaurant
     *
     * @return Restaurant
     */
    public function update(Request $request, Restaurant $restaurant): Restaurant
    {
        $params = $this->getParams($request, ['name', 'description', 'visibility' => 0, 'provision' => 0, 'provision_logged' => 0, 'provision_unlogged' => 0, 'account_number', 'subname', 'table_reservation_active','manager_email']);
        $references = $this->getParams($request, ['photo', 'removePhotos']);
        $address = (array)$request->address;

        $tags = $request->input('tag_checkbox');
        RestaurantTag::assignNewTags($restaurant, empty($tags) ? [] : $tags);

        if (!empty($params) || !empty($address)) {
            DB::transaction(function () use ($params, $restaurant, $address, $references) {
                $restaurant->update($params);
                $restaurant->fresh();

                if ($address) {
                    $address_coord = '';
                    $address = array_diff_key($address, ['id', 'company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone', 'radius']);

                    if (!empty($address['city'])) {
                        $address_coord = $address['city'];
                    }
                    if (!empty($address['postcode'])) {
                        $address_coord = $address['postcode'] . ' ' . $address_coord;
                    }
                    if (!empty($address['street'])) {
                        $address_coord = $address['street'] . ' ' . $address_coord;
                    }
                    if ($address_coord) {
                        $geoService = app(GeoService::class);
                        $addressCoords = $geoService->getCoords($address_coord);

                        if ($addressCoords) {
                            $address['lat'] = $addressCoords->getLat();
                            $address['lng'] = $addressCoords->getLng();
                        }
                    }
                    $restaurant->address()->delete();
                    $address = $restaurant->address()->create(AddressSystem::decamelizeArray($address));
                    $restaurant->address_id = $address->id;
                    $restaurant->save();
                    $restaurant->fresh();

                    if( $this->checkIfResturantHasDeliveryRanges($restaurant) ){
                        $this->setDefaultPolygonForRestaurant($address,$restaurant);
                    }
                }
            });

        }

        return $restaurant;
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return Restaurant
     */
    public function delete(Restaurant $restaurant): Restaurant
    {
        DB::transaction(function () use ($restaurant) {
            $restaurant->delete();
        });

        return $restaurant;
    }

    /**
     * @param Request $request
     *
     * @return Restaurant
     */
    public function create(Request $request): Restaurant
    {
        $params = $this->getParams($request, ['name', 'hostname', 'visibility' => 0, 'provision' => 0, 'provision_logged' => 0, 'provision_unlogged' => 0, 'account_number', 'tag_checkbox', 'table_reservation_active','manager_email']);
        $references = $this->getParams($request, ['photo']);
        $address = (array)$request->address;

        $restaurant = DB::transaction(function () use ($params, $address, $request) {
            if (isset($params['hostname'])) {
                $website = new Website;
                $website->uuid = $params['hostname'];
                app(WebsiteRepository::class)->create($website);

                $hostname = new Hostname;
                $hostname->fqdn = $params['hostname'] . '.' . env('TENANCY_DEFAULT_HOSTNAME');
                $hostname = app(HostnameRepository::class)->create($hostname);
                app(HostnameRepository::class)->attach($hostname, $website);
                $params['hostname_id'] = $hostname->id;
            }
            $restaurant = Restaurant::create($params)->fresh();

            (new UploadService())->moveTempFiles($request, $restaurant, true);

            if ($address) {
                $address = array_diff_key($address, ['company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone', 'radius']);
                $address_coord = '';
                if (!empty($address['city'])) {
                    $address_coord = $address['city'];
                }
                if (!empty($address['postcode'])) {
                    $address_coord = $address['postcode'].' '.$address_coord;
                }
                if (!empty($address['street'])) {
                    $address_coord = $address['street'].' '.$address_coord;
                }
                if (!empty($address['building_number'])) {
                    $address_coord = $address['building_number'].' '.$address_coord;
                }
                if ($address_coord) {
                    $geoService = app(GeoService::class);
                    $addressCoords = $geoService->getCoords($address_coord);

                    if ($addressCoords) {
                        $address['lat'] = $addressCoords->getLat();
                        $address['lng'] = $addressCoords->getLng();
                    }
                }

                $address = $restaurant->address()->create(Address::decamelizeArray($address));
                $restaurant->address_id = $address->id;
                $restaurant->save();
                $restaurant->fresh();
                $this->setDefaultPolygonForRestaurant($address,$restaurant);

            }

            return $restaurant;
        });

        $this->copyNewRestaurantData($restaurant, $request);

        if (isset($params['tag_checkbox'])) {
            RestaurantTag::assignNewTags($restaurant, empty($params['tag_checkbox']) ? [] : $params['tag_checkbox']);
        }

        Report::create([
            'restaurant_id' => $restaurant->id
        ]);

        return $restaurant;
    }

    public function copyNewRestaurantData(Restaurant $restaurant, Request $request)
    {
        $images = ResourceSystem::query()
            ->where('resourcetable_type', 'restaurants')
            ->where('resourcetable_id', $restaurant->id)
            ->get();

        foreach ($images as $image) {
            $type = $image->additional['file_type'] ?? '';
            if ($type == 'logo' || $type == 'bg_image' || $type == 'dish_default_image') {
                $this->moveToRestaurant($image, $restaurant);
            }
        }
        //bg image
    }

    public function moveToRestaurant(ResourceSystem $resourceSystem, Restaurant $restaurant)
    {
        $uploadService = app(UploadService::class);
        $imagesSetting = Settings::query()->where('key', 'logo')->first();

        if (!$imagesSetting instanceof Settings) {
            return false;
        }
        $resource = new Resource($resourceSystem->toArray());
        $resource->resourcetable_type = 'settings';
        $resource->resourcetable_id = $imagesSetting->id;
        $resource->save();

        $source = storage_path('app/' . $uploadService->getRelativePath($resourceSystem->resourcetable_type, $resourceSystem->resourcetable_id));
        $destination = storage_path('app/public/tenancy/' . $restaurant->hostname . '/settings/' . $imagesSetting->id);

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }
        copy($source . '/' . $resourceSystem->filename, $destination . '/' . $resource->filename);
    }

    private function setDefaultPolygonForRestaurant(mixed $address,Restaurant $restaurant)
    {
        if ($address instanceof AddressSystem) {
            $lat = $address->lat;
            $lng = $address->lng;
        } else if (is_array($address)) {
            $lat = $address['lat'];
            $lng = $address['lng'];
        } else {
            $lat = 0;
            $lng = 0;
        }
        $maxRadius = 15;

        $polygonCoordinates = PolygonHelper::getCirclePolygonCoordinates($lat, $lng, $maxRadius);
        $polygonCoordinates[] = $polygonCoordinates[0];
        $polygonString = 'POLYGON((' . implode(',', array_map(function ($point) {
                return $point[0] . ' ' . $point[1];
            }, $polygonCoordinates)) . '))';

        DB::table('restaurants')->where('id', $restaurant->id)->update([
            'max_delivery_range' => DB::raw("ST_GeomFromText('{$polygonString}')")
        ]);
    }
    private function checkIfResturantHasDeliveryRanges(Restaurant $restaurant)
    {
        $this->reconnect($restaurant);
        $lastDelivery = DeliveryRange::latest()->first();
        if (!$lastDelivery) {
            return true;
        }
        $this->reset();
        return false;
    }

    public function revertRestaurantDeliveryPolygonToDefault(Restaurant $restaurant)
    {

        $newCords = [];
        $address=DB::table('addresses')->where('id', $restaurant->address_id)->first();

        if ($address) {
            $geoService = app(GeoService::class);
            $addressCoords = $geoService->getCoords($address->street.' '.$address->postcode.' '.$address->city);

            if ($addressCoords) {
                $newCords['lat'] = $addressCoords->getLat();
                $newCords['lng'] = $addressCoords->getLng();
            }
        }
        $this->setDefaultPolygonForRestaurant($newCords, $restaurant);
    }
}

