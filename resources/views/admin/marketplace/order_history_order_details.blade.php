<x-admin.layout.admin-layout>
    {{--    @dd($orderDetails)--}}
    <div class="py-8 xl:px-8">
        {{--        <div class="container mx-auto p-4">--}}
        {{--            <div class="bg-white shadow-md rounded-lg p-6">--}}
        {{--                <h2 class="text-2xl font-bold mb-4">Order Details (Order #{{ $orderDetails['number'] }})</h2>--}}
        {{--                <h3 class="text-xl font-semibold mb-2"><span class="font-semibold"></span>{{ $orderDetails['payments']['createdAt'] }}</h3>--}}

        {{--        <div class="mb-6">--}}
        {{--            <h3 class="text-xl font-semibold mb-2">Shipping Address</h3>--}}
        {{--            <div class="grid grid-cols-2 gap-4">--}}
        {{--                @foreach($orderDetails['shippingAddress'] as $key => $value)--}}
        {{--            <div>--}}
        {{--                <span class="font-semibold">{{ ucfirst($key) }}: </span>{{ $value }}--}}
        {{--            </div>--}}
        {{--@endforeach--}}
        {{--        </div>--}}
        {{--    </div>--}}

        {{--    <div class="mb-6">--}}
        {{--        <h3 class="text-xl font-semibold mb-2">Billing Address</h3>--}}
        {{--        <div class="grid grid-cols-2 gap-4">--}}
        {{--@foreach($orderDetails['billingAddress'] as $key => $value)--}}
        {{--            <div>--}}
        {{--                <span class="font-semibold">{{ ucfirst($key) }}: </span>{{ $value }}--}}
        {{--            </div>--}}
        {{--@endforeach--}}
        {{--        </div>--}}
        {{--    </div>--}}

        {{--    <div class="mb-6">--}}
        {{--        <h3 class="text-xl font-semibold mb-2">Payment Details</h3>--}}
        {{--        <div class="grid grid-cols-2 gap-4">--}}
        {{--            <div><span class="font-semibold">Method: </span>{{ $orderDetails['payments']['method']['name'] }}</div>--}}
        {{--                <div><span class="font-semibold">Amount: </span>{{ number_format($orderDetails['payments']['amount'] / 100, 2) }} {{ $orderDetails['payments']['currencyCode'] }}</div>--}}
        {{--                <div><span class="font-semibold">State: </span>{{ ucfirst($orderDetails['payments']['state']) }}</div>--}}
        {{--                <div><span class="font-semibold">Created At: </span>{{ $orderDetails['payments']['createdAt'] }}</div>--}}
        {{--                <div><span class="font-semibold">Updated At: </span>{{ $orderDetails['payments']['updatedAt'] }}</div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{--        <div class="mb-6">--}}
        {{--            <h3 class="text-xl font-semibold mb-2">Shipment Details</h3>--}}
        {{--            <div class="grid grid-cols-2 gap-4">--}}
        {{--                <div><span class="font-semibold">State: </span>{{ ucfirst($orderDetails['shipments']['state']) }}</div>--}}
        {{--                @if(isset($orderDetails['shipments']['shippedAt']))--}}
        {{--                    <div><span class="font-semibold">Shipped At: </span>{{ $orderDetails['shipments']['shippedAt'] }}</div>--}}
        {{--                @endif--}}
        {{--                <div><span class="font-semibold">Created At: </span>{{ $orderDetails['shipments']['createdAt'] }}</div>--}}
        {{--                <div><span class="font-semibold">Updated At: </span>{{ $orderDetails['shipments']['updatedAt'] }}</div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{--        <div class="mb-6">--}}
        {{--            <h3 class="text-xl font-semibold mb-2">Order Items</h3>--}}
        {{--            <table class="min-w-full bg-white border">--}}
        {{--                <thead>--}}
        {{--                    <tr>--}}
        {{--                        <th class="py-2 border-b">Product Image</th>--}}
        {{--                        <th class="py-2 border-b">Product Name</th>--}}
        {{--                        <th class="py-2 border-b">Quantity</th>--}}
        {{--                        <th class="py-2 border-b">Unit Price</th>--}}
        {{--                        <th class="py-2 border-b">Total</th>--}}
        {{--                    </tr>--}}
        {{--                </thead>--}}
        {{--                <tbody>--}}
        {{--                    @foreach($orderDetails['items'] as $item)--}}
        {{--            <tr>--}}
        {{--                <td class="py-2 border-b"><image src="{{ $item['productImageUrl'] }}" alt="{{ $item['productName'] }}" class="w-20 h-20 object-cover object-center"/></td>--}}
        {{--                <td class="py-2 border-b">{{ $item['productName'] }}</td>--}}
        {{--                            <td class="py-2 border-b">{{ $item['quantity'] }}</td>--}}
        {{--                            <td class="py-2 border-b">{{ number_format($item['unitPrice'] / 100, 2) }}</td>--}}
        {{--                            <td class="py-2 border-b">{{ number_format($item['total'] / 100, 2) }}</td>--}}
        {{--                        </tr>--}}
        {{--                    @endforeach--}}
        {{--        </tbody>--}}
        {{--    </table>--}}
        {{--</div>--}}

        {{--<div class="mb-6">--}}
        {{--    <h3 class="text-xl font-semibold mb-2">Order Summary</h3>--}}
        {{--    <div class="grid grid-cols-2 gap-4">--}}
        {{--        <div><span class="font-semibold">Items Total: </span>{{ number_format($orderDetails['itemsTotal'] / 100, 2) }}</div>--}}
        {{--                <div><span class="font-semibold">Total: </span>{{ number_format($orderDetails['total'] / 100, 2) }}</div>--}}
        {{--                <div><span class="font-semibold">Tax Total: </span>{{ number_format($orderDetails['taxTotal'] / 100, 2) }}</div>--}}
        {{--                <div><span class="font-semibold">Shipping Total: </span>{{ number_format($orderDetails['shippingTotal'] / 100, 2) }}</div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--</div>--}}
{{--        @dd($orderDetails)--}}
        {{--        $orderDetails['shippingState'] => 'ready' (w trakcie dostawy) else 'shipped' (dostarczono)--}}
        <div>
            <x-admin.marketplace.address-info :data="$orderDetails" :shipMethod="$shipMethod" :payMethod="$payMethod"
                                              :countryName="$countryName" title="order details"
                                              :isOrderDetails="true" :shippingState="$orderDetails['shippingState']"/>
        </div>
        <div>
                <x-admin.marketplace.cart-summary :products="$products" :totalPrice="$orderDetails['total']"
                                                  :shippingPrice="$orderDetails['shippingTotal']"
                                                  :productsPrice="$orderDetails['items']" />
        </div>


    </div>
</x-admin.layout.admin-layout>
