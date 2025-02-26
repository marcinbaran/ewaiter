<?php

namespace App\Services;

class GuzzleService
{
    protected $baseUri;

    protected $clientId;

    protected $clientSecret;

    protected $getTokenUri;

    protected $accessToken;

    public function __construct()
    {
        if (config('app.curl_test')) {
            $client = new \GuzzleHttp\Client([
                'curl'   => [CURLOPT_SSL_VERIFYPEER => false],
                'verify' => false,
                'headers' => ['Content-Type' => 'application/json'],
            ]);
        } else {
            $client = new \GuzzleHttp\Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);
        }
        $this->baseUri = config('admanager.url');
        $this->client = $client;
        $this->clientId = 'wirtualnykelner';
        $this->clientSecret = 'zetoapp';
        $this->getTokenUri = '/auth';
        $this->accessToken = $this->getToken($this->clientId, $this->clientSecret, $this->getTokenUri);
        $this->headers = [
                'Authorization' => 'Bearer '.$this->accessToken,
                'Accept'        => 'application/json',
            ];
    }

    public function getToken($clientId, $clientSecret, $getTokenUri)
    {
        $res = $this->client->request('POST', $this->baseUri.$this->getTokenUri, [
            'body' => json_encode(['login' => $clientId, 'password' => $clientSecret]),
            'http_errors' => false,
        ]);
        if ($res->getStatusCode() == 200 && $body = $res->getBody()->getContents()) {
            $body = json_decode($body);
            $access_token = $body->access_token;

            return $access_token;
        }

        return null;
    }

    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $hasFile = false)
    {
        $client = $this->client;
        $bodyType = 'form_params';
        if ($hasFile) {
            $bodyType = 'multipart';
            $multipart = [];
            foreach ($formParams as $name => $contents) {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents,
                ];
            }
        }
        $response = $client->request($method, $this->baseUri.$requestUrl, [
            'query' => $queryParams,
            $bodyType => $hasFile ? $multipart : $formParams,
            'headers' => $this->headers,
            'http_errors' => false,
        ]);
        $response = $response->getBody()->getContents();

        return $response;
    }
}
