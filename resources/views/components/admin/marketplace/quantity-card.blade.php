@props( ["variants"=>""] )

<div class="bg-gray-200 p-6 rounded-lg shadow-lg dark:bg-gray-700 dark:text-primary-700">
    <form action="{{route('admin.marketplace.add_to_cart')}}" method="post" class="space-y-4">
        @csrf
        <div>
            <label for="quantity"
                   class="block text-sm font-medium text-gray-700">{{__('marketplace.quantity')}}</label>
            <input type="number" name="quantity" required
                   class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                   placeholder="{{__('marketplace.quantity')}}" min="0">
        </div>
        <div>
            <label for="variant" class="block text-sm font-medium text-gray-700">{{__('marketplace.variant')}}</label>
            <select name="variant" id="variant"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                @foreach ($variants as $variant)
                    <option class="bg-white text-black"
                            value="{{ $variant->code }}">{{ $variant->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-500 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                {{__('marketplace.add to cart')}}
            </button>
        </div>
    </form>
</div>
