<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.tables.update', ['table' => $data->id]) : route('admin.tables.store');
    @endphp

    <x-admin.form.form id="table" role="form" method="POST" :action="$action" enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        @csrf
        @if (!$data->id)
            <div>
                <x-admin.form.label for="adding_type" value="{{ __('admin.tables.adding_type') }}" :required="true" />
                <x-admin.form.new-input type="select" id="adding_type" name="adding_type" :required="true"
                                        :value="route('admin.tables.create_form_types')"
                                        :oldValue="old('adding_type')" />
            </div>
            <div id='adding_single' class="flex flex-col gap-6">
                <div>
                    <x-admin.form.label for="table_name" value="{{ __('admin.tables.name') }}" :required="true" />
                    <x-admin.form.new-input type="text" id="table_name" name="name" :value="old('name', $data->name)"
                                            :required="true" min="3" max="100" />
                </div>
                <div>
                    <x-admin.form.label for="table_number" value="{{ __('admin.tables.number') }}" :required="true" />
                    <x-admin.form.new-input type="text" id="table_number" name="number"
                                            :value="old('number', $data->number)" :required="true" min="1" max="100" />
                </div>
            </div>
            <div id='adding_range' class="flex flex-col gap-6">
                <div>
                    <x-admin.form.label for="table_from_number" value="{{ __('admin.tables.from_number') }}"
                                        :required="true" />
                    <x-admin.form.new-input type="number" id="table_from_number" name="from_number" min="1" max="10000"
                                            step="1" :value="old('from_number')" />
                </div>
                <div>
                    <x-admin.form.label for="table_to_number" value="{{ __('admin.tables.to_number') }}"
                                        :required="true" />
                    <x-admin.form.new-input type="number" id="table_to_number" name="to_number" min="1" max="10000"
                                            step="1" :value="old('to_number')" />
                </div>
            </div>
            <div>
                <x-admin.form.label for="table_people_number" value="{{ __('admin.tables.people_count') }}"
                                    :required="true" />
                <x-admin.form.new-input type="number" id="table_people_number" name="people_number" min="1" max="100"
                                        step="1" :required="true" :value="old('people_number', $data->people_number)" />
            </div>
            <div>
                <x-admin.form.label for="table_description" value="{{ __('admin.tables.description') }}" />
                <x-admin.form.new-input type="textarea" id="table_description" name="description" min="3" max="1000"
                                        value="{{ old('description', $data->description) }}" />
            </div>
            <div>
                <x-admin.form.new-input type="toggle" id="table_active" name="active"
                                        :checked="old('active', $data->active)"
                                        placeholder="{{ __('admin.tables.active') }}" />
            </div>
        @else
            <div>
                <x-admin.form.label for="table_name" value="{{ __('admin.tables.name') }}" :required="true" />
                <x-admin.form.new-input type="text" id="table_name" name="name" :value="old('name', $data->name)"
                                        min="3" max="100" />
            </div>
            <div>
                <x-admin.form.label for="table_number" value="{{ __('admin.tables.number') }}" :required="true" />
                <x-admin.form.new-input type="text" id="table_number" name="number"
                                        :value="old('number', $data->number)" max="100" />
            </div>
            <div>
                <x-admin.form.label for="table_people_number" value="{{ __('admin.tables.people_count') }}"
                                    :required="true" />
                <x-admin.form.new-input type="number" id="table_people_number" name="people_number" min="1" max="100"
                                        step="1" :value="old('people_number', $data->people_number)" />
            </div>
            <div>
                <x-admin.form.label for="table_description" value="{{ __('admin.tables.description') }}" />
                <x-admin.form.new-input type="textarea" id="table_description" name="description" min="3" max="1000"
                                        value="{{ old('description', $data->description) }}" />
            </div>
            <div>
                <x-admin.form.new-input type="toggle" id="table_active" name="active"
                                        :checked="old('active', $data->active)"
                                        placeholder="{{ __('admin.tables.active') }}" />
            </div>
        @endif
    </x-admin.form.form>
</x-admin.layout.admin-layout>
