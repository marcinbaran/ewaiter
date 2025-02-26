@php
    $billLink = '<a href="'.route('admin.bills.show',$data->bill->id).'" class="hover:text-primary-900 dark:hover:text-primary-700">'.$data->bill->id.'</a>'
@endphp

<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    <x-admin.block.list-element :title="__('admin.Bill id')" :value="$billLink" />
    <x-admin.block.list-element :title="__('admin.Products price')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->bill->price, $data->p24_currency)" />
    @if ($data->bill->delivery_cost > 0)
        <x-admin.block.list-element :title="__('admin.Cost of delivery')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->bill->delivery_cost, $data->p24_currency)" />
    @endif
    @if ($data->bill->service_charge > 0)
        <x-admin.block.list-element :title="__('admin.Service charge')" :value="(new \App\Decorators\MoneyDecorator())->decorate($data->bill->service_charge, $data->p24_currency)" />
    @endif
    @if (!is_null($data->bill->points))
       <x-admin.block.list-element :title="__('admin.Points')" :value="($data->bill->points).' ('.(new \App\Decorators\MoneyDecorator())->decorate($data->bill->points_value, $data->p24_currency).')'" />
    @endif
</ul>
