@props(['address'=>[]])
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="mb-4 flex justify-between items-center">
        <h5 class="text-xl font-bold">{{ $address->firstName }} {{ $address->lastName }}</h5>
        <form action="{{ route('admin.marketplace.deleteAddress') }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this address?');">
            @csrf
            <input type="hidden" name="id" value="{{ $address->id }}">
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                {{__('marketplace.delete address')}}
            </button>
        </form>
        <div>
            <a href="{{ route('admin.marketplace.editAddress', $address->id) }}"
               class="text-blue-500 hover:text-blue-700 font-bold py-2 px-4 rounded">
                {{__('marketplace.edit address')}}
            </a>
        </div>
    </div>
    <p class="text-gray-700 space-y-2">
        <span><strong>{{__('marketplace.address form.company')}}:</strong> {{ $address->company }}</span><br>
        <span><strong>{{__('marketplace.address form.phone')}}:</strong> {{ $address->phoneNumber }}</span><br>
        <span><strong>{{__('marketplace.address form.address')}}:</strong> {{ $address->street }}, {{ $address->city }}</span><br>
        <span><strong>{{__('marketplace.address form.postcode')}}:</strong> {{ $address->postcode }}</span><br>
        <span><strong>{{__('marketplace.address form.country')}}:</strong> {{ $address->countryCode }}</span><br>
        <span><strong>{{__('marketplace.address form.province')}}:</strong> {{ $address->provinceName }}</span><br>
    </p>
</div>
