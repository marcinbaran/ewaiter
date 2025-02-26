@props(['waitTime' => null, 'error' => null, 'calculatedDeliveryTime' => null, 'class' => '', 'isNewOrder' => false, 'orders' => null])

<ul class="{{ $class }} flex flex-col gap-2 text-sm leading-5">
    <li class="flex items-center justify-between gap-2">
        <div class="flex flex-grow flex-col justify-center gap-2">
            @if($isNewOrder)
            <span class="block">{{ __('admin.Time wait') }} (min): </span>
            <span class="block text-xs">
                {{ __('orders.Delivery time') }}:
                {{ $calculatedDeliveryTime ? $calculatedDeliveryTime->format('H:i') : '0:00' }}
            </span>
            @else
            <span class="block">{{ __('admin.Time wait') }}: </span>
            @endif
        </div>
        @if ($isNewOrder)
        <div class="flex w-24 items-center justify-betweem gap-2 pr-4 2xl:pr-0">
            <x-admin.form.new-input type="text" containerClass="min-w-0 flex-1" class="input-mask" format="integer" wire:model='waitTime' :error="$error" />
            <div class="flex flex-col justify-center items-center">
                <button id="increase" wire:click='increaseValue'>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-up w-6 h-6"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M6 15l6 -6l6 6"></path>
                    </svg>
                </button>
                <button id="decrease" wire:click='decreaseValue'>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down w-6 h-6"  viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M6 9l6 6l6 -6"></path>
                    </svg>
                </button>
            </div>
        </div>
        @else
        <div class="flex items-center justify-center text-right">
            {{ $orders[0]->time_wait ?? '' }}
        </div>
        @endif
    </li>
    <li class="text-red-600">
        {{ $error ? __('admin.Time wait is required') : '' }}
    </li>
</ul>
