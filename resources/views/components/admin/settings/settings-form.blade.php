@php
    $action = $data->id ? route('admin.settings.update', ['settings' => $data->id]) : route('admin.settings.store');
    $createTpayTenantRoute= isset($data->value['konto_tpay'])?route('admin.settings.createTpayAccount', ['settings' => $data->id]):null;
    $action = $data->key == 'konto_tpay'? $createTpayTenantRoute : $action;
@endphp

@if ($data->key == 'konfiguracja_dostawy')
    <button
        class="settings-menu-button mb-4 flex w-full flex-row items-center justify-between rounded-lg bg-none text-gray-900 hover:bg-gray-800 dark:text-gray-50 md:hidden md:justify-center md:hover:bg-transparent"
        type="button">
        <h3 class="text-bold pb-2 text-center text-3xl text-gray-900 dark:text-gray-50">
            {{ __('settings.konfiguracja_dostawy') }}
        </h3>
        <svg class="icon icon-tabler icon-tabler-chevron-down md:hidden" xmlns="http://www.w3.org/2000/svg" width="28"
             height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
             stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M6 9l6 6l6 -6"></path>
        </svg>
    </button>
    <livewire:datatables.delivery-range-datatable></livewire:datatables.delivery-range-datatable>
    <div id="polygon-delivery-range" class="z-0">
        <polygon-delivery-range-configuration readonly="{{true}}" />
    </div>
@else
    <x-admin.form.form id="setting" role="form" method="POST" :action='$action' enctype="multipart/form-data"
                       formWide="w-2/3">
        <button
            class="settings-menu-button flex w-full flex-row items-center justify-between rounded-lg bg-none text-gray-900 hover:bg-gray-600 hover:text-gray-50 hover:ring-1 hover:ring-gray-600 dark:text-gray-50 dark:hover:bg-gray-800 dark:hover:ring-gray-800 md:justify-center"
            type="button">
            <h3 class="text-bold pb-2 text-center text-3xl">
                {{ __('settings.' . $data->key) }}
            </h3>
            <svg class="icon icon-tabler icon-tabler-chevron-down h-7 w-7 md:hidden" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6 9l6 6l6 -6"></path>
            </svg>
        </button>
        <div class="flex flex-col divide-y divide-gray-300 dark:divide-gray-700">
            <div class="mb-4 flex flex-col gap-2">
                @if (!empty($data->description))
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ old('description', $data->description) }}
                    </p>
                @endif
            </div>
            @if ($data->key == config('settings.logo_key'))
                <x-admin.settings.forms.logo :data="$data" />
            @elseif ($data->key == config('settings.service_charge_key'))
                <x-admin.settings.forms.service-charge :data="$data" />
            @elseif($data->key == config('settings.online_payment_provider_account_key'))
                <x-admin.settings.forms.online-payment-provider-form :data="$data" />
            @elseif($data->key == config('settings.delivery_disable_time_key'))
                <x-admin.settings.forms.delivery-disable-time :data="$data" />
            @elseif($data->key == config('settings.restaurant_key'))
                <x-admin.settings.forms.restaurant :data="$data" />
            @else
                @foreach ($data->resource->value as $key => $value)
                    @switch($data->key)
                        @case('restauracja')
                            @if($key == 'info')
                                <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"
                                     data-setting-type="{{ $data->resource->value_type[$key] }}">
                                    <x-admin.form.label
                                        for="{{ $key }}">{{ __('settings.value.' . $key) }}</x-admin.form.label>
                                    @include('admin.settings.form.' . $data->resource->value_type[$key], compact('key', 'value', 'loop'))
                                </div>
                            @else
                                @include('admin.settings.form.hidden', compact('key', 'value', 'loop'))
                            @endif
                            @break
                        @case('rodzaje_dostawy')
                            <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"
                                 data-setting-type="{{ $data->resource->value_type[$key] }}">
                                <x-admin.form.toggle name="value_active[{{ $key }}]" id="{{ $key }}"
                                                     checked="{{ isset($data->resource->value_active[$key]) && $data->resource->value_active[$key] }}">{{ __('settings.value.' . $key) }}</x-admin.form.toggle>
                                <input type="hidden" name="value[{{ $key }}]" value="{{ $value }}">
                            </div>
                            @break
                        @case('sposoby_platnosci')
                            @if( (__('settings.value.' . $key))=='Karta' || (__('settings.value.' . $key))=='Rachunek hotelowy' || (__('settings.value.' . $key))=='Przelewy24' || ((__('settings.value.' . $key))=='Tpay' && $tpayDisabled))
                                <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"
                                     data-setting-type="{{ $data->resource->value_type[$key] }}">
                                    <x-admin.form.toggle disabled name="value_active[{{ $key }}]" id="{{ $key }}"
                                                         checked="{{ !isset($data->resource->value_active[$key]) && $data->resource->value_active[$key] }}">{{ __('settings.value.' . $key) }}</x-admin.form.toggle>
                                    @include(
                                    'admin.settings.form.' . $data->resource->value_type[$key],
                                    compact('key', 'value', 'loop'))
                                </div>
                                @break
                            @endif
                            <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"
                                 data-setting-type="{{ $data->resource->value_type[$key] }}">
                                <x-admin.form.toggle name="value_active[{{ $key }}]" id="{{ $key }}"
                                                     checked="{{ isset($data->resource->value_active[$key]) && $data->resource->value_active[$key] }}">{{ __('settings.value.' . $key) }}</x-admin.form.toggle>
                                @include(
                                'admin.settings.form.' . $data->resource->value_type[$key],
                                compact('key', 'value', 'loop'))
                            </div>
                            @break
                            {{--                            @case('online_payment_provider_account')--}}
                            {{--                            <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"--}}
                            {{--                                 data-setting-type="{{ $data->resource->value_type[$key] }}">--}}
                            {{--                                <x-admin.settings.forms.online-payment-provider-form :data="$data" />--}}
                            {{--                            </div>--}}
                            {{--                            @break--}}
                        @default
                            <div class="setting flex flex-col gap-2 py-4" data-setting="{{ $key }}"
                                 data-setting-type="{{ $data->resource->value_type[$key] }}">
                                <x-admin.form.toggle name="value_active[{{ $key }}]" id="{{ $key }}"
                                                     checked="{{ isset($data->resource->value_active[$key]) && $data->resource->value_active[$key] }}">{{ __('settings.value.' . $key) }}</x-admin.form.toggle>
                                @include(
                                'admin.settings.form.' . $data->resource->value_type[$key],
                                compact('key', 'value', 'loop'))
                            </div>

                    @endswitch

                @endforeach
            @endif
        </div>
    </x-admin.form.form>
@endif
