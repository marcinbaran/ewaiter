@props(['products' => '', 'totalPrice' => 0, 'shippingPrice' => 0, 'productsPrice' => 0])
<div class="">
    <div class="border-b border-[#F3F4F6] py-4">
        <div class="w-full hidden md:block">
            <div
                class="grid grid-cols-3 text-sm font-light text-light-grey-2 text-right w-1/3 float-right py-4 gap-12">
                <div>
                    <p>{{__('marketplace.unit price')}}</p>
                </div>
                <div>
                    <p>{{__('marketplace.quantity')}}</p>
                </div>
                <div>
                    <p>{{__('marketplace.full price')}}</p>
                </div>
            </div>
        </div>
        <div class="grid w-full">
            @foreach($products as $product)
                <div class="flex justify-between">
                    <div class="flex py-4 gap-4 w-full">
                        <div class="w-full min-w-[120px] max-w-[120px] h-full md:max-w-[80px] md:min-w-[80px]">
                            <img class="rounded min-w-[120px] max-w-[120px] md:max-w-[80px] md:min-w-[80px]"
                                 src="{{$product['image']}}"
                                 alt="{{$product['productName']}}" />
                        </div>
                        <div class="flex flex-col gap-1 py-2 justify-between px-2 w-full md:py-4 md:gap-4">
                            <div class="">
                                <h2 class="text-dark-grey-2 text-xl font-medium">{{$product['productName']}}</h2>
                            </div>
                            <div class="text-dark-grey-1 text-sm font-light">
                                <p>{{__('marketplace.size')}}: <span
                                        class="font-normal text-dark-grey-2">{{$product['variant']['name']}}</span></p>
                                <p>{{__('marketplace.quantity')}}:
                                    <span
                                        class="font-normal text-dark-grey-2">50</span></p>
                            </div>
                            <div class="flex justify-between text-xl text-dark-grey-1 md:hidden">
                                <div>
                                    <p>x <span>{{$product['quantity']}}</span></p>
                                </div>
                                <div>
                                    <p>{{number_format($product['subtotal'] / 100, 2, ',')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="hidden h-full py-4 relative md:grid md:grid-cols-3 w-1/2 text-right gap-12 self-center text-xl text-dark-grey-1 md:py-0 md:h-auto">
                        <div class="">
                            <p class="text-nowrap">{{number_format($product['unitPrice'] / 100, 2, ',')}} zł</p>
                        </div>
                        <div>
                            <p>x<span>{{$product['quantity']}}</span></p>
                        </div>
                        <div class="text-nowrap">
                            <p>{{number_format($product['subtotal'] / 100, 2, ',')}} zł</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="py-4">
        <div class="py-2 text-dark-grey-2 border-b border-[#F3F4F6]">
            <div class="flex justify-between py-1">
                <p class="font-light text-light-grey-2">{{__('marketplace.products cost')}}</p>
                @if(is_array($productsPrice))
                                    <p class=" text-nowrap">{{ number_format(array_sum(array_column($productsPrice, 'total'))/ 100, 2, ',', ' ') }} zł</p>
                @else
                    <p class=" text-nowrap">{{ number_format($productsPrice / 100, 2, ',', ' ') }} zł</p>
                @endif
{{--                <p class=" text-nowrap">{{ number_format($productsPrice / 100, 2, ',', ' ') }} zł</p>--}}
            </div>
            <div class="flex justify-between py-1">
                <p class="font-light text-light-grey-2">{{__('marketplace.delivery cost')}}</p>
                <p class=" text-nowrap">{{number_format($shippingPrice / 100, 2, ',')}} zł</p>
            </div>
{{--            <div class="flex justify-between py-1">--}}
{{--                <p class="font-light">{{__('marketplace.discount')}}</p>--}}
{{--                <p>-12,00 zł</p>--}}
{{--            </div>--}}
        </div>
        <div class="flex justify-end font-medium text-xl text-dark-grey-2 mt-2 py-2 mb-4 gap-4">
            <p>{{__('marketplace.total price')}}</p>
            <p class="font-medium text-[#EC3F59]">{{number_format($totalPrice / 100, 2, ',')}} zł</p>
        </div>
    </div>
</div>
