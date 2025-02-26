@props(['id' => '' ,'name' => '', 'image' => '', 'price' => '', 'quantity' => '', 'size' => ''])
<div class="px-4 pt-6">
    <div class="flex gap-4 border-b border-[#F3F4F6] pb-6">
        <div class="max-w-[80px] max-h-[140px]">
            <img class="w-full h-full rounded" src="{{$image}}" alt="{{$name}}" />
        </div>
        <div class="w-full">
            <div class="flex flex-col justify-between px-2">
                <div class="">
                    <h2 class="text-dark-grey-2 text-xl font-medium">{{$name}}</h2>
                </div>
                <div>
                    <p class="font-light text-dark-grey-1">{{__('marketplace.size')}}: <span
                            class="font-normal text-dark-grey-2">{{$size}}</span></p>
                    <p class="font-light text-dark-grey-1">{{__('marketplace.quantity')}}: <span
                            class="font-normal text-dark-grey-2">50</span></p>
                </div>
            </div>
            <div class="w-full flex gap-4 justify-between px-2">
                <div class="my-auto">
                    <p class="text-[#EC3F59]">{{$price}}</p>
                </div>
                <div>

                    <livewire:marketplace.add-sub-button :id="(int)$id" :isBackground="false" :quantity="$quantity"
                                                         :isInCart="true" />
                </div>
            </div>
        </div>
{{--        <livewire:marketplace.cart-item-content :id="$id" :name="$name" :price="$price" :quantity="$quantity" :size="$size"/>--}}
    </div>
</div>
