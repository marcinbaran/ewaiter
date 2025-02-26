<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.reservations.update', ['reservation' => $data->id]) : route('admin.reservations.store');
    @endphp

    <x-admin.form.form id="reservation" role="form" method="POST" :action="$action" enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        <div>
            <x-admin.form.label for="reservation_status" value="{{ __('admin.reservations.status') }}"
                                :required="true" />
            <x-admin.form.new-input type="select" id="reservation_status" name="status" :oldValue="$oldStatuses"
                                    :value="route('admin.reservations.statuses', ['id' => $data->id])"
                                    :required="true" />
        </div>
        <div>
            <x-admin.form.label for="reservation_table" value="{{ __('admin.reservations.table') }}" :required="true" />
            <x-admin.form.new-input type="select" id="reservation_table" name="table_id" :oldValue="$oldTables"
                                    :value="route('admin.reservations.tables', ['id' => $data->id])" :required="true"
                                    :nullOption="__('reservations.do_not_assign_table')" />
        </div>
        <div>
            <x-admin.form.label for="reservation_user" value="{{ __('admin.reservations.user') }}" :required="true" />
            <x-admin.form.new-input type="select" id="reservation_user" name="user_id" :oldValue="$oldUsers"
                                    :value="route('admin.reservations.users', ['id' => $data->id])" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="reservation_name" value="{{ __('admin.reservations.first_name') }}"
                                :required="true" />
            <x-admin.form.new-input type="text" id="reservation_name" name="name" :value="old('name', $data->name)"
                                    :required="true" min="3" max="100" />
        </div>
        <div>
            <x-admin.form.label for="reservation_people_number" value="{{ __('admin.reservations.people_count') }}"
                                :required="true" />
            <x-admin.form.new-input type="number" id="reservation_people_number" name="people_number" min="1" max="100"
                                    step="1" :required="true" :value="old('people_number', $data->people_number)" />
        </div>
        <div>
            <x-admin.form.label for="reservation_start" value="{{ __('admin.reservations.start_date') }}"
                                :required="true" />
            <x-admin.form.new-input type="date-time" id="reservation_start" name="start"
                                    value="{{ old('start', Carbon\Carbon::parse($data->start)->format('Y-m-d H:i')) }}"
                                    min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" :required="true" />
        </div>
        <div>
            <x-admin.form.label for="reservation_phone" value="{{ __('admin.reservations.phone') }}" />
            <x-admin.form.new-input type="phone" id="reservation_phone" name="phone"
                                    :value="old('phone', $data->phone)" />
        </div>
        <div>
            <x-admin.form.label for="table_description" value="{{ __('admin.reservations.description') }}" />
            <x-admin.form.new-input type="textarea" id="table_description" name="description"
                                    placeholder="{{ __('admin.reservations.description_placeholder') }}" min="3"
                                    max="1000" value="{{ old('description', $data->description) }}" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
