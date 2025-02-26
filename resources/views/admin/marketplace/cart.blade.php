<x-admin.layout.admin-layout>
    <div>
        <b>Produkty w koszyku:</b><br>
        <br>
        <br>
        <br>
        @dump($cartToken)
        {{--    @dump($variantsInCart)--}}
        <form action="{{route('admin.marketplace.update_cart')}}" method="post" id="updateCartForm">
            @csrf
        </form>
        <div>
            <div class="p-4">
                <a href="{{route('admin.marketplace.remove_cart')}}"
                   class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-700 focus:ring-opacity-50">
                    Wyczyść koszyk
                </a>
            </div>
            <div>
                <div class="p-4">
                    <a href="{{route('admin.marketplace.checkout')}}"
                       class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-opacity-50">
                        Zamów
                    </a>
                </div>
            </div>
            <div>
                <button form="updateCartForm" type="submit"
                        class="px-6 py-3 bg-green-500 text-white font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-colors duration-300 ease-in-out">
                    Zaktualizuj koszyk
                </button>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <th>Zdjęcie</th>
                <th>Nazwa</th>
                <th>Ilość</th>
                <th>Cena</th>
                <th>Usuń</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($variantsInCart as $key => $variant)
                <tr>
                    <td>
                        <span> <img src=""> </span>
                    </td>
                    <td>
                        <span class="m-2 p-2">{{ $variant->productName }}</span>
                    </td>
                    <td>

                        <input type="number" name="items[{{$key}}][quantity]" form="updateCartForm" min="1"
                               value="{{ $variant->quantity }}" required>
                    </td>
                    <td>
                        <span class="m-2 p-2">{{ $variant->total }}</span>
                    </td>
                    <td>
                        <div class="m-2 bg-gray-200 p-2 text-center rounded">
                            <form action="{{route('admin.marketplace.remove_from_cart')}}" method="post">
                                @csrf
                                <input
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded"
                                    type="hidden" name="orderItemId" value="{{ $variant->id }}" min="0">
                                <button type="submit"
                                        class="ui circular icon button sylius-cart-remove-butto w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-500 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500n">
                                    X
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br>

        <br>

    </div>
</x-admin.layout.admin-layout>
