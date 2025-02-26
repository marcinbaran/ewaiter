@props(['class' => '', 'orders' => []])

<div class="new-order--client-details {{ $class }} flex flex-col gap-2">
    <h3 class="font-bold">{{ __('admin.Client details') }}</h3>
    <ul class="flex flex-grow flex-col gap-2 text-sm leading-5">
        @if (isset($orders[0]->address))
        @if (isset($orders[0]->address->company_name))
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Company name') }}:</span>
            <span class="text-right">{{ $orders[0]->address->company_name }}</span>
        </li>
        @endif
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Name') }}:</span>
            <span class="text-right">{{ $orders[0]->user->first_name ?? '-' }}</span>
        </li>
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Email') }}:</span>
            <span class="text-right">{{ $orders[0]->user->email ?? '-' }}</span>
        </li>
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Phone') }}:</span>
            <span class="text-right">{{ $orders[0]->address->phone ?? '-' }}</span>
        </li>
        <li class="flex justify-between gap-2">
            <span>{{ __('admin.Address') }}:</span>
            <span class="text-right">
                {{ $orders[0]->address->street }}
                {{ $orders[0]->address->house_number }}
                {{ $orders[0]->address->building_number ?? '' }}<br />
                {{ $orders[0]->address->city }}
                {{ $orders[0]->address->postal_code }}
            </span>
        </li>
        @else
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Name') }}:</span>
            <span class="text-right">{{ $orders[0]->user->first_name ?? '' }}</span>
        </li>
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Email') }}:</span>
            <span class="text-right">{{ isset($orders[0]->user->email) ? (str_contains($orders[0]->user->email, '@') ? $orders[0]->user->email : '-' ):'-' }}</span>
        </li>
        <li class="flex justify-between gap-2">
            <span>{{ __('addresses.Phone') }}:</span>
            <span class="text-right">{{ $orders[0]->phone ?? '-' }}</span>
        </li>
        @endif
        @if($orders)
        @if ($orders[0]->comment)
        <li class="mt-2 flex flex-col justify-between gap-2">
            <h3 class="text-base font-bold">{{ __('admin.Comment') }}:</h3>
            <span class="text-justify break-all">{{ $orders[0]->comment }}</span>
        </li>
        @else
        <li class="flex-row flex justify-between gap-2">
            <span>{{ __('admin.Comment') }}:</span>
            <span class="text-justify ">-</span>
        </li>
        @endif
        @endif
    </ul>
</div>