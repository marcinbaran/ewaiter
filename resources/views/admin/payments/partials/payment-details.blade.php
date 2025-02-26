<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    <x-admin.block.list-element :title="__('admin.Payment type')" :value="$data->type" />
    <x-admin.block.list-element :title="__('admin.Price to pay')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->p24_amount/100,$data->p24_currency)" />
    <x-admin.block.list-element :title="__('admin.email')" :value="$data->email" />
    <x-admin.block.list-element :title="__('admin.Paid')" :value="(new \App\Decorators\BoolStatusDecorator())->decorate($data->paid)" />
</ul>