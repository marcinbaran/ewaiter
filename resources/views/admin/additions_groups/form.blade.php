<x-admin.layout.admin-layout>

    @php
        $action = ($data->id) ? route('admin.additions_groups.update', ['addition_group' => $data->id]) : route('admin.additions_groups.store');
    @endphp

    <x-admin.form.form
        role="form"
        id="additions_groups"
        method="POST"
        :action="$action"
        enctype="multipart/form-data"
        :redirectUrl="$redirectUrl"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        @csrf
        <x-admin.form.tablist :data="$data" id="myTabContent">
            @foreach ($data->getLocales() as $locale)
                <div
                    class="hidden space-y-6 rounded-lg rounded-tl-none border border-gray-300 bg-gray-200 p-4 text-gray-600 dark:!border-gray-700 dark:!bg-gray-800 dark:text-gray-400"
                    id="{{ $locale }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div>
                        <x-admin.form.label class="text-gray-600 dark:text-gray-400" value="{{ __('admin.Name') }}"
                                            for="addition_group_name_{{ $locale }}" :required="$locale == 'pl'" />
                        <x-admin.form.new-input type="text" name="{{ 'name[' . $locale . ']' }}"
                                                id="addition_group_name_{{ $locale }}" :required="$locale == 'pl'"
                                                value="{{ old('name.' . $locale, $data->getTranslation('name', $locale)) }}"
                                                min="3" max="100" />
                    </div>
                </div>
            @endforeach
        </x-admin.form.tablist>
        @if($data->id)
            <livewire:datatables.addition-group-additions class="mb-6" :addition-group-id="$data->id" />
        @endif
        <div>
            <x-admin.form.new-input type="toggle" name="mandatory" id="addition_group_mandatory"
                                    :checked="old('mandatory',$data->mandatory)"
                                    placeholder="{{ __('admin.If addition required') }}" />
        </div>
        <div class="flex gap-6">
            <div class="flex-1 flex flex-col">
                <x-admin.form.label value="{{__('admin.Food categories')}}" for="addition_group_category" />
                <x-admin.form.new-input type="select" mode="multiple" containerClass="flex-1"
                                        name="addition_group_category[][id]" id="addition_group_category"
                                        :value="route('admin.additions_groups.categories',['id' => $data->id])"
                                        :oldValue="$oldFoodCategories" />
            </div>
            <div class="flex-1 flex flex-col">
                <x-admin.form.label value="{{__('admin.Dishes')}}" for="addition_group_dish" />
                <x-admin.form.new-input type="select" mode="multiple" containerClass="flex-1"
                                        name="addition_group_dish[][id]" id="addition_group_dish"
                                        :value="route('admin.additions_groups.dishes',['id' => $data->id])"
                                        :oldValue="$oldDishes" />
            </div>
        </div>
        <div>
            <x-admin.form.label value="{{__('admin.Type')}}" for="addition_group_type" :required="true" />
            <x-admin.form.new-input type="select" name="type" id="addition_group_type"
                                    :value="route('admin.additions_groups.addition_group_types')"
                                    :oldValue="old('type', $data->type)">
            </x-admin.form.new-input>
        </div>
        <div>
            <x-admin.form.new-input type="toggle" name="visibility" id="addition_group_active"
                                    :checked="old('visibility',$data->visibility)"
                                    placeholder="{{ __('admin.Is the addition group visible?') }}" />
        </div>
    </x-admin.form.form>

    @section('bottomscripts')
        <script type="text/javascript">
            setTimeout(function() {
                $(document).ready(function() {
                    $("[role=\"tabpanel\"]").find("input,select").on("invalid", function() {
                        $("#" + $(this).closest("[role=\"tabpanel\"]").attr("aria-labelledby")).addClass("invalid");
                    }).on("change", function() {
                        if (!$(this).is(":invalid")) {
                            $("#" + $(this).closest("[role=\"tabpanel\"]").attr("aria-labelledby")).removeClass("invalid");
                        }
                    });

                    function loadTableAdditions() {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('admin.addition.modal_table') }}",
                            data: {
                                _token: '{{ csrf_token() }}',
                                addition_group_id: '{{$data->id}}'
                            },
                            dataType: "JSON",
                            success: function(response) {
                                $("#previewModal").modal("hide");
                                if (response.status == 200) {
                                    $("#mainTable").html(response.data);
                                } else if (response.error) {
                                    alert(response.error);
                                } else {
                                    alert("Problem z pobraniem listy dodatków!");
                                }

                            },
                            error: function(data) {
                            }
                        });
                    }

                    function additionAction(action, id) {
                        if (action == "delete" && !confirm("Czy na pewno usunąć?")) {
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            url: "{{ route('admin.addition.modal_action') }}",
                            data: {
                                _token: '{{ csrf_token() }}',
                                addition_group_id: '{{$data->id}}',
                                addition_id: id,
                                action: action
                            },
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status == 200) {
                                    if (action == "delete") {
                                        loadTableAdditions();
                                    } else {
                                        $("#previewModal .modal-body").html(response.data);
                                        $("#previewModal").modal("show");
                                        $("#previewModal .select2-container").css("width", "80%");
                                    }
                                } else if (response.error) {
                                    alert(response.error);
                                } else {
                                    alert("Problem z przesłaniem danych!");
                                }

                            },
                            error: function(data) {

                            }
                        });
                    }

                    function additionActionSubmit() {
                        $form = $("form#addition");
                        if ($form[0].checkValidity()) {
                            $.ajax({
                                type: "POST",
                                url: $form.attr("action"),
                                data: $form.serialize(),
                                dataType: "JSON",
                                success: function(response) {
                                    if (response.status == 200) {
                                        loadTableAdditions();
                                    } else if (response.errors) {
                                        jQuery.each(response.errors, function(key, value) {
                                            alert(value);
                                        });
                                    } else if (response.error) {
                                        alert(response.error);
                                    } else {
                                        alert("Problem z przesłaniem danych!");
                                    }

                                },
                                error: function(data) {

                                }
                            });
                        } else {
                            alert("Prosimy poprawić dane w formularzu");
                        }
                    }
                });
            }, 1000);
        </script>
    @append
</x-admin.layout.admin-layout>
