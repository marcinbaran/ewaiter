@if(auth()->check())
    <div class="relative h-full flex flex-col items-center text-gray-900 dark:text-gray-50">
        <div class="flex w-full justify-between justify-self-start">
            <h3 class="text-xl font-bold">
                {{ __('admin.Bill status') }}
            </h3>
            <span class="text-sm text-gray-600 dark:text-gray-400" data-tooltip-target="tooltip-updated-at"
                  data-tooltip-placement="top">
            {{ __('admin.Edited') }} {{ \Carbon\Carbon::parse($resource->updated_at)->diffForHumans() }}
        </span>
            <div
                class="tooltip invisible text-right absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm dark:bg-gray-700"
                id="tooltip-updated-at" role="tooltip">
                {{ $resource->updated_at }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>
        <div
            class="{{ $status === \App\Models\Bill::STATUS_RELEASED ? 'justify-around' : 'justify-between' }} w-full mt-8 grid md:grid-cols-12 md:grid-rows-1 md:auto-cols-auto grid-cols-2 grid-rows-1 gap-4 md:px-4 items-center xl:translate-y-1/3"
            x-data="{ 'open': false }">
            @if ($status === \App\Models\Bill::STATUS_CANCELED || $status === \App\Models\Bill::STATUS_COMPLAINT)
                <span
                    class="text-lg col-span-2 row-span-2  first:text-lg dark:text-white"> {!! (new \App\Decorators\OrderStatusDecorator())->decorate($resource) !!}</span>
            @else

                <div
                    class="absolute left-1/2 top-1/2 z-50 h-full w-full -translate-x-1/2 -translate-y-1/2 rounded-lg backdrop-blur"
                    x-cloak x-show="open"></div>
                <div
                    class="absolute left-1/2 top-1/2 z-50 flex p-4 -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center gap-4 rounded-lg border border-gray-300 bg-gray-200 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-50"
                    x-cloak x-show="open" x-on:click.away="open = false">
                    <p class="text-center">
                        {{ __('admin.Are you sure you want to cancel the order?') }}
                    </p>
                    <div class="flex items-center justify-center gap-4">
                        <x-admin.button color="cancel" x-on:click="open = false">{{ __('admin.No') }}</x-admin.button>
                        <x-admin.button color="danger" wire:click.debounce.300ms="cancel"
                                        x-on:click="open = false">{{ __('admin.Yes') }}</x-admin.button>
                    </div>
                </div>

                @if(!$isUserAdmin && $status === \App\Models\Bill::STATUS_RELEASED)
                    <div
                        class="md:col-span-2 md:row-span-1 max-md:row-start-2 max-md:row-end-3 col-span-1 group flex cursor-pointer flex-col items-center justify-center gap-2">
                    </div>
                @else
                    <div
                        class="md:col-span-2 md:row-span-1 max-md:row-start-2 max-md:row-end-3 col-span-1 group flex cursor-pointer flex-col items-center justify-center gap-2"
                        id="cancel" x-on:click="open = true">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-700 group-hover:bg-red-800 dark:bg-red-600
                            dark:group-hover:bg-red-700 text-gray-50">
                            <svg class="icon icon-tabler icon-tabler-x h-10 w-10" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M18 6l-12 12"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                        </div>
                        <span class="text-center text-sm">
                    {{ $status === \App\Models\Bill::STATUS_RELEASED ? __('orders.Refund Order') : __('orders.Cancel Order') }}
                </span>
                    </div>
                @endif

            @endif
            <div
                class="relative md:col-span-8 row-span-1 col-span-2  flex h-fit w-full items-center justify-between gap-3 "
                wire:model="statusElements">
                <div class="{{ $statusElements[0]['pointClass'] }}" id="new">
                <span
                    class="absolute -top-6 left-1/2 -translate-x-1/2 transform text-[0.7rem] md:text-xs font-bold transition-all duration-75">
                    {{ __('orders.New') }}
                </span>
                </div>

                <div class="absolute top-1/2 z-10 h-[5px] w-1/3 -translate-y-1/2 overflow-hidden bg-white">
                    <div class="{{ $statusElements[1]['statusBar'] }}" id="acceptedBar"></div>
                </div>
                <div class="{{ $statusElements[1]['pointClass'] }}" id="acceptedElement">
                <span
                    class="absolute -top-6 left-1/2 -translate-x-1/2 transform text-[0.7rem] md:text-xs font-bold transition-all duration-75">
                    {{ __('orders.Accepted') }}
                </span>
                </div>

                <div class="absolute left-1/3 top-1/2 z-10 h-[5px] w-1/3 -translate-y-1/2 overflow-hidden bg-white">
                    <div class="{{ $statusElements[2]['statusBar'] }}" id="readyBar"></div>
                </div>
                <div class="{{ $statusElements[2]['pointClass'] }}" id="readyElement">
                <span
                    class="absolute -top-6 left-1/2 -translate-x-1/2 transform text-[0.7rem] md:text-xs font-bold transition-all duration-75">
                    {{ __('orders.Ready') }}
                </span>
                </div>
                <div class="absolute left-2/3 top-1/2 z-10 h-[5px] w-1/3 -translate-y-1/2 overflow-hidden bg-white">

                    <div class="{{ $statusElements[3]['statusBar'] }}" id="releasedBar"></div>
                </div>
                <div class="{{ $statusElements[3]['pointClass'] }}" id="releasedElement">
                <span
                    class="absolute -top-6 left-1/2 -translate-x-1/2 transform text-[0.7rem] md:text-xs font-bold transition-all duration-75">
                    {{ __('orders.Released') }}
                </span>
                </div>
            </div>
            @if (
            $status !== \App\Models\Bill::STATUS_RELEASED &&
            $status !== \App\Models\Bill::STATUS_CANCELED &&
            $status !== \App\Models\Bill::STATUS_COMPLAINT)
                <div wire:loading.remove
                     class="group max-md:row-start-2 max-md:row-end-3 col-span-1 md:col-span-2  flex cursor-pointer flex-col items-center justify-center gap-2"
                     id="next" wire:click.debounce.300ms="next">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-900 group-hover:bg-primary-800 dark:bg-primary-700 dark:group-hover:bg-primary-800 text-gray-50">
                        <svg class="icon icon-tabler icon-tabler-chevron-right h-10 w-10 translate-x-[1px]"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1"
                             stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </div>
                    @switch($status)
                        @case(\App\Models\Bill::STATUS_NEW)
                            <span class="text-center text-sm">{{ __('orders.Accept Order') }}</span>
                            @break

                        @case(\App\Models\Bill::STATUS_ACCEPTED)
                            <span class="text-center text-sm">{{ __('orders.Ready Order') }}</span>
                            @break

                        @case(\App\Models\Bill::STATUS_READY)
                            <span class="text-center text-sm">{{ __('orders.Release Order') }}</span>
                            @break

                        @default
                            @break
                    @endswitch
                </div>
            @endif


        </div>

    </div>
@else
    <x-admin.logged-out class="w-full h-full" />
@endif
