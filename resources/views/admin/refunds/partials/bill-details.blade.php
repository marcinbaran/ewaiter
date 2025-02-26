<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    @if($data->bill->comment)
        <x-admin.block.list-element :title="__('admin.Comment')" :value="$data->bill->comment" />
    @else
        <x-admin.block.list-element :title="__('admin.Comment')" :value="__('admin.Absence')" />
    @endif
    @if($data->bill->room_delivery)
        <x-admin.block.list-element :title="__('refunds.Room delivery')" :value="__('admin.Yes')" />
    @else
        <x-admin.block.list-element :title="__('refunds.Room delivery')" :value="__('admin.No')"  />
    @endif
    <x-admin.block.list-element :title="__('admin.Cost of delivery')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->bill->delivery_cost,'PLN')" />
    <x-admin.block.list-element :title="__('admin.Points')" :value="$data->bill->points" />
    <x-admin.block.list-element :title="__('admin.Price')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->bill->price,'PLN')" />
</ul>
