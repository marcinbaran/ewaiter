@php
    use App\Models\Settings;
        $service_charge_status = Settings::getSetting('service_charge', 'service_charge', true, false);
        $service_charge_procent_status = Settings::getSetting('service_charge', 'service_charge_procent', true, false);

@endphp
<div class="flex flex-col md:flex-row justify-between items-baseline px-2 gap-8">
    <ul class="divide-y divide-gray-200 dark:divide-gray-600 w-full md:w-1/2">
        <x-admin.block.list-element :title="__('admin.Discount')" :value="$data->discount.' PLN'" />

        <x-admin.block.list-element :title="__('admin.Price')"
                                    :value="(new App\Decorators\MoneyDecorator)->decorate($collectivePriceProductsInOrder,'PLN')" />

        @if($data->delivery_cost)
            <x-admin.block.list-element :title="__('admin.Delivery cost')"
                                        :value="(new App\Decorators\MoneyDecorator)->decorate($data->delivery_cost,'PLN')" />
            <x-admin.block.list-element :title="__('admin.Package price')"
                                        :value="((new App\Decorators\MoneyDecorator)->decorate($data->getPackagePrice(),'PLN'))" />
            @php(
                    $collectivePriceProductsInOrder+=$data->delivery_cost + ($data->getPackagePrice() ?? 0)
                )
        @endif

    </ul>
    <ul class="divide-y divide-gray-200 dark:divide-gray-600 w-full md:w-1/2">
        @if(!is_null($service_charge_procent_status) ||!is_null($service_charge_status))
            @if($data->service_charge)
                <x-admin.block.list-element :title="__('admin.Service charge')"
                                            :value="(new App\Decorators\MoneyDecorator)->decorate($data->service_charge,'PLN')" />
                @php(
                    $collectivePriceProductsInOrder+=$data->service_charge
                )
            @endif
        @endif
        @if($data->points)
            <li class="py-4">
                <div class="flex flex-col md:flex-row items-center space-x-4">
                    <div class="flex-1 min-w-0">
                    <span class="text-sm font-normal truncate  text-gray-500 dark:text-white hover:underline">
                        {{ __('admin.Points') }}
                    </span>
                    </div>
                    <div class="inline-flex items-center">
                    <span class="block text-base font-semibold  truncate dark:text-white" {{($data->isPaid() == "Yes") ? 'style=color:green;':'style="color:red;"' }}>
                        {{ $data->points.'/'.(new App\Decorators\MoneyDecorator)->decorate($data->points_value,'PLN')}}
                        @if($data->points_refunded)
                            ({{__('admin.Refunded')}})
                        @endif
                    </span>
                    </div>
                </div>
            </li>
        @endif
            @php(
            $collectivePriceProductsInOrder-=$data->discount
            )
        <x-admin.block.list-element :title="__('admin.Price without points')"
                                    :value="(new App\Decorators\MoneyDecorator)->decorate($collectivePriceProductsInOrder,'PLN')" />
        <li class="py-4">
            <div class="flex flex-col md:flex-row justify-between items-center space-x-4">
                <p class="text-sm font-bold break-words hover:underline dark:text-white">
                    {{ __('bills.Final price') }}
                </p>
                <div class="inline-flex items-center">
                    <span class="block text-base font-semibold truncate dark:text-white" {{($data->isPaid() == "Yes") ? 'style=color:green;':'style="color:red;"' }}>
                        {{ (new \App\Decorators\MoneyDecorator())->decorate($data->getPriceToPay(),'PLN') }}
                        @if($data->isPaid() == "Yes")
                            ({{__('admin.Paid')}})
                        @endif
                    </span>
                </div>
            </div>
        </li>
    </ul>
</div>
