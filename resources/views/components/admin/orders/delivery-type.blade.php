@props(['orders' => [], 'class' => ''])

<ul>
    <li class="flex justify-between gap-2 leading-5 text-sm {{ $class }}">
        <span>{{__('orders.Type of delivery')}}: </span>
        @if($orders)
        <span>{{$orders[0]->getTypeDelivery() }}</span>
        @endif
    </li>
</ul>