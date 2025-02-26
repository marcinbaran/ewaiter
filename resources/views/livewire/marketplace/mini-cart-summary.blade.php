<div>
    <div class="py-2 text-dark-grey-2 border-b border-[#F3F4F6]">
        <div class="flex justify-between">
            <p class="font-light text-light-grey-2">{{__('marketplace.products cost')}}</p>
            <p>20,12 zł</p>
        </div>
        <div class="flex justify-between">
            <p class="font-light text-light-grey-2">{{__('marketplace.delivery cost')}}</p>
            <p>12,10 zł</p>
        </div>
        <div class="flex justify-between">
            <p class="font-light">{{__('marketplace.discount')}}</p>
            <p>-12,00 zł</p>
        </div>
    </div>
    <div class="flex justify-between text-xl text-dark-grey-2 mt-2 py-2 mb-4">
        <p>{{__('marketplace.total price')}}</p>
        <p class="font-medium text-[#EC3F59]">{{number_format($totalPrice / 100, 2, ',')}} zł</p>
    </div>
</div>
