<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class GoogleDistanceService
{
    private const string API_URL = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    private const string DEFAULT_TRAFFIC_MODEL = 'optimistic';

    private const string MODE_DRIVING = 'driving';

    private const string UNITS_METRICAL = 'metrical';

    public function __construct(
        private string $apiKey,
    ) {
    }

    public function calculate(string $origins, string $destinations, ?string $trafficModel = null): int
    {
        $client = new Client();
        $tomorrowAt3AmUnixTime = Carbon::tomorrow()->addHours(3)->unix();

        try {
            $response = $client->get(self::API_URL, [
                'query' => [
                    'units' => self::UNITS_METRICAL,
                    'origins' => $origins,
                    'destinations' => $destinations,
                    'mode' => self::MODE_DRIVING,
                    'traffic_model' => $trafficModel ?? self::DEFAULT_TRAFFIC_MODEL,
                    'departure_time' => $tomorrowAt3AmUnixTime,
                    'key' => $this->apiKey,
                    'random' => random_int(1, 100),
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === Response::HTTP_OK) {
                $responseData = json_decode($response->getBody()->getContents());

                if (isset($responseData->rows[0]->elements[0]->distance)) {
                    return $responseData->rows[0]->elements[0]->distance->value;
                }
            }

            return -1;
        } catch (Exception $e) {
            return -1;
        }
    }
}
