<div>
    {{--    @dump($address)--}}
    <div>
        {{--        <form id="address-form" action="" wire:submit.prevent="submitForm">--}}
        {{--            @csrf--}}

        {{--            <input type="hidden" id="address-id" name="id" wire:model="address.id">--}}

        {{--            <div class="grid grid-cols-1 gap-4">--}}
        {{--                <div class="flex items-center">--}}
        {{--                    <label for="first-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">First--}}
        {{--                        Name</label>--}}
        {{--                    <input type="text" id="first-name" name="first-name" required --}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.firstName">--}}
        {{--                    @error('address.firstName') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}

        {{--                <div class="flex items-center">--}}
        {{--                    <label for="last-name" class="block text-gray-700 text-sm font-bold mb-2 w-32">Last Name</label>--}}
        {{--                    <input type="text" id="last-name" name="last-name" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.lastName">--}}
        {{--                    @error('address.lastName') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}

        {{--                <div class="flex items-center">--}}
        {{--                    <label for="company" class="block text-gray-700 text-sm font-bold mb-2 w-32">Company</label>--}}
        {{--                    <input type="text" id="company" name="company" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.company">--}}
        {{--                    @error('address.company') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}

        {{--                <div class="flex items-center">--}}
        {{--                    <label for="street" class="block text-gray-700 text-sm font-bold mb-2 w-32">Street</label>--}}
        {{--                    <input type="text" id="street" name="street" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.street">--}}
        {{--                    @error('address.street') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}

        {{--                --}}{{--                    DO UKRYCIA --}}
        {{--                <div class="flex items-center hidden">--}}
        {{--                    <label for="country-code" class="block text-gray-700 text-sm font-bold mb-2 w-32 ">Country--}}
        {{--                        Code </label>--}}
        {{--                    <input type="text" id="country-code" name="country-code" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.countryCode">--}}
        {{--                </div>--}}
        {{--                <div>--}}
        {{--                    <label for="addAddress_address_billingAddress_country"--}}
        {{--                           class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.country')}}</label>--}}
        {{--                    <select id="addAddress_address_billingAddress_country" required--}}
        {{--                            name="addAddress_address[billingAddress][country]"--}}
        {{--                            wire:model.live="address.countryCode"--}}
        {{--                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">--}}
        {{--                        <option--}}
        {{--                            value="">{{ old('addAddress_address.billingAddress.country',__('marketplace.address form.select country')) }}</option>--}}
        {{--                        @foreach ($countries as $country)--}}
        {{--                            <option--}}
        {{--                                value="{{$country['code']}}">{{$country['name']}}</option>--}}
        {{--                        @endforeach--}}
        {{--                    </select>--}}
        {{--                    @error('address.countryCode') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}


        {{--                <div>--}}
        {{--                    <label for="addAddress_address_billingAddress_province"--}}
        {{--                           class="block text-sm font-medium text-gray-700">{{__('marketplace.address form.province')}}</label>--}}

        {{--                    <select id="addAddress_address_billingAddress_province"--}}
        {{--                            wire:model.change="address.provinceCode" required--}}
        {{--                            name="addAddress_address[billingAddress][provinceCode]"--}}
        {{--                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">--}}
        {{--                        <option--}}
        {{--                            value="">{{ old('addAddress_address.billingAddress.province',__('marketplace.address form.select province')) }}</option>--}}

        {{--                        @foreach ($country['provinces'] as $province)--}}

        {{--                            <option value="{{$province['code']}}">{{$province['name']}}</option>--}}
        {{--                        @endforeach--}}
        {{--                    </select>--}}
        {{--                    @error('address.provinceCode') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}
        {{--                <div class="flex items-center hidden">--}}
        {{--                    <label for="province-code" class="block text-gray-700 text-sm font-bold mb-2 w-32">Province--}}
        {{--                        Code</label>--}}
        {{--                    --}}{{--                    DO UKRYCIA --}}
        {{--                    <input type="text" id="province-code" name="province-code" required--}}
        {{--                           class="  shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.provinceCode">--}}
        {{--                </div>--}}
        {{--                <div class="flex items-center">--}}
        {{--                    <label for="city" class="block text-gray-700 text-sm font-bold mb-2 w-32">City</label>--}}
        {{--                    <input type="text" id="city" name="city" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.city">--}}
        {{--                    @error('address.city') <span class="error">{{ $message }} Pole jest wymagane</span> @enderror--}}
        {{--                </div>--}}

        {{--                <div class="flex items-center">--}}
        {{--                    <label for="postcode" class="block text-gray-700 text-sm font-bold mb-2 w-32">Postcode</label>--}}
        {{--                    <input required type="text" id="postcode" name="postcode"--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.postcode">--}}
        {{--                    @error('address.postcode') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}

        {{--                <div class="flex items-center">--}}
        {{--                    <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2 w-32">Phone--}}
        {{--                        Number</label>--}}
        {{--                    <input type="text" id="phone-number" name="phone-number" required--}}
        {{--                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"--}}
        {{--                           wire:model.live="address.phoneNumber">--}}
        {{--                    @error('address.phoneNumber') <span class="error">{{ $message }}</span> @enderror--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </form>--}}

        <form wire:submit.prevent="submitForm">
            @csrf
            <input type="hidden" id="address-id" name="id" wire:model="address.id">
            <div class="py-4">
                <label class="text-dark-grey-2 font-light">Informacje kontaktowe</label>
                <div class="py-4 grid grid-cols-2 gap-4 w-full text-dark-grey-1 font-light">
                    <div>
                        <input id="first-name" placeholder="Imię" wire:model.live="address.firstName" required
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] invalid:border-[#E83A3A] outline-none" />
                        @error('address.firstName') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input id="last-name" placeholder="Nazwisko" wire:model.live="address.lastName" required
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] invalid:border-[#E83A3A] outline-none" />
                        @error('address.lastName') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input id="company-name" placeholder="Nazwa firmy" wire:model.live="address.company"
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] invalid:border-[#E83A3A] outline-none" />
                        @error('address.company') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input id="nip" placeholder="NIP" wire:model.live="address.nip"
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] invalid:border-[#E83A3A] outline-none" />
                        @error('address.nip') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div class=" col-span-2">
                        <input id="phone-number" placeholder="Numer telefonu" wire:model.live="address.phoneNumber"
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-[0_0_0_2px_rgba(107,148,226,0.2)] invalid:border-[#E83A3A] outline-none" />
                        @error('address.phoneNumber') <span
                            class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="py-4">
                <label class="text-dark-grey-2 font-light">Adres dostawy</label>
                <div class="py-4 grid grid-cols-4 gap-4 w-full text-dark-grey-1 font-light">
                    <div class=" col-span-2">
                        <select id="country" wire:model.live="address.countryCode" required
                                class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none">
                            <option value="" disabled selected hidden>Kraj</option>
                            @foreach($countries as $country)
                                <option value="{{$country['code']}}">{{$country['name']}}</option>
                            @endforeach
                        </select>
                        @error('address.countryName') <span
                            class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div class=" col-span-2">
                        <select id="province" wire:model.live="address.provinceCode" required
                                class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none">
                            <option value="" disabled selected hidden>Województwo</option>
                            @foreach($country['provinces'] as $province)
                                <option value="{{$province['code']}}">{{$province['name']}}</option>
                            @endforeach
                        </select>
                        @error('address.provinceName') <span
                            class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div class=" col-span-2">
                        <input id="city" placeholder="Miasto" wire:model.live="address.city" required
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none" />
                        @error('address.city') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div class=" col-span-2">
                        <input id="post-code" placeholder="Kod Pocztowy" wire:model.live="address.postcode" required
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none" />
                        @error('address.postcode') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                    </div>
                    <div class=" col-span-4 md:col-span-2">
                        <input id="street" placeholder="Ulica" wire:model.live="address.street" required
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none" />
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <input id="number-building" placeholder="Nr budynku" wire:model.live="address.buildingNumber"
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none" />
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <input id="number-apartment" placeholder="Nr mieszkania"
                               wire:model.live="address.apartmentNumber"
                               class="py-2 px-4 border border-[#E5E7EB] w-full rounded hover:border-[#BABFCA] focus:border-[#596273] focus:shadow-input invalid:border-[#E83A3A] outline-none" />
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{--        @dump($step)--}}
    {{--    <div>--}}
    {{--        <a href="{{route('admin.marketplace.cart')}}"--}}
    {{--           class="bg-gray-200 p-2 rounded">Powrót do koszyka</a>--}}
    {{--        <button wire:click="backStep()" class="bg-gray-200 p-2 rounded">Back</button>--}}

    {{--        <button wire:click="nextStep()"--}}
    {{--                class="w-full py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">--}}
    {{--            Zapisz i przejdź dalej--}}
    {{--        </button>--}}
    {{--    </div>--}}

{{--    <div class="w-full h-full content-end md:col-span-4">--}}
{{--        <div--}}
{{--            class="max-h-12 flex gap-4 justify-between">--}}
{{--            <button wire:click="backStep"--}}
{{--                    class="max-w-sm py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-dark-grey-2 text-xl font-medium xl:m-0">--}}
{{--                Poprzedni kork--}}
{{--            </button>--}}
{{--            <button wire:click="handleNextStep"--}}
{{--                    class="py-2 px-4 rounded text-xl font-medium xl:m-0 border border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59]">--}}
{{--                Zapisz i przejdź dalej--}}
{{--            </button>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>

