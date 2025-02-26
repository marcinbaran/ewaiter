<div class="h-[80%] md:h-[90%]">
    {{--    @dump($selectedMethod)--}}
    {{--    <div class="p-4">--}}
    {{--        <form wire:submit.prevent="submitForm">--}}
{{--                @error('selectedMethod.code') <span class="error">{{ $message }}</span> @enderror--}}
{{--                <p class="text-xl font-semibold mb-4">Shipping Methods</p>--}}
    {{--            @foreach ($availableShippingMethods as $method)--}}
    {{--                --}}{{--                @dump($method, $method['code'])--}}
    {{--                <div class="bg-white shadow-md rounded-lg p-4 mb-4">--}}
    {{--                    <label class="flex items-center justify-between">--}}
    {{--                        <div>--}}
    {{--                            <p class="text-lg font-semibold"> {{ $method['name'] }}</p>--}}
    {{--                            <p class="text-sm text-gray-600">3,50 zł</p>--}}
    {{--                        </div>--}}
    {{--                        <input type="radio" wire:model="selectedMethod.code" value="{{$method['code']}}"--}}
    {{--                               name="shipping_method"--}}
    {{--                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded">--}}

    {{--                    </label>--}}
    {{--                </div>--}}
    {{--            @endforeach--}}

    {{--        </form>--}}
    {{--    </div>--}}
    <div class="h-full flex flex-col justify-between">
        <div class="py-8">
            <h2 class="text-dark-grey-2 text-[28px]">{{__('marketplace.delivery method')}}</h2>
            <form>
                <div class="flex flex-col gap-4 py-6">
{{--                                        {{dd($availableShippingMethods)}}--}}
                    @foreach($availableShippingMethods as $method)
                        <div>
                            <label wire:key="$method['code']" wire:click="changeMethod('{{$method['code']}}', '{{$method['id']}}')"
                                   class="cursor-pointer flex justify-between border rounded p-4 w-full  {{ $selectedMethod['code'] === $method['code'] ? 'border-[#EC3F59] shadow-button' : 'border-[#F3F4F6]  hover:border-[#BABFCA]' }}">
                                <div class="flex gap-4 px-2 py-1">
                                    <div class="relative my-1 pr-4">
                                        <input type="radio"
                                               wire:model="selectedMethod.code"
                                               class="hidden"
                                               name="shipping_method">
                                        <span
                                            class="top-0 p-2 rounded-full absolute {{$selectedMethod['code'] === $method['code'] ? 'bg-[#EC3F59] outline outline-offset-2 outline-[#EC3F5933]' : 'bg-white border border-[#BABFCA]'}}"></span>
                                    </div>
                                    <p class="text-dark-grey-2">{{$method['name']}}</p>
                                </div>
                                <div
                                    class="px-2 py-1 rounded {{$selectedMethod['code'] === $method['code'] ? 'bg-[#EC3F59]/20' : 'bg-[#F3F4F6]'}}">
                                    <p class="">{{number_format($method['price'] / 100, 2, ',')}} zł</p>
                                </div>
                            </label>
                            @error('selectedMethod.code') <span class="text-xs text-[#DA3407]">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </form>
        </div>

        {{--        @dump($step)--}}
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
