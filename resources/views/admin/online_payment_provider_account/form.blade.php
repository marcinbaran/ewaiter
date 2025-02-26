<x-admin.layout.admin-layout>
    @php
        $action = route('admin.online_payment_provider_account.store');
    @endphp
    <x-admin.form.form id="online_payment_provider_account"
                       role="form"
                       method="POST"
                       :action="$action"
                       enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl"
                       formWide="w-1/2"
                       class="flex flex-col gap-6">
        <div>
            <x-admin.form.label for="comment" value="{{ __('online_payment_provider_account.form.comment') }}"
                                :required="false" />
            <x-admin.form.new-input type="text" id="comment" name="comment" :required="false" min="3" max="100"
                                    value="{{old('comment')}}" />
        </div>
        <div>
            <x-admin.form.label for="login" value="{{ __('online_payment_provider_account.form.login') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="login" name="login" :required="true" min="3" max="255"
                                    value="{{old('login')}}" />
        </div>
        <div>
            <x-admin.form.label for="password" value="{{ __('online_payment_provider_account.form.password') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="password" name="password" :required="true" min="3" max="255"
                                    value="{{old('password')}}" />
        </div>
        <div>
            <x-admin.form.label for="api_key" value="{{ __('online_payment_provider_account.form.api_key') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="api_key" name="api_key" :required="true" min="3" max="255"
                                    value="{{old('api_key')}}" />
        </div>
        <div>
            <x-admin.form.label for="api_password" value="{{ __('online_payment_provider_account.form.api_password') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="api_password" name="api_password" :required="true" min="3" max="255"
                                    value="{{old('api_password')}}" />
        </div>
        <div>
            <x-admin.form.label for="restaurant" value="{{ __('online_payment_provider_account.form.restaurant') }}"
                                :required="true" />
            <x-admin.form.new-input type="select" id="restaurant" name="restaurant_id" :required="true"
                                    :value="route('admin.online_payment_provider_account.restaurants')"
                                    :oldValue="old('restaurant_id')" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
