<div>
    <div>
        {{--    <div>--}}
        {{--        Summary--}}
        {{--    </div>--}}

        {{--    <div class="max-w-4xl mx-auto p-5 mt-10 bg-white shadow-md rounded-lg">--}}
        {{--        <h1 class="text-2xl font-bold mb-5">Order Summary</h1>--}}
        {{--        @dd($data)--}}
        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Shipping Address</h2>--}}
        {{--            <p><strong>Street:</strong> {{ $data['shippingAddress']['street'] }}</p>--}}
        {{--            <p><strong>City:</strong> {{ $data['shippingAddress']['city'] }}</p>--}}
        {{--            <p><strong>Province:</strong> {{ $data['shippingAddress']['provinceName'] }}</p>--}}
        {{--            <p><strong>Postcode:</strong> {{ $data['shippingAddress']['postcode'] }}</p>--}}
        {{--            <p><strong>Country:</strong> {{ $data['shippingAddress']['countryCode'] }}</p>--}}
        {{--            <p><strong>Phone Number:</strong> {{ $data['shippingAddress']['phoneNumber'] }}</p>--}}
        {{--        </div>--}}

        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Billing Address</h2>--}}
        {{--            <p><strong>Street:</strong> {{ $data['billingAddress']['street'] }}</p>--}}
        {{--            <p><strong>City:</strong> {{ $data['billingAddress']['city'] }}</p>--}}
        {{--            <p><strong>Province:</strong> {{ $data['billingAddress']['provinceName'] }}</p>--}}
        {{--            <p><strong>Postcode:</strong> {{ $data['billingAddress']['postcode'] }}</p>--}}
        {{--            <p><strong>Country:</strong> {{ $data['billingAddress']['countryCode'] }}</p>--}}
        {{--            <p><strong>Phone Number:</strong> {{ $data['billingAddress']['phoneNumber'] }}</p>--}}
        {{--        </div>--}}

        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Items</h2>--}}
        {{--            @foreach ($data['items'] as $item)--}}
        {{--                <div class="mb-3 p-3 bg-gray-50 rounded-lg">--}}
        {{--                    <p><strong>Product Name:</strong> {{ $item['productName'] }}</p>--}}
        {{--                    <p><strong>Quantity:</strong> {{ $item['quantity'] }}</p>--}}
        {{--                    <p><strong>Unit Price:</strong> {{ number_format($item['unitPrice'] / 100, 2) }} Zł</p>--}}
        {{--                    <p><strong>Total:</strong> {{ number_format($item['total'] / 100, 2) }} Zł</p>--}}
        {{--                </div>--}}
        {{--            @endforeach--}}
        {{--        </div>--}}

        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Payment</h2>--}}
        {{--            <p><strong>Payment Method:</strong> {{ $data['payments'][0]['method'] }}</p>--}}
        {{--            <p><strong>Payment State:</strong> {{ $data['paymentState'] }}</p>--}}
        {{--        </div>--}}

        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Shipment</h2>--}}
        {{--            <p><strong>Shipping Method:</strong> {{ $data['shipments'][0]['method'] }}</p>--}}
        {{--            <p><strong>Shipping State:</strong> {{ $data['shippingState'] }}</p>--}}
        {{--        </div>--}}

        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Totals</h2>--}}
        {{--            <p><strong>Items Total:</strong> ${{ number_format($data['itemsTotal'] / 100, 2) }}</p>--}}
        {{--            <p><strong>Total:</strong> ${{ number_format($data['total'] / 100, 2) }}</p>--}}
        {{--        </div>--}}
        {{--        <div class="mb-5">--}}
        {{--            <h2 class="text-xl font-semibold">Additional Information</h2>--}}
        {{--            <textarea wire:model="notes" class="w-full p-2 border rounded-lg" rows="5"--}}
        {{--                      placeholder="Enter any additional information here..."></textarea>--}}
        {{--        </div>--}}
        {{--    </div>--}}

        {{--        <div class="pt-8">--}}
        {{--            <h2 class="font-medium text-[28px] text-dark-grey-2">{{__('marketplace.order and pay')}}</h2>--}}
        {{--            <div class="pt-6">--}}
        {{--                <div class="sm:grid sm:grid-cols-2">--}}
        {{--                    <div class="border border-[#F3F4F6] rounded-l-md border-r-0 px-6 py-8">--}}
        {{--                        <div class="w-full pb-8">--}}
        {{--                            <h3 class="text-xl text-dark-grey-2">{{__('marketplace.contact details')}}</h3>--}}
        {{--                        </div>--}}
        {{--                        <div class="md:grid grid-rows-3 grid-cols-2 gap-2">--}}
        {{--                            <div class="flex flex-col gap-2 xl:gap-4 xl:flex-row col-span-2">--}}
        {{--                                <div>--}}
        {{--                                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.firstname')}}--}}
        {{--                                        :--}}
        {{--                                        <span--}}
        {{--                                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['firstName']}}</span>--}}
        {{--                                    </p>--}}
        {{--                                </div>--}}
        {{--                                <div>--}}
        {{--                                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.lastname')}}:--}}
        {{--                                        <span--}}
        {{--                                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['lastName']}}</span>--}}
        {{--                                    </p>--}}
        {{--                                </div>--}}
        {{--                            </div>--}}
        {{--                            <div class="col-span-2 py-2 xl:py-4">--}}
        {{--                                <p class="text-light-grey-2 font-light">{{__('marketplace.address form.company')}}:--}}
        {{--                                    <span--}}
        {{--                                        class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['company']}}</span>--}}
        {{--                                </p>--}}
        {{--                            </div>--}}
        {{--                            <div class="col-span-2">--}}
        {{--                                <p class="text-light-grey-2 font-light">{{__('marketplace.address form.phone')}}: <span--}}
        {{--                                        class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['phoneNumber']}}</span>--}}
        {{--                                </p>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="border border-[#F3F4F6] rounded-r-md px-6 py-8">--}}
        {{--                        <div class="w-full pb-8">--}}
        {{--                            <h3 class="text-xl text-dark-grey-2">{{__('marketplace.delivery address')}}</h3>--}}
        {{--                        </div>--}}
        {{--                        <div class="grid grid-cols-1 xl:grid-cols-2 xl:grid-rows-3 gap-2">--}}
        {{--                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.country')}}: <span--}}
        {{--                                    class="text-dark-grey-1 font-normal">{{$coutryName}}</span>--}}
        {{--                            </p>--}}
        {{--                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.province')}}: <span--}}
        {{--                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['provinceName']}}</span>--}}
        {{--                            </p>--}}
        {{--                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.city')}}: <span--}}
        {{--                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['city']}}</span>--}}
        {{--                            </p>--}}
        {{--                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.postcode')}}: <span--}}
        {{--                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['postcode']}}</span>--}}
        {{--                            </p>--}}
        {{--                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.street')}}: <span--}}
        {{--                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['street']}}</span>--}}
        {{--                            </p>--}}
        {{--                            <div class="flex flex-col gap-2 md:flex-row">--}}
        {{--                                <p class="text-light-grey-2 font-light">{{__('marketplace.address form.building')}}:--}}
        {{--                                    <span--}}
        {{--                                        class="text-dark-grey-1 font-normal">21</span>--}}
        {{--                                </p>--}}
        {{--                                <p class="text-light-grey-2 font-light">{{__('marketplace.address form.apartment')}}:--}}
        {{--                                    <span--}}
        {{--                                        class="text-dark-grey-1 font-normal">37</span></p>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--                <div class="grid grid-rows-2 md:grid-cols-2 gap-4 py-4 border-b border-b-[#F3F4F6] pb-8">--}}
        {{--                    <div class="rounded border border-[#F3F4F6] flex justify-between p-4 bg-white">--}}
        {{--                        <div class="text-dark-grey-2">--}}
        {{--                            <p class="py-1">{{$shipMethod['name']}}</p>--}}
        {{--                        </div>--}}
        {{--                        <div--}}
        {{--                            class="px-2 py-1 rounded bg-[#F3F4F6]">--}}
        {{--                            <p class="">{{number_format($shipMethod['price'] / 100, 2, ',')}} zł</p>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="rounded border border-[#F3F4F6] flex justify-between p-4 bg-white">--}}
        {{--                        <div class="text-dark-grey-2">--}}
        {{--                            <p class="py-1">{{__('marketplace.'.$payMethod['code'])}}</p>--}}
        {{--                        </div>--}}
        {{--                        <div class="px-2 py-1">--}}
        {{--                            @if($payMethod['code'] === 'bank_transfer')--}}
        {{--                                <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"--}}
        {{--                                     xmlns="http://www.w3.org/2000/svg">--}}
        {{--                                    <path--}}
        {{--                                        d="M4 22H22M4 11H22M6 7L13 4L20 7M5 11V22M21 11V22M9 15V18M13 15V18M17 15V18"--}}
        {{--                                        stroke="#BABFCA"--}}
        {{--                                        stroke-linecap="round" stroke-linejoin="round" />--}}
        {{--                                </svg>--}}
        {{--                            @elseif($payMethod['code'] === 'cash_on_delivery')--}}
        {{--                                <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"--}}
        {{--                                     xmlns="http://www.w3.org/2000/svg">--}}
        {{--                                    <path--}}
        {{--                                        d="M10 15C10 16.657 12.686 18 16 18C19.314 18 22 16.657 22 15M10 15C10 13.343 12.686 12 16 12C19.314 12 22 13.343 22 15M10 15V19C10 20.656 12.686 22 16 22C19.314 22 22 20.656 22 19V15M4 7C4 8.072 5.144 9.062 7 9.598C8.856 10.134 11.144 10.134 13 9.598C14.856 9.062 16 8.072 16 7C16 5.928 14.856 4.938 13 4.402C11.144 3.866 8.856 3.866 7 4.402C5.144 4.938 4 5.928 4 7ZM4 7V17C4 17.888 4.772 18.45 6 19M4 12C4 12.888 4.772 13.45 6 14"--}}
        {{--                                        stroke="#BABFCA"--}}
        {{--                                        stroke-linecap="round" stroke-linejoin="round" />--}}
        {{--                                </svg>--}}
        {{--                            @endif--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <x-admin.marketplace.address-info :data="$data" :countryName="$countryName" :shipMethod="$shipMethod"
                                          :payMethod="$payMethod" title="order and pay" />
        <div>
            <!-- cart summary -->
{{--            @dd($products)--}}
                <x-admin.marketplace.cart-summary :products="$products" :totalPrice="$data['total']" :productsPrice="$data['itemsTotal']" :shippingPrice="$data['shippingTotal']" />

            <!-- textarea -->
            <div class="relative w-full h-36 text-light-grey-2 font-light mb-4">
                    <textarea wire:model="notes" class="w-full h-full px-10 py-4 border border-[#F3F4F6] rounded-md"
                              placeholder="Napisz komentarz..."></textarea>
                <svg class="absolute top-5 left-4" width="16" height="16" viewBox="0 0 16 16" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.38889 2.47718L11.5 5.70888M10.3333 12.5762H15M12.6667 10.1525V15M1 13.3842H4.11111L12.2778 4.901C12.4821 4.6888 12.6441 4.43689 12.7547 4.15964C12.8652 3.8824 12.9221 3.58524 12.9221 3.28515C12.9221 2.98506 12.8652 2.68791 12.7547 2.41066C12.6441 2.13342 12.4821 1.8815 12.2778 1.66931C12.0735 1.45711 11.831 1.28879 11.5641 1.17395C11.2972 1.05911 11.0111 1 10.7222 1C10.4333 1 10.1473 1.05911 9.88036 1.17395C9.61346 1.28879 9.37095 1.45711 9.16667 1.66931L1 10.1525V13.3842Z"
                        stroke="#979FAF" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        {{--    @dump($step)--}}
        {{--    <div>--}}
        {{--        <button wire:click="backStep()" class="bg-gray-200 p-2 rounded">Back</button>--}}
        {{--        --}}{{--        <button wire:click="nextStep()" class="bg-gray-200 p-2 rounded">Confirm</button>--}}
        {{--        <form action="{{ route('admin.marketplace.order_history_order_details', ['orderId' => $data['id']]) }}"--}}
        {{--              wire:submit.prevent="confirmCheckout">--}}
        {{--            @csrf--}}
        {{--            <input type="hidden" name="order" value="{{ $data['tokenValue'] }}">--}}
        {{--            <button type="submit"--}}
        {{--                    class="bg-gray-200 p-2 rounded">--}}
        {{--                Confirm--}}
        {{--            </button>--}}
        {{--        </form>--}}
        {{--    </div>--}}
        {{--    <div class="flex gap-4 justify-between">--}}
        {{--        <button wire:click="backStep()"--}}
        {{--                class="max-w-sm py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">--}}
        {{--            Poprzedni kork--}}
        {{--        </button>--}}
        {{--        <button wire:click="nextStep()"--}}
        {{--                class="max-w-sm py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">--}}
        {{--            Zamów i zapłać--}}
        {{--        </button>--}}
        {{--    </div>--}}
    </div>
</div>
