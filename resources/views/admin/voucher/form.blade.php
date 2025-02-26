<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.vouchers.update', ['voucher' => $data->id]) : route('admin.vouchers.store');
    @endphp
    <x-admin.form.form id="voucher"
                       role="form"
                       method="POST"
                       :action="$action"
                       enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl"
                       formWide="w-1/2"
                       class="flex flex-col gap-6">
        @if(!$data->id)
            <div>
                <x-admin.form.label for="voucher_adding_type" value="{{ __('voucher.form.adding_type') }}"
                                    :required="true" />
                <x-admin.form.new-input type="select" id="voucher_adding_type" name="adding_type"
                                        :value="route('admin.vouchers.adding_types')"
                                        :oldValue="old('adding_type')" />
            </div>
            <div id="voucher_quantity_container" class="hidden">
                <x-admin.form.label for="voucher_quantity" value="{{ __('voucher.form.quantity') }}" :required="true" />
                <x-admin.form.new-input type="number" id="voucher_quantity" name="quantity" :required="false" min="2"
                                        max="50"
                                        :value="old('quantity')" />
            </div>
        @else
            <div>
                <x-admin.form.label value="{{ __('voucher.form.code') }}" :required="true" />
                <p class="text-gray-900 dark:text-gray-50">{{$data->code}}</p>
            </div>
        @endif
        <div>
            <x-admin.form.label for="voucher_comment" value="{{ __('voucher.form.comment') }}" :required="true" />
            <x-admin.form.new-input type="text" id="voucher_comment" name="comment" :required="true" min="3" max="100"
                                    value="{{old('comment', $data->comment)}}" />
        </div>
        <div>
            <x-admin.form.label for="voucher_value" value="{{ __('voucher.form.value') }}" :required="true" />
            <x-admin.form.new-input type="money" id="voucher_value" name="value" :required="true" min="1" max="500"
                                    value="{{ old('value', $data->value) }}" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
