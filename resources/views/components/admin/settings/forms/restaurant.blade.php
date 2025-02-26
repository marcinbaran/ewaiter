@props(['data'])
<div class="pt-4 flex flex-col gap-4">
    @foreach($data->resource->value as $key => $value)

        @if($key == 'info')
            <div>
                <x-admin.form.label for="restaurant-info" :required="true">
                    {{ __('settings.value.info') }}
                </x-admin.form.label>
                <x-admin.form.new-input type="text" id="restaurant-info" name="value[{{$key}}]"
                                        :value="$data->resource->value['info']"
                                        :required="true" />
            </div>
        @elseif($key == 'address')
            <div>
                <x-admin.form.label for="restaurant-address" :required="true">
                    {{ __('settings.value.address') }}
                </x-admin.form.label>
                <x-admin.form.new-input type="text" id="restaurant-address" name="value[{{$key}}]"
                                        :value="$data->resource->value['address']"
                                        :required="true" />
            </div>
        @elseif($key == 'company_name')
            <div>
                <x-admin.form.label for="restaurant-company-name" :required="true">
                    {{ __('settings.value.company_name') }}
                </x-admin.form.label>
                <x-admin.form.new-input type="text" id="restaurant-company-name" name="value[{{$key}}]"
                                        :value="$data->resource->value['company_name']"
                                        :required="true" />
            </div>
        @elseif($key == 'company_address')
            <div>
                <x-admin.form.label for="restaurant-company-address" :required="true">
                    {{ __('settings.value.company_address') }}
                </x-admin.form.label>
                <x-admin.form.new-input type="text" id="restaurant-company-address" name="value[{{$key}}]"
                                        :value="$data->resource->value['company_address']"
                                        :required="true" />
            </div>
        @elseif($key == 'company_nip')
            <div>
                <x-admin.form.label for="restaurant-company-nip" :required="true">
                    {{ __('settings.value.company_nip') }}
                </x-admin.form.label>
                <x-admin.form.new-input type="text" id="restaurant-company-nip" name="value[{{$key}}]"
                                        :value="$data->resource->value['company_nip']"
                                        :required="true" />
            </div>
        @else
            <input type="hidden" name="value[{{$key}}]" value="{{ $value }}">
        @endif
    @endforeach
</div>
