<x-admin.layout.admin-layout>
    <div class="flex items-center flex-col">
        <div class="grid grid-cols-1 px-4 pt-6 xl:grid-cols-12 xl:gap-4 gap-4 dark:bg-gray-900 w-full mb-4">
            <x-admin.show.details-block class="col-span-12 xl:col-span-4 flex flex-col">
                @include('admin.bills.partials.general_info')
            </x-admin.show.details-block>
            <x-admin.show.details-block class="col-span-12 xl:col-span-8">
                <livewire:bill-status />
            </x-admin.show.details-block>
            <x-admin.show.details-block :title="__('admin.Client details')" id="bills_client_details"
                                        class="col-span-12 xl:col-span-3">
                @include('admin.bills.partials.client_details')
            </x-admin.show.details-block>
            <x-admin.show.details-block class="col-span-12 xl:col-span-9" :title="__('admin.Ordered items')"
                                        id="bills_ordered_items">
                @include('admin.bills.partials.ordered_items')
            </x-admin.show.details-block>
            @if($data->address)
                <x-admin.show.details-block class="col-span-12 xl:col-span-9" :title="__('admin.Delivery place')">
                    @include('admin.bills.partials.map')
                </x-admin.show.details-block>
                <x-admin.show.details-block class="col-span-12 xl:col-span-3" :title="__('bills.Payment details')">
                    @include('admin.bills.partials.payment_details')
                </x-admin.show.details-block>
            @endif

        </div>
        <div class="flex w-full justify-between px-4">
            <div class="w-full flex justify-end">
                <x-admin.button type="link" color="link" href="{{$redirectUrl}}"
                                class="mr-3">
                    {{ __('admin.Back') }}
                </x-admin.button>
            </div>

        </div>
    </div>

    @section('bottomscripts')
        <script>
            function PrintElem() {
                var mywindow = window.open("", "PRINT", "height=400,width=600");

                mywindow.document.write("</head><body >");
                mywindow.document.write("<h1>" + document.title + "</h1>");
                mywindow.document.write(document.getElementById("bills_ordered_items").innerHTML);
                mywindow.document.write(document.getElementById("bills_ordered_items").innerHTML);
                mywindow.document.write("</body></html>");
                mywindow.document.close(); // necessary for IE >= 10

                mywindow.focus(); // necessary for IE >= 10*/
                mywindow.print();
                mywindow.close();

                return true;
            }
        </script>
    @append
</x-admin.layout.admin-layout>
