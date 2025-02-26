<div class="flex flex-col">
    <ul class="flex flex-col 2xl:flex-row gap-2 2xl:justify-between text-sm leading-5">
        <li class="flex 2xl:flex-col justify-between gap-2">
            <span>{{ __('orders.Payment status') }}:</span>
            <span class="{{ $orders && $orders[0]->paid == 1 ? 'text-green-400' : 'text-red-600' }}">
                {{ $orders ? isset($orders[0]->paid)  ? ($orders[0]->paid ? __('admin.Paid') : __('admin.Not paid') ) : '' : ''}}
            </span>
        </li>
        <li class="flex 2xl:flex-col justify-between gap-2">
            <span>{{ __('bills.Final price') }}:</span>
            <span class="font-bold">
                {{ $orders ? (new \App\Decorators\MoneyDecorator())->decorate($orders[0]->price, 'PLN') : '' }}
            </span>
        </li>
    </ul>
</div>
