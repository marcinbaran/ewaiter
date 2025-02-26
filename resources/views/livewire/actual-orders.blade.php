@props(['class' => ''])

@if(auth()->check())
<div class="{{$class}} relative flex flex-col gap-6 overflow-hidden">
    <div class="actual-orders w-full h-full flex flex-col gap-6 p-6" id="actual-orders-element" wire:poll.5000ms="refresh">
        <x-admin.orders.pagination title="{{ __('orders.Actual orders') }}" :page="$page" :numberOfOrders="$numberOfOrders" />
        @if(isset($orders[0]))
            <div class="actual-orders__content grid-rows-order-vertical md:grid-rows-order-horizontal grid h-full auto-rows-min grid-cols-1 gap-x-8 gap-y-4 md:grid-cols-2">
                <x-admin.orders.order-info :orders="$orders" :time="$time" />
                <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                <div class="flex flex-col gap-2">
                    <x-admin.orders.delivery-type :orders="$orders" />
                    <x-admin.orders.pay-type :orders="$orders" />
                </div>
                <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                <x-admin.orders.order-pay-info :orders="$orders" />
                <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                <x-admin.orders.client-details class="border-gray-300 pr-4 dark:border-gray-700 md:col-start-1 md:col-end-2 md:row-start-1 md:row-end-2 md:border-r" :orders="$orders" />
                <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                <x-admin.orders.order-details class="md:col-start-2 md:col-end-3 md:row-start-1 md:row-end-2" :orders="$orders" />
                <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                <x-admin.orders.order-wait-time class="h-full" :orders="$orders" />
                <div class="col-span-1 md:col-span-2 pb-8">
                    @if ($orders)
                        <livewire:bill-status :billId="$orders[0]" />
                    @endif
                </div>
                <div class="new-order--horizontal-divider col-span-2 row-start-2 row-end-3 hidden h-px bg-gray-300 dark:bg-gray-700 md:block">
                </div>
                <div class="new-order--horizontal-divider col-span-2 row-start-4 row-end-5 hidden h-px bg-gray-300 dark:bg-gray-700 md:block">
                </div>
                <div class="new-order--horizontal-divider col-span-2 row-start-6 row-end-7 hidden h-px bg-gray-300 dark:bg-gray-700 md:block">
                </div>
            </div>
        @endif
    </div>
    <div class="scroll-indicator-actual-orders bg-gradient-to-t from-gray-200 dark:from-gray-800 hidden cursor-pointer absolute bottom-0 h-[25%] w-full ">
        <svg xmlns="http://www.w3.org/2000/svg" class="scrollabe-icon-arrow-actual-orders hidden stroke-gray-900 dark:stroke-gray-50 icon icon-tabler icon-tabler-chevron-compact-down absolute left-1/2 bottom-0 -translate-x-1/2  w-20 h-auto" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M4 11l8 3l8 -3"></path>
        </svg>
    </div>
    <div class="absolute inset-0 bg-gray-200 dark:bg-gray-800 rounded-lg overflow-hidden @if(isset($orders[0])) hidden @endif">
        <x-admin.orders.zero-data />
    </div>
</div>
@else
    <x-admin.logged-out :class="$class" />
@endif
