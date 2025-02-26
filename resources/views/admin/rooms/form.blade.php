<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.rooms.update', ['room' => $data->id]) : route('admin.rooms.store');
    @endphp

    <x-admin.form.form id="room" role="form" method="POST" :action="$action" enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        @csrf
        @if (!$data->id)
            <div>
                <x-admin.form.label for="adding_type" value="{{ __('admin.rooms.adding_type') }}" :required="true" />
                <x-admin.form.new-input type="select" id="adding_type" name="adding_type" :value="old('adding_type')"
                                        :required="true" :value="route('admin.tables.create_form_types')"
                                        :oldValue="old('adding_type')" />
            </div>
            <div id='adding_single' class="flex flex-col gap-6">
                <div>
                    <x-admin.form.label for="room_name" value="{{ __('admin.rooms.name') }}" :required="true" />
                    <x-admin.form.new-input type="text" id="room_name" name="name" :value="old('name', $data->name)"
                                            min="3" max="100" />
                </div>
                <div>
                    <x-admin.form.label for="room_number" value="{{ __('admin.rooms.number') }}" :required="true" />
                    <x-admin.form.new-input type="text" id="room_number" name="number"
                                            :value="old('number', $data->number)" min="1" max="100" />
                </div>
            </div>
            <div id='adding_range' class="flex flex-col gap-6">
                <div>
                    <x-admin.form.label for="room_from_number" value="{{ __('admin.rooms.from_number') }}"
                                        :required="true" />
                    <x-admin.form.new-input type="number" id="room_from_number" name="from_number" min="1" max="10000"
                                            :value="old('from_number')" />
                </div>
                <div>
                    <x-admin.form.label for="room_to_number" value="{{ __('admin.rooms.to_number') }}"
                                        :required="true" />
                    <x-admin.form.new-input type="number" id="room_to_number" name="to_number" min="1" max="10000"
                                            :value="old('to_number')" />
                </div>
            </div>
            <div>
                <x-admin.form.label for="room_floor" value="{{ __('admin.rooms.floor') }}" />
                <x-admin.form.new-input type="number" id="room_floor" name="floor" min="-100" max="100"
                                        :value="old('floor', $data->floor)" />
            </div>
        @else
            <div>
                <x-admin.form.label for="room_name" value="{{ __('admin.rooms.name') }}" :required="true" />
                <x-admin.form.new-input type="text" id="room_name" name="name" :value="old('name', $data->name)"
                                        min="3" max="100" />
            </div>
            <div>
                <x-admin.form.label for="room_number" value="{{ __('admin.rooms.number') }}" :required="true" />
                <x-admin.form.new-input type="text" id="room_number" name="number" :value="old('number', $data->number)"
                                        max="100" />
            </div>
            <div>
                <x-admin.form.label for="room_floor" value="{{ __('admin.rooms.floor') }}" />
                <x-admin.form.new-input type="number" id="room_floor" name="floor" min="-100" max="100"
                                        :value="old('floor', $data->floor)" />
            </div>
        @endif
    </x-admin.form.form>
</x-admin.layout.admin-layout>
