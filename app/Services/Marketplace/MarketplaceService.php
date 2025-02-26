<?php

namespace App\Services\Marketplace;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class MarketplaceService
{
    protected string $url;
    protected string $token;
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = config('services.marketplace.url');
    }

    public function responseToArray($response)
    {
        $response = json_decode(json_encode($response), true);
        return $response;
    }

    public function toSlug(string $productName)
    {

        $productName = preg_replace('/[^A-Za-z0-9\- ]/', '', $productName);
        $slug = str_replace(" ", "-", $productName);
        $slug = preg_replace('/-+/', '-', $slug);

        return $slug;
    }

    public function EncodeToBase64($url)
    {
        $base64 = base64_encode($url);
        $dataUrl = "data:image/jpeg;base64,$base64";
        return $dataUrl;
    }

    /**
     * @param string $url
     * @param int $expectedStatus
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function get(string $url, int $expectedStatus)
    {
        $options = $this->getHeaders();
        $res = $this->client->request('GET', $this->url . $url, $options);
        return $this->returnResposne($res, $expectedStatus);
    }

    protected function getHeaders()
    {
        $token = $this->token();

        return [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'merge-patch+json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
    }

    /**
     * @return mixed
     */
    protected function token()
    {
        return $this->getToken();

//        return Cache::remember('marketplace_token_' . $jwtToken, 900, function () use ($jwtToken) {
//            return $jwtToken;
//        });
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getToken()
    {

        $options = [
            'json' => [
                'email' => Auth::user()->email,
                'password' => substr(Auth::user()->getAuthPassword(), strlen(Auth::user()->getAuthPassword())-10,10 ),
            ],
        ];
//        dd($options);
            try {
//            $res = $this->client->request('POST', $this->url . '/api/v2/shop/customers/token', $options);
            $res = $this->client->request('POST', $this->url . '/api/v2/shop/authentication-token', $options);
            } catch (ClientException $e) {

            if($e->getResponse()->getStatusCode() == 401){
//                $user =Auth::user();
//                if(!$user->marketplace_registration_status){
                    $this->registerClient();
//                }
//                else{
//                    request()->session()->flash('alert-info', "Twoje konto nie zostało jeszcze zweryfikowane.");
//                    request()->session()->save();
//                    return redirect()->route('admin.marketplace.notVerified')->send();
//                }
            }
        }

        if (isset($res) && $res->getStatusCode() == 200 && $body = $res->getBody()->getContents()) {
            $body = json_decode($body);
            return $body->token;
        }
        return null;
    }

    /**
     * @param $res
     * @param int $expectedStatus
     * @return mixed
     */
    protected function returnResposne($res, int $expectedStatus)
    {
        if ($res->getStatusCode() == $expectedStatus && $body = $res->getBody()->getContents()) {
            return json_decode($body);
        } else {
            return $res->getStatusCode();
        }
    }

    /**
     * @param string $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function delete(string $url, int $expectedStatus, array $additionalOptions = [])
    {
        $token = $this->token();
        $options = [
            'headers' => [
                'accept' => '*/*',
                'Authorization' => 'Bearer ' . $token
            ]
        ];

        $mergedOptions = array_merge($options, $additionalOptions);

        $res = $this->client->request('DELETE', $this->url . $url, $mergedOptions);

        return $this->returnResposne($res, $expectedStatus);
    }

    /**
     * @param string $url
     * @param int $expectedStatus
     * @param array $additionalOptions
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function patch(string $url, int $expectedStatus, array $additionalOptions = [])
    {
        $token = $this->token();

        $options = [
            'headers' => [
                'accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];

        $mergedOptions = array_merge($options, $additionalOptions);
//        dd($mergedOptions,$this->url . $url);
        $res = $this->client->request('PATCH', $this->url . $url, $mergedOptions);
//         dd($this->client->request('PATCH', $this->url . $url, $mergedOptions));
        return $this->returnResposne($res, $expectedStatus);
    }

    protected function put(string $url, int $expectedStatus, array $additionalOptions = [])
    {
        $options = $this->getHeaders();
        $mergedOptions = array_merge($options, $additionalOptions);
        $mergedOptions['headers']['Content-Type'] = 'application/ld+json';
        $mergedOptions['headers']['accept'] = 'application/ld+json';
        $res = $this->client->request('PUT', $this->url . $url, $mergedOptions);
        return $this->returnResposne($res, $expectedStatus);
    }

    protected function registerClient()
    {
        $user = Auth::user();
        $pwd = substr(Auth::user()->getAuthPassword(), strlen(Auth::user()->getAuthPassword())-10,10 );
        $options = [
            'headers' => [
                'accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json'
            ],
            'json' => [
                "firstName" => Auth::user()->first_name,
                "lastName" => Auth::user()->last_name??Auth::user()->first_name,
                "email" => Auth::user()->email,
                "password" => $pwd,
                "subscribedToNewsletter" => false,
            ],
        ];
        try {
          $res=  $this->post('/api/v2/shop/customers', 200, $options);
            if($res === 204){
            }
            $this->verifyUser($user);

        } catch (ClientException $e) {
            Log::error($e->getMessage());
        }
    }

    protected function verifyUser($user)
    {
        $params = [
            'json' => [
                'email' => $user->email,
            ],
        ];
        try {
            $res = $this->post('/verify-user', 204, $params);
        } catch (ClientException $e) {
            Log::error($e->getMessage());
        }
    }
    /**
     * @param string $url
     * @param int $expectedStatus
     * @param array $additionalOptions
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function post(string $url, int $expectedStatus, array $additionalOptions = [])
    {
        $res = $this->client->request('POST', $this->url . $url, $additionalOptions);
        return $this->returnResposne($res, $expectedStatus);
    }

    protected function setDefaultAddress($address)
    {
        $options = [
            'headers' => [
                'accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'Bearer ' . $this->token(),
                'Content-Type+' => 'application/ld+json',
            ],
            'json' => [
                "firstName" => "$address->firstName",
                "lastName" => "$address->lastName",
                "phoneNumber" => "$address->phoneNumber",
                "company" => "$address->company",
                "countryCode" => "$address->countryCode",
                "provinceCode" => "$address->provinceCode",
                "provinceName" => "$address->provinceName",
                "street" => "$address->street",
                "city" => "$address->city",
                "postcode" => "$address->postcode"
            ],
        ];
        $this->post('/api/v2/shop/customers/default-address', 200, $options);
    }
}
