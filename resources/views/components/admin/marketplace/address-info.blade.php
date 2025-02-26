@props(['data' => '', 'countryName' => '', 'payMethod' => '', 'shipMethod' => '', 'title' => '', 'isOrderDetails' => false, 'shippingState' => ''])
<div class="pt-8">
    <div class="flex justify-between flex-col mb-4 gap-4 sm:flex-row">
        <div>
            <h2 class="font-medium text-2xl sm:text-[28px] text-dark-grey-2">{{__('marketplace.'.$title)}}</h2>
        </div>
{{--        @dd($data)--}}
        @if($isOrderDetails)
        <div class="border rounded-md border-[#2FB575] {{$shippingState === 'ready' ? ' text-[#2FB575]' : 'bg-[#2FB575] text-[#FFF]'}}  p-4 max-w-52 xl:mr-8">
            <div class="flex justify-center gap-2">
                <div>
                    <svg width="26" height="24" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4 13C4 13.5304 4.21071 14.0391 4.58579 14.4142C4.96086 14.7893 5.46957 15 6 15C6.53043 15 7.03914 14.7893 7.41421 14.4142C7.78929 14.0391 8 13.5304 8 13M4 13C4 12.4696 4.21071 11.9609 4.58579 11.5858C4.96086 11.2107 5.46957 11 6 11C6.53043 11 7.03914 11.2107 7.41421 11.5858C7.78929 11.9609 8 12.4696 8 13M4 13H2V9M8 13H14M14 13C14 13.5304 14.2107 14.0391 14.5858 14.4142C14.9609 14.7893 15.4696 15 16 15C16.5304 15 17.0391 14.7893 17.4142 14.4142C17.7893 14.0391 18 13.5304 18 13M14 13C14 12.4696 14.2107 11.9609 14.5858 11.5858C14.9609 11.2107 15.4696 11 16 11C16.5304 11 17.0391 11.2107 17.4142 11.5858C17.7893 11.9609 18 12.4696 18 13M18 13H20V7M1 1H12V13M20 7H12M20 7L17 2H12M2 5H6"
                            stroke="{{$shippingState === 'ready' ? '#2FB575' : '#FFF'}}" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p>{{$shippingState === 'ready' ? __('marketplace.during delivery') : __('marketplace.shipped')}}</p>
            </div>
        </div>
        @endif
    </div>
    <div class="pt-6">
        <div class="sm:grid sm:grid-cols-2">
            <div class="border border-[#F3F4F6] rounded-l-md border-r-0 px-6 py-8">
                <div class="w-full pb-8">
                    <h3 class="text-xl text-dark-grey-2">{{__('marketplace.contact details')}}</h3>
                </div>
                <div class="md:grid grid-rows-3 grid-cols-2 gap-2">
                    <div class="flex flex-col gap-4 xl:gap-4 xl:flex-row col-span-2">
                        <div>
                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.firstname')}}
                                :
                                <span
                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['firstName']}}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-light-grey-2 font-light">{{__('marketplace.address form.lastname')}}:
                                <span
                                    class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['lastName']}}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-span-2 py-4 xl:py-4 min-[1437px]:py-0">
                        <p class="text-light-grey-2 font-light">{{__('marketplace.address form.company')}}:
                            <span
                                class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['company']}}</span>
                        </p>
                    </div>
                    <div class="col-span-2 py-4 xl:py-4 min-[1437px]:py-0">
                        <p class="text-light-grey-2 font-light">{{__('marketplace.address form.phone')}}: <span
                                class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['phoneNumber']}}</span>
                        </p>
                    </div>
                </div>
            </div>
            {{--            TODO: buidlingNumber and apartmentNumber dodać do api parametry bo nie czyta!!! --}}
            <div class="border border-[#F3F4F6] rounded-r-md px-6 py-8">
                <div class="w-full pb-8">
                    <h3 class="text-xl text-dark-grey-2">{{__('marketplace.delivery address')}}</h3>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 xl:grid-rows-3 gap-2">
                    @isset($countryName)
                        <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.country') }}:
                            <span class="text-dark-grey-1 font-normal">{{ $countryName }}</span>
                        </p>
                    @endisset

                    @isset($data['shippingAddress']['provinceName'])
                        <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.province') }}:
                            <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['provinceName'] }}</span>
                        </p>
                    @endisset

                    @isset($data['shippingAddress']['city'])
                        <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.city') }}:
                            <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['city'] }}</span>
                        </p>
                    @endisset

                    @isset($data['shippingAddress']['postcode'])
                        <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.postcode') }}:
                            <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['postcode'] }}</span>
                        </p>
                    @endisset

                    @isset($data['shippingAddress']['street'])
                        <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.street') }}:
                            <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['street'] }}</span>
                        </p>
                    @endisset

                    <div class="flex flex-col gap-2 md:flex-row">
                        @isset($data['shippingAddress']['building'])
                            <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.building') }}:
                                <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['building'] }}</span>
                            </p>
                        @endisset

                        @isset($data['shippingAddress']['apartment'])
                            <p class="text-light-grey-2 font-light">{{ __('marketplace.address form.apartment') }}:
                                <span class="text-dark-grey-1 font-normal">{{ $data['shippingAddress']['apartment'] }}</span>
                            </p>
                        @endisset
                    </div>
                </div>

                {{--                <div class="grid grid-cols-1 xl:grid-cols-2 xl:grid-rows-3 gap-2">--}}
{{--                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.country')}}: <span--}}
{{--                            class="text-dark-grey-1 font-normal">{{$countryName}}</span>--}}
{{--                    </p>--}}
{{--                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.province')}}: <span--}}
{{--                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['provinceName']}}</span>--}}
{{--                    </p>--}}
{{--                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.city')}}: <span--}}
{{--                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['city']}}</span>--}}
{{--                    </p>--}}
{{--                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.postcode')}}: <span--}}
{{--                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['postcode']}}</span>--}}
{{--                    </p>--}}
{{--                    <p class="text-light-grey-2 font-light">{{__('marketplace.address form.street')}}: <span--}}
{{--                            class="text-dark-grey-1 font-normal">{{$data['shippingAddress']['street']}}</span>--}}
{{--                    </p>--}}
{{--                    <div class="flex flex-col gap-2 md:flex-row">--}}
{{--                        <p class="text-light-grey-2 font-light">{{__('marketplace.address form.building')}}:--}}
{{--                            <span--}}
{{--                                class="text-dark-grey-1 font-normal">21</span>--}}
{{--                        </p>--}}
{{--                        <p class="text-light-grey-2 font-light">{{__('marketplace.address form.apartment')}}:--}}
{{--                            <span--}}
{{--                                class="text-dark-grey-1 font-normal">37</span></p>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
        <div class="grid grid-rows-2 md:grid-rows-1 md:grid-cols-2 gap-4 py-4 border-b border-b-[#F3F4F6] pb-8">
            <div class="rounded border border-[#F3F4F6] flex justify-between p-4 bg-white">
                @if(is_array($shipMethod))
                <div class="text-dark-grey-2">
                    <p class="py-1">{{$shipMethod['name']}}</p>
                </div>
                <div
                    class="px-2 py-1 rounded bg-[#F3F4F6]">
                    <p class="">{{number_format($shipMethod['price'] / 100, 2, ',')}} zł</p>
                </div>
                @else
{{--                <div class="text-dark-grey-2">--}}
{{--                    @dump($shipMethod)--}}
{{--                    <p class="py-1">{{__('marketplace.'.$shipMethod['code'])}}</p>--}}
{{--                </div>--}}
                @endif
            </div>
            <div class="rounded border border-[#F3F4F6] flex justify-between p-4 bg-white">
                <div class="text-dark-grey-2">
                    <p class="py-1">{{__('marketplace.paymentsType.'.$payMethod['code'])}}</p>
                </div>
                <div class="px-2 py-1">
                    @if($payMethod['code'] === 'bank_transfer')
                        <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4 22H22M4 11H22M6 7L13 4L20 7M5 11V22M21 11V22M9 15V18M13 15V18M17 15V18"
                                stroke="#BABFCA"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @elseif($payMethod['code'] === 'cash')
                        <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 15C10 16.657 12.686 18 16 18C19.314 18 22 16.657 22 15M10 15C10 13.343 12.686 12 16 12C19.314 12 22 13.343 22 15M10 15V19C10 20.656 12.686 22 16 22C19.314 22 22 20.656 22 19V15M4 7C4 8.072 5.144 9.062 7 9.598C8.856 10.134 11.144 10.134 13 9.598C14.856 9.062 16 8.072 16 7C16 5.928 14.856 4.938 13 4.402C11.144 3.866 8.856 3.866 7 4.402C5.144 4.938 4 5.928 4 7ZM4 7V17C4 17.888 4.772 18.45 6 19M4 12C4 12.888 4.772 13.45 6 14"
                                stroke="#BABFCA"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
<div>
