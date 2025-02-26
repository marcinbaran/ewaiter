<x-admin.layout.admin-layout>
    <div class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 gap-4 w-full">
        <x-admin.show.details-block :title="__('refunds.Payment details')">
            @include('admin.payments.partials.payment-details')
        </x-admin.show.details-block>
        @if($data->refund instanceof \App\Models\Refund)
            <x-admin.show.details-block :title="__('admin.Refund details')">
                @include('admin.payments.partials.refund-details')
            </x-admin.show.details-block>
        @endif
        <x-admin.show.details-block :title="__('refunds.Bill details')">
            @include('admin.payments.partials.bill-details')
        </x-admin.show.details-block>
    </div>
    <div class="px-4 pt-6">
        <x-admin.button type="cancel" color="cancel" href="{{$redirectUrl}}" class="mr-2">
            {{ __('admin.Return') }}
        </x-admin.button>
    </div>
</x-admin.layout.admin-layout>
