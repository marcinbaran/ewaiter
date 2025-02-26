<ul class="divide-y divide-gray-200 dark:divide-gray-600">
    <x-admin.block.list-element :title="__('orders.Type of payment')" :value="$data->getTypePayment() ? $data->getTypePayment() : '-'" />
    <x-admin.block.list-element :title="__('admin.Paid')" :value="__('admin.'.$data->isPaid())" />
    <x-admin.block.list-element :title="__('orders.CreatedAt')" :value="$data->created_at ? $data->created_at : '-'" />
    <x-admin.block.list-element :title="__('admin.Delivery type')" >
        <x-slot name="value">@include('admin.bills.partials.delivery_type')</x-slot>
    </x-admin.block.list-element>
</ul>

