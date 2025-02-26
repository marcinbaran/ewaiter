{{--@dump($orders)--}}
<div class="overflow-x-scroll">
    <table class="w-full border border-[#F3F4F6] text-dark-grey-1">
        <thead class="border-b border-[#F3F4F6] text-left">
        <tr>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.number')}}
            </th>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.date')}}
            </th>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.recipient')}}
            </th>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.price')}}
            </th>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.status')}}
            </th>
            <th scope="col" class="border-e border-[#F3F4F6] text-dark-grey-2 py-4 px-8 font-normal">
                {{__('marketplace.action')}}
            </th>
        </tr>
        </thead>
        <tbody class="">
        @foreach($orders as $order)
            <livewire:marketplace.table-item :order="$order" :key="$order['id']" />
        @endforeach

        </tbody>
    </table>
</div>
