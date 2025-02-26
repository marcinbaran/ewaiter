<tr>
    @if ($loadData)
        <td class="border-r border-[#F3F4F6] px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">#{{ $order['number'] }}</td>
        <td class="border-r border-[#F3F4F6] px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">{{ $orderDetails['payments']['createdAt'] ?? 'N/A' }}</td>
        <td class="border-r border-[#F3F4F6] px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">{{ $orderDetails['shippingAddress']['firstName'] ?? '' }} {{ $orderDetails['shippingAddress']['lastName'] ?? '' }}</td>
        <td class="border-r border-[#F3F4F6] px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">{{ number_format($orderDetails['total'] / 100, 2,) }} zł</td>
        <td class="px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">
            <div>
                @if($orderDetails['checkoutState'] === 'completed')
                    <p class="bg-[#27CCF0] text-white rounded py-1 px-2 text-center w-fit">{{__('marketplace.'.$orderDetails['checkoutState'])}}</p>
                @elseif($orderDetails['checkoutState'] === 'fulfilled')
                    <p class="bg-[#2FB575] text-white rounded py-1 px-2 text-center w-fit">{{__('marketplace.'.$orderDetails['checkoutState'])}}</p>
                @elseif($orderDetails['checkoutState'] === 'cancelled')
                    <p class="bg-[#BABFCA] text-white rounded py-1 px-2 text-center w-fit">{{__('marketplace.'.$orderDetails['checkoutState'])}}</p>
                @endif
            </div>
        </td>
        <td class="px-8 py-4 whitespace-nowrap text-dark-grey-1 font-light">
            <form action="{{ route('admin.marketplace.order_history_order_details', ['orderId' => $order['id']]) }}"
                  method="GET">
                @csrf
                <input type="hidden" name="order" value="{{ $order['tokenValue'] }}">
                <button type="submit"
                        class="px-4 py-2 flex justify-center gap-2 border border-[#E5E7EB] rounded text-dark-grey-2 w-full">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15 15L10.3333 10.3333M1 6.44444C1 7.15942 1.14082 7.86739 1.41443 8.52794C1.68804 9.18849 2.08908 9.78868 2.59464 10.2942C3.1002 10.7998 3.7004 11.2008 4.36095 11.4745C5.0215 11.7481 5.72947 11.8889 6.44444 11.8889C7.15942 11.8889 7.86739 11.7481 8.52794 11.4745C9.18849 11.2008 9.78868 10.7998 10.2942 10.2942C10.7998 9.78868 11.2008 9.18849 11.4745 8.52794C11.7481 7.86739 11.8889 7.15942 11.8889 6.44444C11.8889 5.72947 11.7481 5.0215 11.4745 4.36095C11.2008 3.7004 10.7998 3.1002 10.2942 2.59464C9.78868 2.08908 9.18849 1.68804 8.52794 1.41443C7.86739 1.14082 7.15942 1 6.44444 1C5.72947 1 5.0215 1.14082 4.36095 1.41443C3.7004 1.68804 3.1002 2.08908 2.59464 2.59464C2.08908 3.1002 1.68804 3.7004 1.41443 4.36095C1.14082 5.0215 1 5.72947 1 6.44444Z"
                            stroke="#596273" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{__('marketplace.look')}}
                </button>
            </form>
        </td>
    @else
        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
{{--            loading spinner --}}
            <span class="w-12 h-12 border-[5px] border-[#E5E7EB] rounded-full border-b-[#E43E50] inline-block animate-spin"></span>
        </td>
    @endif
</tr>

<script>
    document.addEventListener("livewire:load", function() {
    @this.loadOrderDetails(@this.order["tokenValue"])
    });
</script>
