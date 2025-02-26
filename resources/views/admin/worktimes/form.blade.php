<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.worktimes.update', ['worktime' => $data->id]) : route('admin.worktimes.store');
    @endphp
    <x-admin.form.form id="worktime" role="form" method="POST" :action="$action" enctype="multipart/form-data"
                       :redirectUrl="$redirectUrl" formWide="w-1/2" class="flex flex-col gap-6">
        <div>
            <x-admin.form.label for="worktime_type" value="{{ __('worktime.Type') }}" :required="true" />
            <x-admin.form.new-input type="select" id="worktime_type" name="type" :required="true" >
                <option value="0" {{ old('type', $data->type) == 0 ? 'selected' : '' }}>
                    {{ gtrans('worktime.Forced close') }}
                </option>
                <option value="1" {{ old('type', $data->type) == 1 ? 'selected' : '' }}>
                    {{ gtrans('worktime.Forced open') }}
                </option>
            </x-admin.form.new-input>
        </div>
        <div>
            <x-admin.form.label for="worktime_start" value="{{ __('worktime.Date') }}" :required="true" />
            <x-admin.form.new-input type="date" id="worktime_date" name="date" :required="true"
                                    value="{{ old('date', Carbon\Carbon::parse($data->date)->format('Y-m-d')) }}"
                                    min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" />
        </div>
        <div class="flex gap-6">
            <div class="min-w-0 flex-1">
                <x-admin.form.label for="worktime_start" value="{{ __('worktime.Start') }}" :required="true" />
                <x-admin.form.new-input type="time" id="worktime_start" name="start" :required="true"
                                        value="{{ old('start', Carbon\Carbon::parse($data->start)->format('H:i')) }}" />
            </div>
            <div class="min-w-0 flex-1">
                <x-admin.form.label for="worktime_start" value="{{ __('worktime.End') }}" :required="true" />
                <x-admin.form.new-input type="time" id="worktime_end" name="end" :required="true"
                                        value="{{ old('end', Carbon\Carbon::parse($data->end)->format('H:i')) }}" />
            </div>
        </div>
        <div>
            <x-admin.form.new-input type="toggle" id="worktime_all_day" name="visibility"
                                    :checked="old('visibility', $data->visibility)"
                                    placeholder="{{ __('worktime.Active') }}" />
        </div>
    </x-admin.form.form>
</x-admin.layout.admin-layout>
<script>

    document.addEventListener("DOMContentLoaded", function() {
        const worktimeTypeSelect = $('#worktime_type');

        worktimeTypeSelect.select2({
            dropdownParent: $('#worktime'),
            width: '100%',
            minimumResultsForSearch: Infinity,
            containerCssClass: 'new-input',
            selectionCssClass: 'min-w-0 flex-1 p-2.5 text-inherit bg-inherit placeholder-gray-500 dark:placeholder-gray-400 border-none focus:ring-0 focus:border-transparent disabled:text-gray-500 disabled:dark:text-gray-400 disabled:bg-gray-200 disabled:dark:bg-gray-600 disabled:cursor-not-allowed', // Klasa dla samego selecta
        });

        const selectedValue = "{{ old('type', $data->type) }}";
        if (selectedValue) {
            worktimeTypeSelect.val(selectedValue).trigger('change');
        }
    });




</script>
