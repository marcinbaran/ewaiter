<li class="py-4">
    <div class="flex flex-col lg:flex-row items-center justify-between space-x-4">
        <p class="text-gray-500 dark:text-white  text-sm break-words leading-4">
            {{ __('orders.Type of payment') }}
        </p>
        <div class="relative text-gray-500 dark:text-white font-semibold text-sm break-all leading-4">
            {{$data->getTypePayment() ? $data->getTypePayment() : '-'}}
            <div class="w-3 h-3 rounded-full 
            @if($data->isPaid() == 'No')
                bg-red-700
            @else
                bg-green-400
            @endif
            absolute -left-4 top-1/2 -translate-y-1/2">   
            </div>
        </div>


    </div>
</li>