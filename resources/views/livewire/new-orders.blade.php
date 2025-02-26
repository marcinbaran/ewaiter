@props(['class' => ''])
@if(auth()->check())
<div class="{{$class}} relative flex flex-col gap-6 overflow-hidden">
    <div class="new-orders w-full h-full flex flex-col gap-6 p-6" id="new-orders-element" data-toast-order-accepted="{{ __('orders.Order accepted') }}" data-toast-order-cancelled="{{ __('orders.Order cancelled') }}" wire:poll.5000ms="refresh" x-data="{ 'open': false }">
        <x-admin.orders.pagination class="h-8" title="{{ __('orders.New orders') }}" :numberOfUnseenOrders="$numberOfUnseenOrders" :hasUnseen="$hasUnseen" :page="$page" :numberOfOrders="$numberOfOrders" />
        @if(isset($orders[0]))
        <div class="new-orders__content flex-grow">
            <div class="w-full h-full flex-grow">
                <div class="md:grid-rows-order-horizontal grid-rows-order-vertical grid h-full auto-rows-min grid-cols-1 gap-x-8 gap-y-4 md:grid-cols-2">
                    <x-admin.orders.order-info :orders="$orders" :time="$time" :unseen="$seenOrder" />
                    <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>

                    <div class="flex flex-col gap-2">
                        <x-admin.orders.delivery-type :orders="$orders" />
                        <x-admin.orders.pay-type :orders="$orders" />
                    </div>
                    <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                    <x-admin.orders.client-details class="border-gray-300 pr-4 dark:border-gray-700 md:col-start-1 md:col-end-2 md:row-start-1 md:row-end-2 md:border-r" :orders="$orders" />
                    <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                    <x-admin.orders.order-details class="md:col-start-2 md:col-end-3 md:row-start-1 md:row-end-2" :orders="$orders" />
                    <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                    <x-admin.orders.order-wait-time :waitTime="$waitTime" :error="$error" :calculatedDeliveryTime="$calculatedDeliveryTime" :isNewOrder="true" />
                    <div class="new-order--horizontal-divider h-px bg-gray-300 dark:bg-gray-700 md:hidden"></div>
                    <div class="flex justify-center items-start gap-2 h-12">
                        <x-admin.button color="danger" x-on:click="open = true">{{ __('orders.Cancel Order') }}</x-admin.button>
                        <x-admin.button color="success" wire:click="acceptStatus">{{ __('orders.Accept Order') }}</x-admin.button>
                    </div>
                    <div class="new-order--horizontal-divider col-span-2 row-start-2 row-end-3 hidden h-px bg-gray-300 dark:bg-gray-700 md:block">
                    </div>
                    <div class="new-order--horizontal-divider col-span-2 row-start-4 row-end-5 hidden h-px bg-gray-300 dark:bg-gray-700 md:block">
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="flex w-full flex-col gap-4 justify-self-end">
            <div class="absolute left-1/2 top-1/2 h-full w-full -translate-x-1/2 -translate-y-1/2 rounded-lg backdrop-blur" x-cloak x-show="open"></div>
            <div class="absolute left-1/2 top-1/2 flex -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 p-4 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50" x-cloak x-show="open" x-on:click.away="open = false">
                <p class="text-center">{{ __('admin.Are you sure you want to cancel the order?') }}</p>
                <div class="flex items-center justify-center gap-4">
                    <x-admin.button color="cancel" x-on:click="open = false">{{ __('admin.No') }}</x-admin.button>
                    <x-admin.button color="danger" wire:click="cancelStatus" x-on:click="open = false">{{ __('admin.Yes') }}</x-admin.button>
                </div>
            </div>
        </div>

    </div>

    <div class="scroll-indicator-new-orders hidden bg-gradient-to-t from-gray-200 dark:from-gray-800  cursor-pointer absolute bottom-0 h-[25%] w-full actual-orders-scroll-indicator ">
        <svg xmlns="http://www.w3.org/2000/svg" class="scrollabe-icon-arrow-new-orders hidden  stroke-gray-900 dark:stroke-gray-50 icon icon-tabler icon-tabler-chevron-compact-down absolute left-1/2 bottom-0 -translate-x-1/2  w-20 h-auto" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M4 11l8 3l8 -3"></path>
        </svg>
    </div>


    <div class="absolute inset-0 bg-gray-200 dark:bg-gray-800 rounded-lg overflow-hidden {{$displayClass}}">
        <x-admin.orders.zero-data />
    </div>
</div>
@else
<x-admin.logged-out :class="$class" />
@endif
