<ul class="divide-y divide-gray-300 dark:divide-gray-700">
    @if($data->address)
        @if(isset($data->address->company_name))
            <x-admin.block.list-element :title="__('addresses.Company name')" :value="$data->address->company_name" />
        @endif
        <x-admin.block.list-element :title="__('addresses.Name')"
                                    :value="$data->user ? $data->user->first_name : 'KONTO USUNIĘTE'" />
        <x-admin.block.list-element :title="__('addresses.Email')"
                                    :value="$data->user ? $data->user->email : 'KONTO USUNIĘTE'" />
        <x-admin.block.list-element :title="__('addresses.City')" :value="$data->address->city" />
        <x-admin.block.list-element :title="__('addresses.Street')" :value="$data->address->street" />
        <x-admin.block.list-element :title="__('addresses.Building number')" :value="$data->address->building_number" />
        <x-admin.block.list-element :title="__('addresses.House number')" :value="$data->address->house_number" />
        <x-admin.block.list-element :title="__('addresses.Postcode')" :value="$data->address->postcode" />
        <x-admin.block.list-element :title="__('addresses.Phone')" :value="$data->address->phone" />
    @elseif($data->user)
        <x-admin.block.list-element :title="__('addresses.Email')"
                                    :value="$data->user->email ?? \Illuminate\Support\Str::upper(__('admin.guest'))" />
        <x-admin.block.list-element :title="__('addresses.Phone')" :value="$data->phone" />
    @else
        <x-admin.block.list-element :title="__('addresses.Phone')" :value="$data->phone" />
    @endif

    @if($data->comment)
        <li class="py-4">
            <div class="flex items-center space-x-4">
                <div class="flex-1 min-w-full">
                <span class="text-sm font-normal truncate dark:text-white hover:underline">
                    {{ __('orders.Comment') }}:
                </span>
                    <span class="block text-xs break-all font-light text-gray-900 dark:text-white">
                    {{ $data->comment?? __('admin.No comment') }}
                </span>
                </div>
            </div>
        </li>
    @endif
</ul>
