<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class OneSignalService
{
    /**
     * @var string
     */
    public const URL_NOTIFICATIONS = 'https://onesignal.com/api/v1/notifications';

    /**
     * @var string
     */
    public const URL_PLAYERS_DEVICE = 'https://onesignal.com/api/v1/players/';

    /**
     * @var string
     */
    private $apiId;

    /**
     * @var string
     */
    private $restApiKey;

    /**
     * @var string
     */
    private $defaultCode;

    /**
     * @var JsonResponse
     */
    private $response;

    /**
     * @param string $apiId
     * @param string $restApiKey
     * @param string $defaultCode
     */
    public function __construct(string $apiId, string $restApiKey, string $defaultCode)
    {
        $this->apiId = $apiId;
        $this->restApiKey = $restApiKey;
        $this->defaultCode = $defaultCode;
    }

    /**
     * @param string $playerId
     *
     * @return OneSignalService
     */
    public function getPlayerDeviceInfo(string $playerId): self
    {
        $this->response = $this->curlRequest(self::URL_PLAYERS_DEVICE.$playerId);

        return $this;
    }

    /**
     * @param array        $playerIds
     * @param array|string $message
     * @param array        $data
     *
     * @return OneSignalService
     */
    public function pushNotification(array $playerIds, $message = null, array $data = null): self
    {
        $fields = [
            'app_id' => $this->apiId,
            'include_player_ids' => $playerIds,
            'data' => (array) $data,
            'content_available' => true,
        ];

        if (! empty($message)) {
            /*
             * one siglan contents must be key/value collections by language code
             * $message=["pl"=>"Polish message"]
             */
            if (is_string($message)) {
                $message = [$this->defaultCode => $message];
            }
            $fields['contents'] = $message;
        }

        $this->response = $this->curlRequest(self::URL_NOTIFICATIONS, json_encode($fields));

        return $this;
    }

    /**
     * @return null|JsonResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string $url
     * @param string $fields
     *
     * @return JsonResponse
     */
    private function curlRequest(string $url, string $fields = null): JsonResponse
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.$this->restApiKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (! empty($fields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new JsonResponse($response, $httpcode);
    }
}
