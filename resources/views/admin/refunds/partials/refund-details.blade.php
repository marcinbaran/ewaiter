<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    <x-admin.block.list-element :title="__('refunds.Bill')" :value="$data->bill->id" />
    <x-admin.block.list-element :title="__('refunds.Payment')" :value="$data->payment->id" />
    <x-admin.block.list-element :title="__('refunds.Amount')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->amount,'PLN')" />
    <x-admin.block.list-element :title="__('refunds.Status')" :value="$data->getStatusName()" />
    @if($data->refunded)
    <x-admin.block.list-element :title="__('refunds.Refunded')" :value="__('admin.Yes')" />
    @else
        <x-admin.block.list-element :title="__('refunds.Refunded')" :value="__('admin.No')" />
    @endif
</ul>
