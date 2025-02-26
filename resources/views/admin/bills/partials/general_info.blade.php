<ul>
    <x-admin.block.list-element :title="__('orders.CreatedAt')" :value="$data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('Y-m-d H:i') : '-'" />
    @include('admin.bills.partials.payment_type')
    <x-admin.block.list-element :title="__('admin.Delivery type')">
        <x-slot name="value">@include('admin.bills.partials.delivery_type')</x-slot>
    </x-admin.block.list-element>
</ul>
@include('admin.bills.partials.waiting_time')