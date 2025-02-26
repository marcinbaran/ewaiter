

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Shipping Methods</h1>
    <div class="bg-white shadow rounded-lg p-4">
        @foreach($shippingMethods as $method)

            <div class="border-b border-gray-200 py-2">
                <h2 class="text-xl font-semibold">{{ $method['name'] }}</h2>
                <p class="text-gray-700">Code: {{ $method['code'] }}</p>
                <p class="text-gray-700">Position: {{ $method['position'] }}</p>
                @isset($method['description'])
                    <p class="text-gray-700">Description: {{ $method['description'] }}</p>
                @endisset
                <p class="text-gray-700">Price: ${{ number_format($method['price'] / 100, 2) }}</p>
            </div>
        @endforeach
    </div>
</div>
