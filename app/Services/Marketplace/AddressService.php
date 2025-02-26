<?php

namespace App\Services\Marketplace;


class AddressService extends MarketplaceService
{

    //TODO : POBIERANIE ADRESU Z EWAITERA
    public function addaddress(array $address)
    {
        $options = $this->getHeaders();
        $options['headers']['Content-Type'] = 'application/ld+json';
        $options['headers']['Accept'] = 'application/ld+json';
        $options['json'] = [
            'firstName' => $address['firstName'],
            'lastName' => $address['lastName'],
            'phoneNumber' => $address['phoneNumber'],
            'company' => $address['company'],
            'countryCode' => $address['country'],
            'provinceCode' => $address['provinceCode'],
            'provinceName' => $address['provinceName'],
            'street' => $address['street'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
        ];
        $this->post('/api/v2/shop/addresses?page=1&itemsPerPage=30', 201, $options);
    }

    public function getaddress()
    {
        $options = $this->getHeaders();
        $options['json'] = [
            // 'channel' => '/api/v2/shop/channels/WEB',
            'localeCode' => __(config('services.marketplace.locale')),
        ];
        $addresses = $this->get('/api/v2/shop/addresses?page=1&itemsPerPage=30', 200, $options);
//        foreach ($addresses as $singleAddress) {
//            if ($singleAddress->provinceCode !== null) {
//                $singleAddress->provinceName = $this->getProvince($singleAddress->provinceCode);
//            }
//        }

        return $addresses;
//         dd($this->get('/api/v2/shop/addresses?page=1&itemsPerPage=30', 200, $options));
//        return $this->get('/api/v2/shop/addresses?page=1&itemsPerPage=30', 200, $options);
    }

    // TODO: JEŚLI NIEMA WOJEWÓDŹTW NA SYLIUSIE TO WYWALA BŁĄD !
    public function getProvince(string $code)
    {
        $options = $this->getHeaders();
        $options['json'] = [
            // 'channel' => '/api/v2/shop/channels/WEB',
            'localeCode' => __(config('services.marketplace.locale')),
        ];
        $province = $this->get('/api/v2/shop/provinces/' . $code, 200, $options);
        return $province->name;
    }

    public function getaddressbyid(string $id)
    {
        $options = $this->getHeaders();
        $options['json'] = [
            // 'channel' => '/api/v2/shop/channels/WEB',
            'localeCode' => __(config('services.marketplace.locale')),
        ];
        $address = $this->get('/api/v2/shop/addresses/' . $id, 200, $options);
        $address->provinceName = $this->getProvince($address->provinceCode);

        return $address;
    }

    public function getCountries()
    {
        $options = $this->getHeaders();
        $options['json'] = [
            // 'channel' => '/api/v2/shop/channels/WEB',
            'localeCode' => __(config('services.marketplace.locale')),
        ];
        return $this->get('/api/v2/shop/countries?page=1&itemsPerPage=30', 200, $options);
    }

    public function getCountryName(string $code)
    {
        $options = $this->getHeaders();
        $options['json'] = [
            // 'channel' => '/api/v2/shop/channels/WEB',
            'localeCode' => __(config('services.marketplace.locale')),
        ];
        $country = $this->get('/api/v2/shop/countries/' . $code, 200, $options);
        return $country->name;
    }

    public function deleteAddress(string $id)
    {

        $options = $this->getHeaders();
        $options['json'] = [
            'id' => $id

        ];
        return $this->delete('/api/v2/shop/addresses/' . $id, 204, $options);
    }

    public function updateAddress(array $address, int $id)
    {
        //dd($address, $id);
        $options = $this->getHeaders();
        $options['json'] = [
            'firstName' => $address['firstName'],
            'lastName' => $address['lastName'],
            'phoneNumber' => $address['phoneNumber'],
            'company' => $address['company'],
            'countryCode' => $address['country'],
            'provinceCode' => $address['provinceCode'],
            'provinceName' => $address['provinceName'],
            'street' => $address['street'],
            'city' => $address['city'],
            'postcode' => $address['postcode'],
        ];
        $this->put('/api/v2/shop/addresses/' . $id, 200, $options);
    }

}
