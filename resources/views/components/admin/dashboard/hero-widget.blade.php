@props(['class' => '', 'perPage' => 8, 'isFullscreen' => false])

<div class="relative w-full h-full {{$class}}" x-data="{ opened: 1, clickToOrder: true }" {!!$attributes!!}">
    <div class="hero-widget--portrait w-full h-full flex xl:hidden flex-col gap-4" x-show="!clickToOrder">
        <div class="hero-widget--navigation">
            <ul class="flex gap-2">
                <li>
                    <button class="p-4 border-b-2 border-primary-900 dark:border-primary-700 text-primary-900 dark:text-primary-700" @click="opened = 1" x-show="opened == 1">{{__('orders.New orders')}}</button>
                    <button class="p-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50" @click="opened = 1" x-show="opened != 1">{{__('orders.New orders')}}</button>
                </li>
                <li>
                    <button class="p-4 border-b-2 border-primary-900 dark:border-primary-700 text-primary-900 dark:text-primary-700" @click="opened = 2" x-show="opened == 2">{{__('orders.Actual orders')}}</button>
                    <button class="p-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50" @click="opened = 2" x-show="opened != 2">{{__('orders.Actual orders')}}</button>
                </li>
                <li>
                    <button class="p-4 border-b-2 border-primary-900 dark:border-primary-700 text-primary-900 dark:text-primary-700" @click="opened = 3" x-show="opened == 3">{{__('orders.Notifications')}}</button>
                    <button class="p-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-50" @click="opened = 3" x-show="opened != 3">{{__('orders.Notifications')}}</button>
                </li>
            </ul>
        </div>
        <div class="hero-widget--tabs relative flex-1 rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50">
            <div class="w-full h-full" x-show="opened == 1">
                <livewire:new-orders class="w-full h-full" />
            </div>
            <div class="w-full h-full" x-show="opened == 2">
                <livewire:actual-orders class="w-full h-full" />
            </div>
            <div id="{{$isFullscreen ? 'app1' : 'app3'}}" class="w-full h-full overflow-hidden" x-show="opened == 3">
                <notifications></notifications>
            </div>
        </div>
    </div>
    <div class="hero-widget--landscape w-full h-full hidden xl:flex gap-4" x-show="!clickToOrder">
        <div class="hero-widget--left-tab flex-1 flex flex-col gap-4">
            <livewire:new-orders class="w-full h-full rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50" />
            <div class="hero-widget--notifications p-6 rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50">
                <h2 class="text-lg font-bold pb-2">{{__('orders.Notifications')}}</h2>
                <div class="horizontal-divider h-px bg-gray-300 dark:bg-gray-700"></div>
                <div id="{{$isFullscreen ? 'app2' : 'app4'}}" class="h-48 overflow-hidden">
                    <notifications></notifications>
                </div>
            </div>
        </div>
        <div class="hero-widget--right-tab flex-1">
            <livewire:actual-orders class="flex-1 h-full rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50" />
        </div>
    </div>
    <x-admin.dashboard.click-to-order x-show="clickToOrder" />
</div>
