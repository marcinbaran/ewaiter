@props(['orders' => [], 'prices' => []])

<mj-section>
    <mj-column>
        <x-mail.common.heading-2 padding-top="25px">
            Szczegóły zamówienia:
        </x-mail.common.heading-2>
        <mj-table>
            @foreach($orders as $index => $order)
                <x-mail.order_details.table_order_item :index="$index+1" :name="$order['name']"
                                                       :additions="$order['additions']" :price="$order['price']"
                                                       :currency="$order['currency']" />
                @if($index < count($orders) - 1)
                    <x-mail.order_details.table_divider />
                @endif
            @endforeach
        </mj-table>
        <x-mail.common.divider color="light-pink" />
        <mj-table>
            @foreach($prices as $price)
                <x-mail.order_details.table_price_item :title="$price['name']" :price="$price['value']"
                                                       :currency="$price['currency']" />
                @if($index < count($orders) - 1)
                    <x-mail.order_details.table_divider />
                @endif
            @endforeach
        </mj-table>
    </mj-column>
</mj-section>
