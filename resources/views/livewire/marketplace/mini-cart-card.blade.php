<div id="miniCart" class="md:col-span-1  sticky top-0 h-[80vh]" wire:init="loadMiniCart">
    <div x-data="{ count: '{{count($miniCart)}}', isEmpty: true }" x-init="isEmpty = count === 0"
         class="py-4 px-6 h-[80vh] duration-100 ease-in-out">
        {{--        {{$this->removeAllItemsFromCart()}}--}}
        <div class="px-4 pb-4">
            <h2 class="text-dark-grey-2 text-2xl font-medium">{{__('marketplace.your cart')}}</h2>
            <p x-show="!isEmpty" class="text-dark-grey-1 text-xl font-light ">{{__('marketplace.number of items')}}
                : {{count($miniCart)}}</p>
        </div>
        @if(count($miniCart) === 0)
            <div class="font-light text-center h-4/5 flex px-4">
                <div class="m-auto">
                    <div>
                        <svg class="m-auto" width="120" height="120" viewBox="0 0 120 120" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#F3F4F6" />
                            <path
                                d="M39 74.2222C40.8565 74.2222 42.637 74.948 43.9497 76.2399C45.2625 77.5319 46 79.2841 46 81.1111C46 82.9382 45.2625 84.6904 43.9497 85.9823C42.637 87.2742 40.8565 88 39 88C37.1435 88 35.363 87.2742 34.0503 85.9823C32.7375 84.6904 32 82.9382 32 81.1111C32 79.2841 32.7375 77.5319 34.0503 76.2399C35.363 74.948 37.1435 74.2222 39 74.2222ZM39 74.2222H77.5M39 74.2222V32H32M77.5 74.2222C79.3565 74.2222 81.137 74.948 82.4497 76.2399C83.7625 77.5319 84.5 79.2841 84.5 81.1111C84.5 82.9382 83.7625 84.6904 82.4497 85.9823C81.137 87.2742 79.3565 88 77.5 88C75.6435 88 73.863 87.2742 72.5503 85.9823C71.2375 84.6904 70.5 82.9382 70.5 81.1111C70.5 79.2841 71.2375 77.5319 72.5503 76.2399C73.863 74.948 75.6435 74.2222 77.5 74.2222ZM39 38.4444L88 41.8889L84.5 66H39"
                                stroke="#596273" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17 88H87" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                            <path d="M101 88H91" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                            <path d="M74 66L76.5 41.5" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                            <path d="M50 66V39.5" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                            <path d="M62 66L63 41" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                            <path d="M39 53L86 55" stroke="#596273" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="p-4">
                        <h2 class="text-dark-grey-2 text-xl">Twój koszyk czeka na zakupy</h2>
                        <p class="text-dark-grey-1">Znajdź coś fajnego!</p>
                    </div>
                </div>
            </div>
        @else
            {{--                                                                                    {{dd($miniCart, $miniCartItemsWithImg, $miniCartItems)}}--}}
            <div class="flex flex-col justify-between h-[90%]">
                <div class="overflow-y-auto">
                    @foreach($miniCartItems as $cartItem)
                        <div wire:key="$cartItem-{{$cartItem->id}}" class="px-4 pt-6">
                            <div class="flex gap-4 border-b border-[#F3F4F6] pb-6">
                                <div class="max-w-[80px] max-h-[140px]">
                                    <img class="w-full h-full rounded" src="{{$cartItem->image}}"
                                         alt="{{$cartItem->productName}}" />
                                </div>
                                <div class="w-full">
                                    <div class="flex flex-col justify-between px-2">
                                        <div class="">
                                            <h2 class="text-dark-grey-2 text-xl font-medium">{{$cartItem->productName}}</h2>
                                        </div>
                                        <div>
                                            <p class="font-light text-dark-grey-1">{{__('marketplace.size')}}: <span
                                                    class="font-normal text-dark-grey-2">{{$cartItem->size}}</span></p>
                                            <p class="font-light text-dark-grey-1">{{__('marketplace.quantity')}}: <span
                                                    class="font-normal text-dark-grey-2">50</span></p>
                                        </div>
                                    </div>
                                    <div class="w-full flex gap-4 justify-between px-2">
                                        <div class="my-auto">
                                            <p class="text-[#EC3F59]">{{$cartItem->subtotal}} zł</p>
                                        </div>
                                        <div>
                                            {{--                                            <livewire:marketplace.add-sub-button wire:key="$cartItem->id"--}}
                                            {{--                                                                                 :id="(int)$cartItem->id"--}}
                                            {{--                                                                                 :isBackground="false"--}}
                                            {{--                                                                                 :quantity="$cartItem->quantity"--}}
                                            {{--                                                                                 :isInCart="true" />--}}
                                            <div
                                                class="grid grid-cols-3 my-auto scale-[0.80] lg:scale-100 gap-1 w-full">
                                                <svg wire:click="decreaseQuantity({{$cartItem->id}})"
                                                     class="cursor-pointer m-auto"
                                                     width="32" height="32" viewBox="0 0 24 24"
                                                     fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="23.5" y="23.5" width="23" height="23" rx="11.5"
                                                          transform="rotate(180 23.5 23.5)"
                                                          stroke="#E5E7EB" />
                                                    <path d="M7 12H17" stroke="#596273" stroke-width="2"
                                                          stroke-linecap="round" />
                                                </svg>
                                                <div class="p-2 text-center">
                                                    <span>{{ $cartItem->quantity }}</span>
                                                </div>
                                                <svg wire:click="increaseQuantity({{$cartItem->id}})"
                                                     class="cursor-pointer m-auto"
                                                     width="32" height="32" viewBox="0 0 24 24"
                                                     fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="23.5" y="23.5" width="23" height="23" rx="11.5"
                                                          transform="rotate(180 23.5 23.5)"
                                                          stroke="#E5E7EB" />
                                                    <path d="M7 12H17" stroke="#596273" stroke-width="2"
                                                          stroke-linecap="round" />
                                                    <path d="M12 7L12 17" stroke="#596273" stroke-width="2"
                                                          stroke-linecap="round" />
                                                </svg>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--                                                <x-admin.marketplace.cart-item--}}
                        {{--                                                    wire:key="$cartItem-{{$cartItem->id}}"--}}
                        {{--                                                    :name="$cartItem->productName"--}}
                        {{--                                                    :image="$cartItem->image"--}}
                        {{--                                                    :totalPrice="$cartItem->subtotal"--}}
                        {{--                                                    :size="$cartItem->size"--}}
                        {{--                                                    :quantity="$cartItem->quantity"--}}
                        {{--                                                    :id="$cartItem->id" />--}}
                        {{--                                                    wire:key="$cartItem['id']"--}}
                        {{--                                                    :name="$cartItem['productName']"--}}
                        {{--                                                    :image="$cartItem['image']"--}}
                        {{--                                                    :price="$cartItem['totalPrice']"--}}
                        {{--                                                    :size="$cartItem['size']"--}}
                        {{--                                                    :quantity="$cartItem['quantity']"--}}
                        {{--                                                    :id="$cartItem['id']" />--}}
                        {{--                        <div wire:key="$cartItem-{{$cartItem->id}}">--}}
                        {{--                            <livewire:marketplace.mini-cart-item--}}
                        {{--                                :name="$cartItem->productName"--}}
                        {{--                                :image="$cartItem->image"--}}
                        {{--                                :totalPrice="$cartItem->subtotal"--}}
                        {{--                                :size="$cartItem->size"--}}
                        {{--                                :quantity="$cartItem->quantity"--}}
                        {{--                                :id="$cartItem->id" />--}}
                    @endforeach
                </div>
                <div class="px-4">
                    {{--                    <livewire:marketplace.mini-cart-summary :totalPrice="$this->getSummary()" />--}}

                    <div>
{{--                        <div class="py-2 text-dark-grey-2 border-b border-[#F3F4F6]">--}}
{{--                            <div class="flex justify-between">--}}
{{--                                <p class="font-light text-light-grey-2">{{__('marketplace.products cost')}}</p>--}}
{{--                                <p>20,12 zł</p>--}}
{{--                            </div>--}}
{{--                            <div class="flex justify-between">--}}
{{--                                <p class="font-light text-light-grey-2">{{__('marketplace.delivery cost')}}</p>--}}
{{--                                <p>12,10 zł</p>--}}
{{--                            </div>--}}
{{--                            <div class="flex justify-between">--}}
{{--                                <p class="font-light">{{__('marketplace.discount')}}</p>--}}
{{--                                <p>-12,00 zł</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="flex justify-between text-xl text-dark-grey-2 mt-2 py-2 mb-4">
                            <p>{{__('marketplace.total price')}}</p>
                            <p class="font-medium text-[#EC3F59]">{{number_format($this->getSummary() / 100, 2, ',')}}
                                zł</p>
                        </div>
                    </div>


                    <div class="flex gap-2">

                        {{--                        <livewire:marketplace.button wire:click="removeAllItemsFromCart"--}}
                        {{--                                                     class="cursor-pointer text-dark-grey-2 rounded border border-[#E5E7EB] text-center py-2 px-4 hover:border-[#BABFCA] focus:border-[#EC3F59] "--}}
                        {{--                                                                             icon='<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
                        {{--                                                                             <path d="M1 5.44444H19M2.125 5.44444L3.25 18.7778C3.25 19.3671 3.48705 19.9324 3.90901 20.3491C4.33097 20.7659 4.90326 21 5.5 21H14.5C15.0967 21 15.669 20.7659 16.091 20.3491C16.5129 19.9324 16.75 19.3671 16.75 18.7778L17.875 5.44444M6.625 5.44444V2.11111C6.625 1.81643 6.74353 1.53381 6.95451 1.32544C7.16548 1.11706 7.45163 1 7.75 1H12.25C12.5484 1 12.8345 1.11706 13.0455 1.32544C13.2565 1.53381 13.375 1.81643 13.375 2.11111V5.44444M7.75 11L12.25 15.4444M12.25 11L7.75 15.4444" stroke="#EC3F59" stroke-linecap="round" stroke-linejoin="round"/>--}}
                        {{--                                                                             </svg>'--}}
                        {{--                        />--}}
                        <div class="{{$isCheckout ? 'w-full' : ''}}">
                            <button type="button" wire:click="removeAllItemsFromCart"
                                    class="{{$isCheckout ? 'w-full' : ''}} text-dark-grey-2 rounded border border-[#E5E7EB] text-center py-2 px-4 hover:border-[#BABFCA] focus:border-[#EC3F59]">
                                <svg class="mx-auto" width="20" height="22" viewBox="0 0 20 22" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1 5.44444H19M2.125 5.44444L3.25 18.7778C3.25 19.3671 3.48705 19.9324 3.90901 20.3491C4.33097 20.7659 4.90326 21 5.5 21H14.5C15.0967 21 15.669 20.7659 16.091 20.3491C16.5129 19.9324 16.75 19.3671 16.75 18.7778L17.875 5.44444M6.625 5.44444V2.11111C6.625 1.81643 6.74353 1.53381 6.95451 1.32544C7.16548 1.11706 7.45163 1 7.75 1H12.25C12.5484 1 12.8345 1.11706 13.0455 1.32544C13.2565 1.53381 13.375 1.81643 13.375 2.11111V5.44444M7.75 11L12.25 15.4444M12.25 11L7.75 15.4444"
                                        stroke="#EC3F59" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        @if(!$isCheckout)
                            <x-admin.marketplace.cart-card />
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
