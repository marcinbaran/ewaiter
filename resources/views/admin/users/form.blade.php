<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.users.update', ['user' => $data->id]) : route('admin.users.store');
    @endphp

    <x-admin.form.form id="users" formWide="w-1/2" role="form" method="POST" enctype="multipart/form-data"
                       :action="$action" :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        @csrf
        <div>
            <x-admin.form.label for="user-first_name" value="{{ __('admin.users.first_name') }}" :required="true" />
            <x-admin.form.new-input type="text" name="first_name" id="user-first_name"
                                    value="{{ old('first_name', $data->first_name) }}" :required="true" min="3"
                                    max="100" />
        </div>
        <div>
            <x-admin.form.label for="user-last_name" value="{{ __('admin.users.last_name') }}" />
            <x-admin.form.new-input type="text" name="last_name" id="user-last_name"
                                    value="{{ old('last_name', $data->last_name) }}" min="3" max="100" />
        </div>
        <div>
            <x-admin.form.label for="user-login" value="{{ __('admin.users.login') }}" />
            <x-admin.form.new-input type="text" name="login" id="user-login"
                                    value="{{ old('login', $data->login) }}" min="3" max="100" />
        </div>
        <div>
            <x-admin.form.label for="user-email" value="{{ __('admin.users.email') }}" :required="true" />
            <x-admin.form.new-input type="email" name="email" id="user-email"
                                    value="{{ old('email', $data->email) }}" :required="true" min="3" max="100" />
        </div>
        <div>
            <x-admin.form.label for="user-phone" value="{{ __('admin.users.phone') }}" :required="true" />
            <x-admin.form.new-input type="phone" name="phone" id="user-phone" value="{{ old('phone', $data->phone) }}"
                                    :required="true" />
        </div>
        <div>
            <x-admin.form.label for="user-roles" value="{{ __('admin.users.roles') }}" :required="true" />
            <x-admin.form.new-input type="select" id="user-roles" name="roles[][id]" :oldValue="$oldRoles"
                                    :value="route('admin.users.roles', ['id' => $data->id])" mode="multiple"
                                    :required="true" />
        </div>
        <div>
            <x-admin.form.new-input type="toggle" id="user-blocked" name="blocked"
                                    :checked="old('blocked', $data->blocked)"
                                    :placeholder="__('admin.users.is_user_blocked')" />
        </div>
        <div class="{{ $data->hasRole(App\Models\User::ROLE_TABLE) ? '' : 'hidden' }}">
            <x-admin.form.new-input type="toggle" id="user-isRoom" name="isRoom"
                                    :checked="old('isRoom', $data->isTable)"
                                    :placeholder="__('admin.users.allow_ordering_to_room')" />
        </div>
        <div>
            <x-admin.form.label for="user-password" value="{{ __('admin.users.password') }}" :required="true" />
            <x-admin.form.new-input type="password" name="password" id="user-password" :required="!$data->id" />
            @if ($data->id)
                <x-admin.form.info>{{ __('admin.users.leave_empty_to_not_change') }}</x-admin.form.info>
            @endif
        </div>
        <div>
            <x-admin.form.label for="user_password-confirmation" value="{{ __('admin.users.password_confirmation') }}"
                                :required="true" />
            <x-admin.form.new-input type="password" name="password_confirmation" id="user_password-confirmation"
                                    :required="!$data->id" />
            @if ($data->id)
                <x-admin.form.info>{{ __('admin.users.leave_empty_to_not_change') }}</x-admin.form.info>
            @endif
        </div>
        @if ($data->id && !\Hyn\Tenancy\Facades\TenancyFacade::website())
            <div>
                <x-admin.form.label for="user_points" value="{{ __('admin.users.points') }}" :required="true" />
                <x-admin.form.new-input type="text" id="user_points" :value="$data->getBalance()" disabled />
            </div>
            <div>
                <x-admin.form.label for="modify_points" value="{{ __('admin.users.add_subtract_points') }}" />
                <x-admin.form.new-input type="number-select" id="modify_points" name="modify_points" min="0" max="99999"
                                        step="1" value="0">
                    <option value="add">{{ __('admin.users.add') }}</option>
                    <option value="subtract">{{ __('admin.users.subtract') }}</option>
                </x-admin.form.new-input>
            </div>
        @endif
    </x-admin.form.form>
</x-admin.layout.admin-layout>
