@props( ["code" => "", "id" => "", "images" => [], "name" => "", "variant" => "", "price" => "", 'shortDescription' => '', 'description' => ''])
<div
    class="items-center border border-[#F3F4F6] relative rounded-md hover:border-red-500 transition-transform duration-300 hover:-translate-y-1 h-max-80">
    <div class="p-2 gap-x-2 md:gap-x-0"
         x-bind:class="{ 'product' : isTile, 'grid grid-cols-6 grid-row-2' : !isTile}">
        {{--        <div class="flex w-full row-span-2 justify-between">--}}
        <a href="{{route('admin.marketplace.product', ['code' => $code])}}" class="sm:py-2 m-auto max-w-[120px]"
           x-bind:class="{ 'row-span-2' : !isTile }">
            @if(!empty($images))
                <img src="{{$images[0]}}" alt="{{$name}}" class="rounded border border-[#E5E7EB] m-auto w-full" />
            @endif
        </a>
        <a href="{{route('admin.marketplace.product', ['code' => $code])}}"
           class="col-start-2 col-span-full flex flex-col px-2 justify-between my-auto h-full sm:py-2">
            <div class="" x-bind:class="{ 'flex justify-between pr-4' : !isTile }">
                <p class="text-xl font-medium text-dark-grey-2">{{$name}}</p>
                <p class="text-[#EC3F59] my-auto">{{number_format($price / 100, 2, ',')}} zł</p>
            </div>
            <div>
                {{-- TODO: quantity --}}
                <div x-data="{ variant: '{{$variant->name}}', isVariant: true }" x-init="isVariant = variant !== ''"
                     x-bind:class="{ 'flex gap-4' : !isTile }">
                    <p x-show="isVariant"
                        class="text-dark-grey-1 text-sm font-light">{{__('marketplace.size')}}:
                        <strong>{{$variant->name}}</strong>
                    </p>
                    <p class="text-dark-grey-1 text-sm font-light">{{__('marketplace.quantity')}}: <strong>50</strong>
                    </p>
                </div>
                {{-- TODO: '...' at the end of text rest is hidden --}}
                <p class="text-light-grey-2 text-sm font-light hidden overflow-hidden leading-4 max-h-8 sm:block"
                   x-show="!isTile">{{$description}}</p>
                <p class="text-light-grey-2 text-sm font-light hidden overflow-hidden leading-4 max-h-8 sm:block"
                   x-show="isTile">{{$shortDescription}}</p>
            </div>
        </a>
        <div
            class="row-start-2 mx-auto sm:w-3/4 xl:w-full 2xl:w-3/4"
            x-bind:class="{ 'col-start-2' : !isTile }">
            <livewire:marketplace.add-sub-button :id="$id" />
        </div>

        {{--                blade component button --}}
        {{--                <x-admin.marketplace.button class="row-start-2 my-auto" wire:click="addToCart({{$code}}, {{$quantity}})--}}
        {{--                "--}}
        {{--                x-on:click="console.log('working')"--}}
        {{--                                            x-bind:class="{ 'col-start-5 col-span-2 mr-4' : !isTile }">--}}
        {{--                    <p>{{__('marketplace.add to cart')}}</p>--}}
        {{--                </x-admin.marketplace.button>--}}

        <div class="row-start-2 mx-2" x-bind:class="{ 'col-start-5 col-span-2 mr-4' : !isTile}">
            <livewire:marketplace.add-to-cart :variantCode="$variant->code" :id="$id" />
        </div>
        {{--        </div>--}}
    </div>
</div>
