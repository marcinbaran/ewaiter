<?php

namespace App\Http\Livewire\Marketplace;

use AllowDynamicProperties;
use App\Services\Marketplace\AddressService;
use App\Services\Marketplace\CheckoutService;
use Livewire\Component;

#[AllowDynamicProperties]
class AddressBook extends Component
{
    public $addresses;
    public $selectedAddress = '';
    public $isAddressListVisible = false;
    public $parentReady = false;
    public $addressForm = [
        'id' => null,
        'firstName' => '',
        'lastName' => '',
        'company' => '',
        'street' => '',
        'countryCode' => '',
        'provinceCode' => '',
        'provinceName' => '',
        'city' => '',
        'postcode' => '',
        'phoneNumber' => ''
    ];
    public $countryList;

    protected $listeners = ['parentComponentReady' => 'handleParentReady', 'submitForm' => 'submitForm'];
    private $addressService;
    private $checkoutService;

    public function boot(AddressService $addressService, CheckoutService $checkoutService)
    {
        $this->addressService = $addressService;
        $this->checkoutService = $checkoutService;
    }

    public function handleParentReady()
    {
        $this->parentReady = true;
        $response = $this->addressService->getaddress();
        $this->addresses = $this->addressService->responseToArray($response);
    }

    public function render()
    {
        $response = $this->addressService->getaddress();
        $this->addresses = $this->addressService->responseToArray($response);
        $this->countryList = $this->addressService->getCountries();
        return view('livewire.marketplace.address-book',);
    }

    public function submitForm()
    {
//        dump($this->addressForm);
        $this->checkoutService->setAddressforCheckout($this->addressForm);
    }

    public function showAddressList()
    {
        $this->isAddressListVisible = true;
    }

    public function selectAddress($addressId)
    {

        $address = collect($this->addresses)->firstWhere('id', $addressId);
        $this->selectedAddress = "{$address['firstName']} {$address['lastName']}, {$address['street']}, {$address['city']} {$address['postcode']}, {$address['countryCode']}";
        $this->isAddressListVisible = false;
        $this->emitTo('marketplace.checkout-address-form', 'addresSelected', $address);
    }

    public function hideAddressList()
    {
        $this->isAddressListVisible = false;
    }








//
//    protected $rules = [
//        'firstName' => 'required|string|max:255',
//        'lastName' => 'required|string|max:255',
//        'company' => 'nullable|string|max:255',
//        'street' => 'required|string|max:255',
//        'countryCode' => 'required|string|max:10',
//        'provinceCode' => 'nullable|string|max:10',
//        'provinceName' => 'nullable|string|max:255',
//        'city' => 'required|string|max:255',
//        'postcode' => 'required|string|max:20',
//        'phoneNumber' => 'nullable|string|max:20',
//    ];
//

//
//    public function submitForm()
//    {
//        $this->validate();
//
//        $addressData = [
//            'firstName' => $this->firstName,
//            'lastName' => $this->lastName,
//            'company' => $this->company,
//            'street' => $this->street,
//            'countryCode' => $this->countryCode,
//            'provinceCode' => $this->provinceCode,
//            'provinceName' => $this->provinceName,
//            'city' => $this->city,
//            'postcode' => $this->postcode,
//            'phoneNumber' => $this->phoneNumber,
//            'addressId' => $this->addressId,
//        ];
//
//        $this->emitUp('addressSubmitted', $addressData);
//    }
//
//    public function selectAddress(int $id)
//    {
//
//        $addresses = json_decode(json_encode($this->addresses), true);
//        $this->addressId = $id;
//
//        foreach ($addresses as $address) {
//            if ($address['id'] == $id) {
//                $this->firstName = $address['firstName'];
//                $this->lastName = $address['lastName'];
//                $this->company = $address['company'];
//                $this->street = $address['street'];
//                $this->countryCode = $address['countryCode'];
//                $this->provinceCode = $address['provinceCode'];
//                $this->provinceName = $address['provinceName'];
//                $this->city = $address['city'];
//                $this->postcode = $address['postcode'];
//                $this->phoneNumber = $address['phoneNumber'];
//                break;
//            }
//        }
//    }
}
