<x-admin.layout.admin-layout>
{{--        @dump($ordersHistory)--}}
    <div class="py-4 px-8">
        <div class="mb-8">
            <h1 class="text-[28px] text-dark-grey-2 font-medium">{{__('marketplace.order history')}}</h1>
            <p class="text-xl text-dark-grey-1 font-light">{{__('marketplace.see previous orders')}}</p>
        </div>
        <div>
            <livewire:marketplace.orders-history-table :lazy="true" />
            <!-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca -->
        </div>
    </div>

</x-admin.layout.admin-layout>
