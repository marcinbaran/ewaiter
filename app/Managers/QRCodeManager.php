<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\QRCode;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\Table;
use App\Services\TranslationService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use QRCodeLibrary;

class QRCodeManager
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
     * @return QRCode
     */
    public function create(Request $request): QRCode
    {
        $website = \Hyn\Tenancy\Facades\TenancyFacade::website();
        $restaurant = Restaurant::where('hostname', $website->uuid)->first();
        $params = $this->getParams($request, ['object_type', 'object_id_table', 'object_id_room', 'redirect']);
        $json = [];

        if ($params['object_type'] == 'table') {
            $params['object_id'] = $params['object_id_table'];
            $object = Table::where('id', $params['object_id'])->first();
            $json['type'] = 'table';
            $json['number'] = $object->number;
        } elseif ($params['object_type'] == 'room') {
            $params['object_id'] = $params['object_id_room'];
            $object = Room::where('id', $params['object_id'])->first();
            $json['type'] = 'room';
            $json['number'] = $object->number;
        } else {
            $params['object_id'] = $restaurant->id;
            $object = null;
            $json['type'] = 'restaurant';
            $json['number'] = null;
        }

        $params['json'] = json_encode($json);
        $params['path_qrcode'] = '';

        $params['url'] = env('PANEL_URL').'/?type='.$params['object_type'];
        if ($object !== null) {
            $params['url'] .= '&number='.$object->number;
        }
        $params['url'] .= '&res_id='.$restaurant->id.'&restaurant_id='.$restaurant->id.'&hostname='.$restaurant->hostname;

        $qr_code = DB::connection('tenant')->transaction(function () use ($params, $website) {
            $qr_code = QRCode::create($params)->fresh();
            $qr_code->path_qrcode = 'images/qrcodes/'.$qr_code->id.'.svg';
            $qr_code->save();

            $uploadService = new UploadService();

            $qr_code->path_qrcode = $uploadService->getPublicFolder('qrcodes', $qr_code->id, true).'/'.$qr_code->id.'.svg';
            $qr_code->save();

            QRCodeLibrary::size(500)->generate($params['url'], $uploadService->getPathToSave('qrcodes', $qr_code->id, $qr_code->id.'.svg'));
            chmod($uploadService->getPublicFolder('qrcodes', $qr_code->id, true), 0755);
            chmod($uploadService->getPathToSave('qrcodes', $qr_code->id, $qr_code->id.'.svg'), 0755);

            return $qr_code;
        });

        return $qr_code;
    }

    /**
     * @param Request $request
     * @param QRCode   $qr_code
     * @param bool    $isAdmin
     *
     * @return QRCode
     */
    public function update(Request $request, QRCode $qr_code, bool $isAdmin = false): QRCode
    {
        $website = \Hyn\Tenancy\Facades\TenancyFacade::website();
        $restaurant = Restaurant::where('hostname', $website->uuid)->first();
        $params = $this->getParams($request, ['object_type', 'object_id_table', 'object_id_room', 'redirect']);
        $json = [];

        if ($params['object_type'] == 'table') {
            $params['object_id'] = $params['object_id_table'];
            $object = Table::where('id', $params['object_id'])->first();
            $json['type'] = 'table';
            $json['number'] = $object->number;
        } elseif ($params['object_type'] == 'room') {
            $params['object_id'] = $params['object_id_room'];
            $object = Room::where('id', $params['object_id'])->first();
            $json['type'] = 'room';
            $json['number'] = $object->number;
        } else {
            $params['object_id'] = $restaurant->id;
            $object = null;
            $json['type'] = 'restaurant';
            $json['number'] = null;
        }

        $params['json'] = json_encode($json);
        $params['path_qrcode'] = 'images/qrcodes/'.$qr_code->id.'.svg';

        $params['url'] = env('PANEL_URL').'/?type='.$params['object_type'];
        if ($object !== null) {
            $params['url'] .= '&number='.$object->number;
        }
        $params['url'] .= '&res_id='.$restaurant->id.'&restaurant_id='.$restaurant->id.'&hostname='.$restaurant->hostname;

        DB::connection('tenant')->transaction(function () use ($params, $qr_code, $isAdmin, $website) {
            if (! empty($params)) {
                $qr_code->update($params);
                $qr_code->fresh();

                $filename = $qr_code->id.'.svg';
                if ($website) {
                    $path = storage_path('app/tenancy/tenants/'.$website->uuid.'/images/qrcodes/');
                    if (! is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    QRCodeLibrary::size(500)->generate($params['url'], $path.'/'.$filename);
                } else {
                    $path = public_path('public/images/qrcodes/');
                    if (! is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    QRCodeLibrary::size(500)->generate($params['url'], public_path('public/images/qrcodes/'.$filename));
                }

                return $qr_code;
            }

            return $qr_code;
        });

        return $qr_code;
    }

    /**
     * @param QRCode $qr_code
     *
     * @return QRCode
     */
    public function delete(QRCode $qr_code): QRCode
    {
        DB::connection('tenant')->transaction(function () use ($qr_code) {
            $qr_code->delete();
        });

        return $qr_code;
    }
}
