@props(['countries'=>[], 'address'=>[]])
<form name="addAddress"
      action="{{$address ? route('admin.marketplace.updateAddress', $address->id): route('admin.marketplace.addAddress')}}"
      method="post"
      class="space-y-8"
      novalidate="novalidate">
    @method('POST')

    <div id="sylius-billing-address">
        <h3 class="text-lg font-medium leading-6 text-gray-900 border-b border-gray-200 pb-2 mt-4">
            {{ $address ? __('marketplace.address form.title_edit') : __('marketplace.address form.title_new') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label for="addAddress_address_billingAddress_firstName"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.firstname')}}</label>
                <input type="text" id="addAddress_address_billingAddress_firstName"
                       name="addAddress_address[billingAddress][firstName]" required="required"
                       value="{{ old('addAddress_address.billingAddress.firstName', $address->firstName ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="addAddress_address_billingAddress_lastName"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.lastname')}}</label>
                <input type="text" id="addAddress_address_billingAddress_lastName"
                       name="addAddress_address[billingAddress][lastName]" required="required"
                       value="{{ old('addAddress_address.billingAddress.lastName', $address->lastName ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="col-span-2">
                <label for="addAddress_address_billingAddress_company"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.company')}}</label>
                <input type="text" id="addAddress_address_billingAddress_company"
                       name="addAddress_address[billingAddress][company]"
                       value="{{ old('addAddress_address.billingAddress.company', $address->company ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="addAddress_address_billingAddress_country"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.country')}}</label>
                <select id="addAddress_address_billingAddress_country"
                        name="addAddress_address[billingAddress][country]"
                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option
                        value="">{{ old('addAddress_address.billingAddress.country',__('marketplace.address form.select country')) }}</option>
                    @foreach ($countries as $country)
                        <option value="{{$country->code}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="addAddress_address_billingAddress_province"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.province')}}</label>

                <select id="addAddress_address_billingAddress_province"
                        name="addAddress_address[billingAddress][provinceCode]"
                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option
                        value="">{{ old('addAddress_address.billingAddress.province', __('marketplace.address form.select province'))}}</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="addAddress_address_billingAddress_street"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.street')}}</label>
                <input type="text" id="addAddress_address_billingAddress_street"
                       name="addAddress_address[billingAddress][street]"
                       value="{{ old('addAddress_address.billingAddress.street', $address->street ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="addAddress_address_billingAddress_city"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.city')}}</label>
                <input type="text" id="addAddress_address_billingAddress_city"
                       name="addAddress_address[billingAddress][city]"
                       value="{{ old('addAddress_address.billingAddress.city', $address->city ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="addAddress_address_billingAddress_postcode"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.postcode')}}</label>
                <input type="text" id="addAddress_address_billingAddress_postcode"
                       name="addAddress_address[billingAddress][postcode]"
                       value="{{ old('addAddress_address.billingAddress.postcode', $address->postcode ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="col-span-2">
                <label for="addAddress_address_billingAddress_phoneNumber"
                       class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.phone')}}</label>
                <input type="text" id="addAddress_address_billingAddress_phoneNumber"
                       name="addAddress_address[billingAddress][phoneNumber]"
                       value="{{ old('addAddress_address.billingAddress.phoneNumber', $address->phoneNumber ?? ' ') }}"
                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <!-- Hidden inputs for province code and name -->
            <input type="hidden" id="addAddress_address_billingAddress_provinceCode"
                   name="addAddress_address[billingAddress][provinceCode]">
            <input type="hidden" id="addAddress_address_billingAddress_provinceName"
                   name="addAddress_address[billingAddress][provinceName]">
        </div>

    </div>
    <div class="hidden mt-6"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div>
            <a href="{{ url('/admin/marketplace/address') }}"
               class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> {{__('marketplace.back to address list')}}
            </a>
        </div>
        <div class="text-right">
            <button type="submit" id="next-step"
                    class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-arrow-right mr-2"></i> {{ $address ? __('marketplace.update address') : __('marketplace.add new address') }}
            </button>
        </div>
    </div>

    @csrf
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const countries = @json($countries);
        const countrySelect = document.getElementById("addAddress_address_billingAddress_country");
        const provinceSelect = document.getElementById("addAddress_address_billingAddress_province");
        const provinceCodeInput = document.getElementById("addAddress_address_billingAddress_provinceCode");
        const provinceNameInput = document.getElementById("addAddress_address_billingAddress_provinceName");


        countrySelect.addEventListener("change", function() {
            const selectedCountryCode = this.value;
            const selectedCountry = countries.find(country => country.code === selectedCountryCode);
            const provinces = selectedCountry ? selectedCountry.provinces : [];

            provinceSelect.innerHTML = '<option value="">{{ old('addAddress_address.billingAddress.province', __('marketplace.address form.select province'))}}</option>';

            provinces.forEach(function(province) {
                const option = document.createElement("option");
                option.value = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
            provinceSelect.addEventListener("change", function() {
                const selectedProvince = provinceSelect.options[provinceSelect.selectedIndex];
                provinceCodeInput.value = selectedProvince.value;
                provinceNameInput.value = selectedProvince.text;
            });
        });
    });
</script>
