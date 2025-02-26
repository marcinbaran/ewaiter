<div class="h-[80%] md:h-[90%]">
    {{--    @dump($selectedMethod)--}}

    <div class="h-full flex flex-col justify-between">
        {{--        <form wire:submit.prevent="submitForm">--}}
        {{--            <p class="text-xl font-semibold mb-4">Payment Methods</p>--}}
        {{--            @error('selectedMethod.code') <span class="error">{{ $message }}</span> @enderror--}}

        {{--            @foreach ($availablePaymentMethods as $method)--}}
        {{--                <div class="bg-white shadow-md rounded-lg p-4 mb-4">--}}
        {{--                    <label class="flex items-center justify-between">--}}
        {{--                        <div>--}}
        {{--                            <p class="text-lg font-semibold">Method: {{ $method['name'] }}</p>--}}
        {{--                            <p class="text-sm text-gray-600">Code: {{ $method['code'] }}</p>--}}
        {{--                            @if(isset($method['description']))--}}
        {{--                                <p class="text-sm text-gray-600">Description: {{ $method['description'] }}</p>--}}
        {{--                            @endif--}}
        {{--                            <!-- Assuming the price is not provided in the array, this part is omitted -->--}}
        {{--                        </div>--}}
        {{--                        <input type="radio" wire:model="selectedMethod.code" value="{{ $method['code'] }}"--}}
        {{--                               name="payment_method"--}}
        {{--                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded">--}}
        {{--                    </label>--}}
        {{--                </div>--}}
        {{--            @endforeach--}}

        {{--        </form>--}}


        {{--        {{dd($availablePaymentMethods)}}--}}
        <div class="py-8">

            <h2 class="text-dark-grey-2 text-[28px]">{{__('marketplace.payment method')}}</h2>
            <form>
                <div class="flex flex-col gap-4 py-6">
                    @foreach($availablePaymentMethods as $method)
                        <div>
                        <label wire:key="$method['code']" wire:click="changeMethod('{{$method['code']}}')"
                               class="cursor-pointer flex justify-between border rounded p-4 w-full {{ $selectedMethod['code'] === $method['code'] ? 'border-[#EC3F59] shadow-button' : 'border-[#F3F4F6]  hover:border-[#BABFCA]' }}">
                            <div class="flex gap-4 px-2 py-1">
                                <div class="relative my-1 pr-4">
                                    <input type="radio"
                                           wire:model="selectedMethod.code"
                                           class="hidden"
                                           name="payment_method">
                                    <span
                                        class="top-0 p-2 rounded-full absolute {{$selectedMethod['code'] === $method['code'] ? 'bg-[#EC3F59] outline outline-offset-2 outline-[#EC3F5933]' : 'bg-white border border-[#BABFCA]'}}"></span>
                                </div>
                                <p class="text-dark-grey-2">{{__('marketplace.paymentsType.'.$method['code'])}}</p>
                            </div>
                            {{--                            {{dd($method['code'], $selectedMethodCode)}}--}}
                            {{--                            #EC3F59 <- color for selected --}}
                            <div class="px-2 py-1">
                                @if($method['code'] === 'cash_on_delivery')
                                    <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g opacity="1">
                                            <path
                                                d="M10 15C10 16.657 12.686 18 16 18C19.314 18 22 16.657 22 15M10 15C10 13.343 12.686 12 16 12C19.314 12 22 13.343 22 15M10 15V19C10 20.656 12.686 22 16 22C19.314 22 22 20.656 22 19V15M4 7C4 8.072 5.144 9.062 7 9.598C8.856 10.134 11.144 10.134 13 9.598C14.856 9.062 16 8.072 16 7C16 5.928 14.856 4.938 13 4.402C11.144 3.866 8.856 3.866 7 4.402C5.144 4.938 4 5.928 4 7ZM4 7V17C4 17.888 4.772 18.45 6 19M4 12C4 12.888 4.772 13.45 6 14"
                                                stroke="{{$selectedMethod['code'] === $method['code'] ? '#EC3F59' : '#BABFCA'}}"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </g>
                                    </svg>
                                @elseif($method['code'] === 'bank_transfer')
                                    <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M4 22H22M4 11H22M6 7L13 4L20 7M5 11V22M21 11V22M9 15V18M13 15V18M17 15V18"
                                            stroke="{{$selectedMethod['code'] === $method['code'] ? '#EC3F59' : '#BABFCA'}}"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @else
                                    <svg class="m-auto" width="26" height="26" viewBox="0 0 26 26" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2 10.7143H24M6.88889 16.4286H6.90111M11.7778 16.4286H14.2222M2 8.42857C2 7.51926 2.38631 6.64719 3.07394 6.00421C3.76158 5.36122 4.69421 5 5.66667 5H20.3333C21.3058 5 22.2384 5.36122 22.9261 6.00421C23.6137 6.64719 24 7.51926 24 8.42857V17.5714C24 18.4807 23.6137 19.3528 22.9261 19.9958C22.2384 20.6388 21.3058 21 20.3333 21H5.66667C4.69421 21 3.76158 20.6388 3.07394 19.9958C2.38631 19.3528 2 18.4807 2 17.5714V8.42857Z"
                                            stroke="{{$selectedMethod['code'] === $method['code'] ? '#EC3F59' : '#BABFCA'}}"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                            </div>
                        </label>
                            @error('selectedMethod.code') <span class="text-xs text-[#DA3407]">{{$message}}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </form>
        </div>


{{--        @dump($selectedMethod['code'])--}}
{{--        <div class="flex gap-4 justify-between">--}}
{{--            <button wire:click="backStep()"--}}
{{--                    class="max-w-sm py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">--}}
{{--                Poprzedni kork--}}
{{--            </button>--}}
{{--            <button wire:click="nextStep()"--}}
{{--                    class="max-w-sm py-2 px-4 border rounded border-[#E5E7EB] focus:border-[#EC3F59] hover:border-[#BABFCA] text-[#EC3F59] text-xl font-medium xl:m-0">--}}
{{--                Zapisz i przejdź dalej--}}
{{--            </button>--}}
{{--        </div>--}}
    </div>
</div>
