<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    @if(isset($data->payment_at))
    <x-admin.block.list-element :title="__('bills.Payment date')" :value="\Carbon\Carbon::parse($data->payment_at)->format('Y-m-d H:i')" />
    @endif
    @include('admin.bills.partials.payment_type')
    <x-admin.block.list-element :title="__('bills.Payment status')">
        <x-slot name="value"><span class="@if($data->isPaid() == 'Yes')
         text-green-400 
         @else 
         text-red-700 
         @endif">{{$data->isPaid() == 'Yes' ? __('admin.Paid') : __('admin.NoPaid') }}</span></x-slot>
    </x-admin.block.list-element>
    <x-admin.block.list-element :title="__('bills.Payment amount')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->total_price_to_pay,'PLN')" />
    @if($data->tip > 0)
        <x-admin.block.list-element :title="__('bills.Tip')" :value="$data->tip" />
    @endif
</ul>