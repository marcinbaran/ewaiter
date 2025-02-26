<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    <x-admin.block.list-element :title="__('refunds.Amount')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->refund->amount, $data->p24_currency)" />
    <x-admin.block.list-element :title="__('refunds.Status')" :value="$data->refund->getStatusName()" />
    @if ($data->refund->refunded)
        <x-admin.block.list-element :title="__('refunds.Refunded')" :value="__('admin.Yes')" />
    @else
        <x-admin.block.list-element :title="__('refunds.Refunded')" :value="__('admin.No')" />
    @endif
</ul>
