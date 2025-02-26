<?php

namespace App\Http\Helpers;

class ImagesHelper
{
    public const ENDPOINT = 'https://imageconverter.e-waiter.pl/convert-to-webp';

    public static function convertToWebp($data)
    {
        $imagePath = $data->getPathName();
        $imageMime = $data->getMimeType();
        $imageOrigin = $data->getClientOriginalName();
        $newFile = new \CURLFile($imagePath, $imageMime, $imageOrigin);

        $curl = curl_init(self::ENDPOINT);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['file' => $newFile]);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, 'admin:VIsHdvsVeEgTkbsgA0Lh');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($result, true);

        return $result['base64'];
    }

    public static function base64ToPhoto($base64)
    {
        $image = base64_decode($base64);

        return $image;
    }
}
