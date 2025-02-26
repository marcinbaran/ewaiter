<?php

namespace App\Http\Livewire\Marketplace;

use App\Services\Marketplace\AddressService;
use App\Services\Marketplace\CheckoutService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CheckoutAddressForm extends Component
{
    public $countries;

    public $step;

    public $address = [
        'id' => null,
        'firstName' => '',
        'lastName' => '',
        'company' => '',
        'nip' => '',
        'phoneNumber' => '',
        'countryCode' => '',
        'countryName' => '',
        'provinceCode' => '',
        'provinceName' => '',
        'city' => '',
        'street' => '',
        'postcode' => '',
        'buildingNumber' => '',
        'apartmentNumber' => '',
    ];


    protected $rules = [
        'address.firstName' => 'required',
        'address.lastName' => 'required',
        'address.company' => 'required',
//        'address.nip' => 'required',
        'address.street' => 'required',
        'address.countryCode' => 'required',
        'address.provinceCode' => 'required',
        'address.city' => 'required',
        'address.phoneNumber' => 'required',
        'address.postcode' => 'required',
    ];
    protected $listeners = ['addresSelected' => 'handleAddressSelected', 'addressValidationNextStep' => 'handleNextStep'];
    private $addressService;
    private $checkoutService;

    public function mount($step)
    {
        $this->step = $step;
    }

    public function boot(AddressService $addressService, CheckoutService $checkoutService)
    {
        $this->addressService = $addressService;
        $this->checkoutService = $checkoutService;
        $this->countries = $this->addressService->responseToArray($this->addressService->getCountries());
    }

    public function render()
    {
        $response = $this->addressService->getaddress();
        $this->addresses = $this->addressService->responseToArray($response);
        return view('livewire.marketplace.checkout-address-form');
    }

    public function handleAddressSelected($selectedAddress)
    {
        foreach ($this->countries as $country) {
            if ($country['code'] == $selectedAddress['countryCode']) {
                $selectedAddress['countryName'] = $country['name'];
            }
        }
        $this->address = $selectedAddress;
    }

    public function backStep()
    {
        $this->emitTo('marketplace.checkout', 'backStep');
    }

    public function nextStep()
    {
//        dd($this->address);
        $this->validate();
//        $this->address['provinceName'] = $this->addressService->getProvince($this->address['provinceCode']);
//        $this->address['countryName'] = $this->addressService->getCountryName($this->address['countryCode']);
        $response = $this->addressService->responseToArray($this->checkoutService->setAddressforCheckout($this->address));
//        Session::put('cart-data', $response);
//        dd($response);
//        session()->put('cart-data', $response);
        $this->emitTo('marketplace.checkout', 'nextStep');
    }

    public function handleNextStep()
    {

//        $newAddress = [];
//        $newAddress['firstName'] = 'karol';
//        $newAddress['lastName'] = 'strraczek';
//        $newAddress['countryCode'] = 'PL';
//        $newAddress['street'] = 'krajobrazowa';
//        $newAddress['provinceCode'] = 'PL-02';
//        $newAddress['city'] = 'rzeszow';
//        $newAddress['postcode'] = '35119';
//        $newAddress['company'] = 'gogle';
//        $newAddress['phoneNumber'] = '51254125';
//        $newAddress['provinceCode'] = $this->address['provinceCode'];
////        $newAddress['provinceName'] = $this->addressService->getProvince($newAddress['provinceCode']);
//        $newAddress['countryName'] = $this->addressService->getCountryName('PL');
//        $newAddress['buildingNumber'] = '2137';
////        $this->validate();
////        $this->address['countryName'] = $this->addressService->getCountryName($this->address['countryCode']);
        $response = $this->addressService->responseToArray($this->checkoutService->setAddressforCheckout($this->address));
        session()->put('cart-data', $response);
//        dd(session()->get('cart-data'));
        $this->emitTo('marketplace.checkout', 'nextStep');
    }

}
