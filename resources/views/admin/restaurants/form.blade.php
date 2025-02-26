<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.restaurants.update', ['restaurant' => $data->id]) : route('admin.restaurants.store');
    @endphp
    <x-admin.form.form id="restaurantForm" id="restaurant" role="form" method="POST" :redirectUrl="$redirectUrl"
                       :action="$action"
                       enctype="multipart/form-data" formWide="w-1/2" class="flex flex-col gap-6">
        <div>
            <x-admin.form.new-input type="toggle" id="restaurant_visibility" name="visibility"
                                    :checked="old('visibility', $data->visibility)"
                                    :placeholder="__('admin.Is the restaurant visible?')" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_name" value="{{ __('admin.Name') }}" :required="true" />
            <x-admin.form.new-input type="text" id="restaurant_name" name="name" value="{{ old('name', $data->name) }}"
                                    min="3" max="100" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_hostname" value="{{ __('admin.Hostname') }}" :required="true" />
            <input type="hidden" name="hostname" value="{{ old('hostname', $data->hostname) }}">
            <x-admin.form.new-input type="text" id="restaurant_hostname" name="hostname"
                                    value="{{ old('hostname', $data->hostname) }}"
                                    suffix=".{{ env('TENANCY_DEFAULT_HOSTNAME') }}" min="3" max="64" :required="true"
                                    :disabled="isset($data->id)" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_manager_email" value="{{ __('admin.Manager email') }}" :required="true" />
            <x-admin.form.new-input type="email" id="restaurant_manager_email" name="manager_email"
                                    value="{{ old('manager_email', $data->manager_email) }}" :required="true" :max="255"  />
        </div>
        <div>
            <x-admin.form.label for="restaurant_tags" value="{{ __('admin.RestaurantTags') }}"></x-admin.form.label>
            <x-admin.form.new-input type="select" mode="multiple" id="restaurant_tags" name="tag_checkbox[][id]"
                                    :oldValue="$oldRestaurantTags"
                                    :value="route('admin.restaurants.restaurant_tags', ['id' => $data->id])">
            </x-admin.form.new-input>
        </div>
        <div>
            <x-admin.form.new-input type="toggle" id="restaurant_table_reservation_active" name="table_reservation_active"
                                    :checked="old('table_reservation_active', $data->table_reservation_active ?? true)"
                                    :placeholder="__('admin.is_active_table_reservation')" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_postcode" value="{{ __('addresses.Postcode') }}" :required="true" />
            <x-admin.form.new-input type="postal-code" id="restaurant_postcode" name="address[postcode]"
                                    value="{{ old('address.postcode', $data->address->postcode) }}" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_city" value="{{ __('addresses.City') }}" :required="true" />
            <x-admin.form.new-input type="text" id="restaurant_city" name="address[city]"
                                    value="{{ old('address.city', $data->address->city) }}" min="3" max="50"
                                    :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_street" value="{{ __('addresses.Street') }}" :required="true" />
            <x-admin.form.new-input type="text" id="restaurant_street" name="address[street]"
                                    value="{{ old('address.street', $data->address->street) }}" min="3" max="70"
                                    :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_building_number" value="{{ __('addresses.Building number') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="restaurant_building_number" name="address[building_number]"
                                    value="{{ old('address.building_number', $data->address->building_number) }}"
                                    min="1" max="10" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_house_number" value="{{ __('addresses.House number') }}" />
            <x-admin.form.new-input id="restaurant_house_number" name="address[house_number]"
                                    value="{{ old('address.house_number', $data->address->house_number) }}" min="1"
                                    max="10" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_phone" value="{{ __('addresses.Phone') }}" :required="true" />
            <x-admin.form.new-input type="phone" id="restaurant_phone" name="address[phone]"
                                    value="{{ old('address.phone', $data->address->phone) }}" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_account_number" value="{{ __('admin.Account number') }}" />
            <x-admin.form.new-input type="bank-account" id="restaurant_account_number" name="account_number"
                                    value="{{ old('account_number', $data->account_number) }}" />
        </div>
        <div>
            <x-admin.form.label for="restaurant_provision" value="{{ __('admin.Provision') }}" :required="true" />
            <x-admin.form.new-input type="percent" id="restaurant_provision" name="provision"
                                    value="{{ old('provision', $data->provision) }}" :required="true" />
        </div>
        @php
            $tempId = old('temp_gallery_id', \Illuminate\Support\Str::password(10, true, false, false));
        @endphp
        <div>
            <x-admin.form.label for="restaurant_file" value="{{ __('admin.Logo') }}" :required="true" />
            <x-admin.form.gallery
                :id="$data->id ?? $tempId"
                name="logo"
                :additional-data="['file_type' => 'logo']"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                namespace="restaurants"
                :required="true"
                requiredText="{{ __('validation.image_required') }}"
                requiredSingleText="{{ __('validation.single_image_required') }}"
                :id="$data->id ?? $tempId"
                aspect-ratio="square"
            />
        </div>
        <div>
            <x-admin.form.label for="restaurant_file" value="{{ __('admin.Background image') }}" :required="true" />
            <x-admin.form.gallery
                :additional-data="['file_type' => 'bg_image']"
                name="bg_image"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                namespace="restaurants"
                :required="true"
                requiredText="{{ __('validation.image_required') }}"
                requiredSingleText="{{ __('validation.single_image_required') }}"
                :id="$data->id ?? $tempId"
                aspect-ratio="rectangle"
            />
        </div>
        <div>
            <x-admin.form.label for="restaurant_file" value="{{  __('settings.value.dish_default_image') }}"
                                :required="true" />
            <x-admin.form.gallery
                :additional-data="['file_type' => 'dish_default_image']"
                name="dish_default_image"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                namespace="restaurants"
                :required="true"
                requiredText="{{ __('validation.image_required') }}"
                requiredSingleText="{{ __('validation.single_image_required') }}"
                :id="$data->id ?? $tempId"
                aspect-ratio="rectangle"
            />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
