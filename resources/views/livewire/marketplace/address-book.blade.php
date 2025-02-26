<div class="py-8" wire:init="">
    <h2 class="text-[28px] text-dark-grey-2 my-4 font-medium">{{__('marketplace.delivery address')}}</h2>

    <div class="relative w-full address-book">
        <div class="w-full relative text-dark-grey-2">
            <div class="absolute py-2 px-4 top-[2px]">
                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_67_1200)">
                        <path
                            d="M5 5.00085V5.01085M13 13.0009V13.0109M7.828 7.82885C8.38753 7.26949 8.76862 6.55676 8.92305 5.78081C9.07749 5.00485 8.99835 4.20052 8.69564 3.46955C8.39292 2.73857 7.88023 2.11378 7.22241 1.67421C6.56459 1.23463 5.79118 1 5 1C4.20883 1 3.43542 1.23463 2.7776 1.67421C2.11977 2.11378 1.60708 2.73857 1.30437 3.46955C1.00165 4.20052 0.92251 5.00485 1.07695 5.78081C1.23139 6.55676 1.61247 7.26949 2.172 7.82885L5 10.6579L7.828 7.82885ZM15.828 15.8289C16.3875 15.2695 16.7686 14.5568 16.9231 13.7808C17.0775 13.0048 16.9983 12.2005 16.6956 11.4695C16.3929 10.7386 15.8802 10.1138 15.2224 9.67421C14.5646 9.23463 13.7912 9 13 9C12.2088 9 11.4354 9.23463 10.7776 9.67421C10.1198 10.1138 9.60708 10.7386 9.30437 11.4695C9.00165 12.2005 8.92251 13.0048 9.07695 13.7808C9.23139 14.5568 9.61247 15.2695 10.172 15.8289L13 18.6579L15.828 15.8289Z"
                            stroke="#979FAF" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                        <clipPath id="clip0_67_1200">
                            <rect width="18" height="20" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
            </div>
            <input
                id="address-input"
                class="pl-10 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input outline-none"
                type="text" placeholder="Wybierz z listy zapisanych adresów" autocomplete="off" tabindex="0"
                wire:model="selectedAddress"
                wire:focus="showAddressList"
                wire:blur="hideAddressList"
            >
        </div>
        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
            <i class="fas fa-book"></i>
        </div>
        <div id="address-list"
             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg @if(!$isAddressListVisible) hidden @endif">
            <ul>
                @foreach($addresses as $address)
                    <li class="p-2 cursor-pointer hover:bg-gray-200"
                        wire:click="selectAddress({{ $address['id'] }})"
                        onclick="setAddress('{{ $address['firstName'] }} {{ $address['lastName'] }}', '{{ $address['street'] }}', '{{ $address['city'] }}', '{{ $address['postcode'] }}', '{{ $address['countryCode'] }}')">
                        <strong>{{ $address['firstName'] }} {{ $address['lastName'] }}</strong>, {{ $address['street'] }}
                        ,
                        {{ $address['city'] }} {{ $address['postcode'] }},
                    </li>
                @endforeach
            </ul>
        </div>
        {{--        @dump($addresses)--}}
    </div>

    <script>
        function showAddressList() {
        @this.set("isAddressListVisible", true)
            ;
        }

        function hideAddressList() {
            setTimeout(function() {
            @this.set("isAddressListVisible", false)
                ;
            });
        }

        function setAddress(firstName, lastName, street, city, postcode, countryCode) {
            document.getElementById("address-input").value = `${firstName} ${lastName}, ${street}, ${city} ${postcode}, ${countryCode}`;
        @this.set("selectedAddress", `${firstName} ${lastName}, ${street}, ${city} ${postcode}, ${countryCode}`)
            ;
        }
    </script>


    {{--    <div>--}}
    {{--        <form id="address-form" action="" wire:submit.prevent="submitForm">--}}
    {{--            @csrf--}}
    {{--            <input type="hidden" id="address-id" name="id" value="{{ $address['id'] }}">--}}

    {{--            <div class="grid grid-cols-1 gap-4">--}}
    {{--                <div class="flex items-center">--}}
    {{--                    <label for="first-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">First Name</label>--}}
    {{--                    <input type="text" id="first-name" name="first-name"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.firstName">--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="last-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Last Name</label>--}}
    {{--                    <input type="text" id="last-name" name="last-name"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.lastName">--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="company" class="block text-gray-700 text-sm font-bold mb-2 w-32">Company</label>--}}
    {{--                    <input type="text" id="company" name="company"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.company">--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="street" class="block text-gray-700 text-sm font-bold mb-2 w-32">Street</label>--}}
    {{--                    <input type="text" id="street" name="street"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.street">--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="country-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Country--}}
    {{--                        Code</label>--}}
    {{--                    <input type="text" id="country-code" name="country-code"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.countryCode">--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="province-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province--}}
    {{--                        Code</label>--}}
    {{--                    <input type="text" id="province-code" name="province-code"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.provinceCode">--}}
    {{--                    <p class="text-xs text-gray-500 ml-4">Province Code is optional</p>--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="province-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province--}}
    {{--                        Name</label>--}}
    {{--                    <input type="text" id="province-name" name="province-name"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.provinceName">--}}
    {{--                    <p class="text-xs text-gray-500 ml-4">Province Name is optional</p>--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="city" class="block text-gray-700 text-sm font-bold mb-2 w-32">City</label>--}}
    {{--                    <input type="text" id="city" name="city"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.city">--}}
    {{--                    <p class="text-xs text-gray-500 ml-4">City is optional</p>--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="postcode" class="block text-gray-700 text-sm font-bold mb-2 w-32">Postcode</label>--}}
    {{--                    <input type="text" id="postcode" name="postcode"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.postcode">--}}
    {{--                    <p class="text-xs text-gray-500 ml-4">Postcode is optional</p>--}}
    {{--                </div>--}}

    {{--                <div class="flex items-center">--}}
    {{--                    <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2 w-32">Phone--}}
    {{--                        Number</label>--}}
    {{--                    <input type="text" id="phone-number" name="phone-number"--}}
    {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
    {{--                           wire:model="address.phoneNumber">--}}
    {{--                    <p class="text-xs text-gray-500 ml-4">Phone Number is optional</p>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </form>--}}
    {{--    </div>--}}

</div>


{{--@foreach($addresses as $address)--}}
{{--    <li class="p-2 cursor-pointer hover:bg-gray-200"--}}
{{--        wire:click="selectAddress({{ $address['id'] }})"--}}
{{--        data-id="{{ $address['id'] }}"--}}
{{--        data-first-name="{{ $address['firstName'] }}"--}}
{{--        data-last-name="{{ $address['lastName'] }}"--}}
{{--        data-company="{{ $address['company'] }}"--}}
{{--        data-street="{{ $address['street'] }}"--}}
{{--        data-country-code="{{ $address['countryCode'] }}"--}}
{{--        data-province-code="{{ $address['provinceCode'] }}"--}}
{{--        data-province-name="{{ $address['provinceName'] }}"--}}
{{--        data-city="{{ $address['city'] }}"--}}
{{--        data-postcode="{{ $address['postcode'] }}"--}}
{{--        data-phone-number="{{ $address['phoneNumber'] }}">--}}
{{--        <strong>{{ $address['firstName'] }} {{ $address['lastName'] }}</strong>, {{ $address['street'] }},--}}
{{--        {{ $address['city'] }} {{ $address['postcode'] }}, {{ $address['countryCode'] }}--}}
{{--    </li>--}}
{{--@endforeach--}}

{{--<div>--}}
{{--    <form id="address-form" action="" wire:submit.prevent="submitForm">--}}
{{--        @csrf--}}
{{--        <input type="hidden" id="address-id" name="id" value="{{ $addressId }}">--}}

{{--        <div class="grid grid-cols-1 gap-4">--}}
{{--            <div class="flex items-center">--}}
{{--                <label for="first-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">First Name</label>--}}
{{--                <input type="text" id="first-name" name="first-name"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="firstName">--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="last-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Last Name</label>--}}
{{--                <input type="text" id="last-name" name="last-name"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="lastName">--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="company" class="block text-gray-700 text-sm font-bold mb-2 w-32">Company</label>--}}
{{--                <input type="text" id="company" name="company"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="company">--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="street" class="block text-gray-700 text-sm font-bold mb-2 w-32">Street</label>--}}
{{--                <input type="text" id="street" name="street"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="street">--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="country-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Country Code</label>--}}
{{--                <input type="text" id="country-code" name="country-code"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="countryCode">--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="province-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province Code</label>--}}
{{--                <input type="text" id="province-code" name="province-code"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="provinceCode">--}}
{{--                <p class="text-xs text-gray-500 ml-4">Province Code is optional</p>--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="province-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province Name</label>--}}
{{--                <input type="text" id="province-name" name="province-name"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="provinceName">--}}
{{--                <p class="text-xs text-gray-500 ml-4">Province Name is optional</p>--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="city" class="block text-gray-700 text-sm font-bold mb-2 w-32">City</label>--}}
{{--                <input type="text" id="city" name="city"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="city">--}}
{{--                <p class="text-xs text-gray-500 ml-4">City is optional</p>--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="postcode" class="block text-gray-700 text-sm font-bold mb-2 w-32">Postcode</label>--}}
{{--                <input type="text" id="postcode" name="postcode"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="postcode">--}}
{{--                <p class="text-xs text-gray-500 ml-4">Postcode is optional</p>--}}
{{--            </div>--}}

{{--            <div class="flex items-center">--}}
{{--                <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2 w-32">Phone Number</label>--}}
{{--                <input type="text" id="phone-number" name="phone-number"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
{{--                       wire:model="phoneNumber">--}}
{{--                <p class="text-xs text-gray-500 ml-4">Phone Number is optional</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </form>--}}
{{--</div>--}}


{{--<script>--}}
{{--    document.addEventListener('DOMContentLoaded', function() {--}}
{{--        const addressList = document.getElementById('address-list');--}}
{{--        const addressInput = document.getElementById('address-input');--}}

{{--        addressList.addEventListener('click', function(event) {--}}
{{--            const target = event.target.closest('li');--}}
{{--            if (target) {--}}
{{--                const id = target.getAttribute('data-id');--}}
{{--                const firstName = target.getAttribute('data-first-name');--}}
{{--                const lastName = target.getAttribute('data-last-name');--}}
{{--                const company = target.getAttribute('data-company');--}}
{{--                const street = target.getAttribute('data-street');--}}
{{--                const countryCode = target.getAttribute('data-country-code');--}}
{{--                const provinceCode = target.getAttribute('data-province-code');--}}
{{--                const provinceName = target.getAttribute('data-province-name');--}}
{{--                const city = target.getAttribute('data-city');--}}
{{--                const postcode = target.getAttribute('data-postcode');--}}
{{--                const phoneNumber = target.getAttribute('data-phone-number');--}}


{{--                // Set the values in the input fields--}}
{{--                form.querySelector('#address-id').value = id;--}}
{{--                form.querySelector('#first-name').value = firstName;--}}
{{--                form.querySelector('#last-name').value = lastName;--}}
{{--                form.querySelector('#company').value = company;--}}
{{--                form.querySelector('#street').value = street;--}}
{{--                form.querySelector('#country-code').value = countryCode;--}}
{{--                form.querySelector('#province-code').value = provinceCode;--}}
{{--                form.querySelector('#province-name').value = provinceName;--}}
{{--                form.querySelector('#city').value = city;--}}
{{--                form.querySelector('#postcode').value = postcode;--}}
{{--                form.querySelector('#phone-number').value = phoneNumber;--}}

{{--                // Set the value in the address input field--}}
{{--                addressInput.value = `${firstName} ${lastName}, ${street}, ${city} ${postcode}, ${countryCode}`;--}}

{{--                // Hide the address list--}}
{{--                addressList.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Show address list on input focus--}}
{{--        addressInput.addEventListener('focus', function() {--}}
{{--            addressList.classList.remove('hidden');--}}
{{--        });--}}

{{--        // Hide address list if clicked outside--}}
{{--        document.addEventListener('click', function(event) {--}}
{{--            if (!addressList.contains(event.target) && !addressInput.contains(event.target)) {--}}
{{--                addressList.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}
