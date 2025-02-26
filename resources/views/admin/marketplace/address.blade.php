<x-admin.layout.admin-layout>
    <div class="ui grid">
        <div class="row">
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.marketplace.new-address') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{__('marketplace.add new address')}}
                </a>
            </div>
            <div class="column">

{{--                <div class="active section">{{__('admin.marketplace.delivery address')}}</div>--}}

                @foreach ($data as $address)
                    <x-admin.marketplace.address-card :address="$address"></x-admin.marketplace.address-card>
                @endforeach


            </div>
        </div>
    </div>

</x-admin.layout.admin-layout>
