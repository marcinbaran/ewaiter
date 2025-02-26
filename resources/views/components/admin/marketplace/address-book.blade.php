@props(['addresses' => []])
{{--@dump($addresses)--}}
<div>
    <p class="text-xl font-bold mb-4">Address Book</p>
</div>
<div class="relative w-full mb-6">
    <input
        id="address-input"
        class="block w-full pl-10 pr-3 py-2 leading-tight text-gray-700 bg-white border border-gray-300 rounded shadow-sm focus:outline-none focus:shadow-outline"
        type="text" placeholder="Select address from my book" autocomplete="off" tabindex="0">
    <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
        <i class="fas fa-book"></i>
    </div>
    <div id="address-list" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded shadow-lg hidden">
        <ul>
            @foreach($addresses as $address)
                <li class="p-2 cursor-pointer hover:bg-gray-200" data-id="{{ $address->id }}"
                    data-first-name="{{ $address->firstName }}" data-last-name="{{ $address->lastName }}"
                    data-company="{{ $address->company }}" data-street="{{ $address->street }}"
                    data-country-code="{{ $address->countryCode }}" data-province-code="{{ $address->provinceCode }}"
                    data-province-name="{{ $address->provinceName }}" data-city="{{ $address->city }}"
                    data-postcode="{{ $address->postcode }}" data-phone-number="{{ $address->phoneNumber }}">
                    <strong>{{ $address->firstName }} {{ $address->lastName }}</strong>, {{ $address->street }}
                    , {{ $address->city }} {{ $address->postcode }}, {{ $address->countryCode }}
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div>
    <form action="{{-- {{ route('admin.marketplace.address-book.update') }} --}}" method="post">
        @csrf
        <input type="hidden" id="address-id" name="id" value="">
        <div class="grid grid-cols-1 gap-4">
            <div class="flex items-center">
                <label for="first-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">First Name</label>
                <input type="text" id="first-name" name="first-name" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center">
                <label for="last-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Last Name</label>
                <input type="text" id="last-name" name="last-name" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center">
                <label for="company" class="block text-gray-700 text-sm font-bold mb-2 w-32">Company</label>
                <input type="text" id="company" name="company" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center">
                <label for="street" class="block text-gray-700 text-sm font-bold mb-2 w-32">Street</label>
                <input type="text" id="street" name="street" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center">
                <label for="country-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Country Code</label>
                <input type="text" id="country-code" name="country-code" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center">
                <label for="province-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province Code</label>
                <input type="text" id="province-code" name="province-code" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 ml-4">Province Code is optional</p>
            </div>
            <div class="flex items-center">
                <label for="province-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province Name</label>
                <input type="text" id="province-name" name="province-name" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 ml-4">Province Name is optional</p>
            </div>
            <div class="flex items-center">
                <label for="city" class="block text-gray-700 text-sm font-bold mb-2 w-32">City</label>
                <input type="text" id="city" name="city" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 ml-4">City is optional</p>
            </div>
            <div class="flex items-center">
                <label for="postcode" class="block text-gray-700 text-sm font-bold mb-2 w-32">Postcode</label>
                <input type="text" id="postcode" name="postcode" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 ml-4">Postcode is optional</p>
            </div>
            <div class="flex items-center">
                <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2 w-32">Phone Number</label>
                <input type="text" id="phone-number" name="phone-number" value="" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 ml-4">Phone Number is optional</p>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("address-input");
        const addressList = document.getElementById("address-list");
        const listItems = addressList.querySelectorAll("li");

        // Function to set the address details to the input field and form
        function setAddressDetails(item) {
            const addressDetails = `${item.dataset.firstName} ${item.dataset.lastName}, ${item.dataset.street}, ${item.dataset.city} ${item.dataset.postcode}, ${item.dataset.countryCode}`;
            input.value = addressDetails;

            document.getElementById('address-id').value = item.dataset.id;
            document.getElementById('first-name').value = item.dataset.firstName;
            document.getElementById('last-name').value = item.dataset.lastName;
            document.getElementById('company').value = item.dataset.company;
            document.getElementById('street').value = item.dataset.street;
            document.getElementById('country-code').value = item.dataset.countryCode;
            document.getElementById('province-code').value = item.dataset.provinceCode;
            document.getElementById('province-name').value = item.dataset.provinceName;
            document.getElementById('city').value = item.dataset.city;
            document.getElementById('postcode').value = item.dataset.postcode;
            document.getElementById('phone-number').value = item.dataset.phoneNumber;
        }

        // Show the list when the input is focused
        input.addEventListener("focus", function() {
            addressList.classList.remove("hidden");
        });

        // Hide the list when clicking outside
        document.addEventListener("click", function(event) {
            if (!addressList.contains(event.target) && event.target !== input) {
                addressList.classList.add("hidden");
            }
        });

        // Set the input value and hide the list when selecting an address
        listItems.forEach(function(item) {
            item.addEventListener("click", function() {
                setAddressDetails(item);
                addressList.classList.add("hidden");
            });
        });

        // Automatically set the first address as default
        if (listItems.length > 0) {
            setAddressDetails(listItems[0]);
        }
    });
</script>
