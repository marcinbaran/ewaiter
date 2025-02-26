@props(['orders' => [], 'class' => ''])


<ul>
    <li class="flex justify-between gap-2 leading-5 text-sm {{ $class }}">
        <span>{{__('orders.Type of payment')}}: </span>
        @if($orders)
        <span>{{__('orders.' . $orders[0]->paid_type)}}</span>
        @endif
    </li>
</ul>