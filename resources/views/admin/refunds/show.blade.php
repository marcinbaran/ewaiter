<x-admin.layout.admin-layout>
    <div class="w-full grid grid-cols-1 px-4 pt-6 xl:grid-cols-3 gap-4 text-gray-900 dark:text-gray-50">
        <x-admin.show.details-block :title="__('admin.Refund details')">
            @include('admin.refunds.partials.refund-details')
        </x-admin.show.details-block>
        <x-admin.show.details-block :title="__('refunds.Bill details')">
            @include('admin.refunds.partials.bill-details')
        </x-admin.show.details-block>
        <x-admin.show.details-block :title="__('refunds.Payment details')">
            @include('admin.refunds.partials.payment-details')
        </x-admin.show.details-block>
    </div>
    <div class="px-4 pt-6">
        <x-admin.button type="cancel" color="cancel" href="{{$redirectUrl}}">
            {{ __('admin.Return') }}
        </x-admin.button>
    </div>
</x-admin.layout.admin-layout>
