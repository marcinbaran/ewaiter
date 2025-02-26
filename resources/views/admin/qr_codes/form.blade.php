<x-admin.layout.admin-layout>

    @php
        $action = $data->id ? route('admin.qr_codes.update', ['qr_code' => $data->id]) : route('admin.qr_codes.store');
    @endphp

    <x-admin.form.form
        role="form"
        id="qr_code"
        method="POST"
        :action="$action"
        enctype="multipart/form-data"
        :redirectUrl="$redirectUrl"
        formWide="w-1/2"
        class="flex flex-col gap-6"
    >
        <div>
            <x-admin.form.label value="{{ __('admin.Type') }}" for="object_type" :required="true" />
            <x-admin.form.new-input type="select" id="qr_code_type" name="object_type" :required="true">
                <option value="{{ \App\Models\QRCode::OBJECT_TYPE_ROOM }}"
                        @if (old('object_type', $data->object_type) == \App\Models\QRCode::OBJECT_TYPE_ROOM) selected @endif>
                    {{ __('admin.Room') }}
                </option>
                <option value="{{ \App\Models\QRCode::OBJECT_TYPE_TABLE }}"
                        @if (old('object_type', $data->object_type) == \App\Models\QRCode::OBJECT_TYPE_TABLE) selected @endif>
                    {{ __('admin.TABLE') }}
                </option>
                <option value="{{ \App\Models\QRCode::OBJECT_TYPE_RESTAURANT }}"
                        @if (old('object_type', $data->object_type) == \App\Models\QRCode::OBJECT_TYPE_RESTAURANT) selected @endif>
                    {{ __('admin.Restaurant') }}
                </option>
                </x-admin.form.select>
        </div>
        <div id="room_type_div">
            <x-admin.form.label value="{{ __('admin.Room') }}" for="qr_code_room" :required="true" />
            <x-admin.form.new-input type="select" id="qr_code_room" name="object_id_room" :oldValue="$oldRooms"
                                    :value="route('admin.qr_codes.rooms', ['id' => $data->id])" />
        </div>
        <div id="table_type_div">
            <x-admin.form.label value="{{ __('admin.TABLE') }}" for="qr_code_table" :required="true" />
            <x-admin.form.new-input type="select" id="qr_code_table" name="object_id_table" :oldValue="$oldTables"
                                    :value="route('admin.qr_codes.tables', ['id' => $data->id])" />
        </div>
        <div>
            <x-admin.form.new-input type="toggle" id="qr_code_redirect" name="redirect"
                                    :checked="old('redirect', $data->redirect)"
                                    placeholder="{{ __('admin.QR code register') }}" />
        </div>
    </x-admin.form.form>

    @section('bottomscripts')
        <script type="text/javascript">
            setTimeout(function() {
                function changeType() {
                    var type_val = $("#qr_code_type").val();
                    if (type_val == '{{ \App\Models\QRCode::OBJECT_TYPE_ROOM }}') {
                        $("#table_type_div").hide();
                        $("#room_type_div").show();
                        $("#qr_code_room").prop("required", true);
                        $("#qr_code_table").prop("required", false);

                    } else if (type_val == '{{ \App\Models\QRCode::OBJECT_TYPE_TABLE }}') {
                        $("#table_type_div").show();
                        $("#room_type_div").hide();
                        $("#qr_code_room").prop("required", false);
                        $("#qr_code_table").prop("required", true);

                    } else if (type_val == '{{ \App\Models\QRCode::OBJECT_TYPE_RESTAURANT }}') {
                        $("#table_type_div").hide();
                        $("#room_type_div").hide();
                        $("#qr_code_room").prop("required", false);
                        $("#qr_code_table").prop("required", false);
                    }
                }

                $(document).ready(function() {
                    changeType();

                    $(document).on("change", "#qr_code_type", function() {
                        changeType();
                    });

                    // init select2
                    function initSelect2(el) {
                        const base = $(el).data("base");
                        let options = {};

                        if ($(el).prop("multiple")) {
                            options = {
                                tags: true
                            };
                        }
                        $(el).select2($.extend({
                            ajax: {
                                url: "/admin/" + base,
                                dataType: "json",
                                processResults: function(data) {
                                    return {
                                        results: data.results,
                                        pagination: {
                                            more: data.meta.current_page < data.meta.last_page
                                        }
                                    };
                                },
                                data: function(params) {
                                    return {
                                        query_table: params.term,
                                        query_room: params.term,
                                        query_type: "select2",
                                        page: params.page || 1
                                    };
                                }
                            },
                            templateSelection: function(data) {
                                if (!data.id) {
                                    return data.text;
                                }
                                if (data.text != "") {
                                    return $(data.text);
                                }
                                var theme = getTheme(data);
                                return $(theme);
                            },
                            templateResult: function(data) {
                                if (!data.id) {
                                    return data.text;
                                }
                                var theme = getTheme(data, true);
                                return $(theme);
                            },
                            createTag: function(params) {
                                var term = $.trim(params.term);
                                if (term === "") {
                                    return null;
                                }
                                return {
                                    id: term,
                                    playerId: term
                                };
                            }
                        }, options));
                    }

                    //set default value
                    $("select[role=\"select2\"][data-value]").each(function(index, el) {

                        var id = $(el).data("value");
                        var base = $(el).data("base");
                        initSelect2(el);
                        var data = {
                            query_type: "select2"
                        };
                        if ($.isArray(id)) {
                            data.id = id.getColumn("id");
                        } else if (id > 0) {
                            data.id = [id];
                        }
                        if (!data.hasOwnProperty("id") || data.id.length == 0) {
                            return true; //continue
                        }
                        $.ajax({
                            type: "GET",
                            url: "/admin/" + base + ($.isArray(id) ? "" : "/show/" + id),
                            data: data,
                            success: function(data) {
                                if (!$.isArray(data.results)) {
                                    data.results = [data.results];
                                }
                                for (var i = 0; i < data.results.length; ++i) {
                                    var theme = getTheme(data.results[i]);
                                    var option = new Option(theme, data.results[i].id, true,
                                        true);
                                    $(el).append(option).trigger("change");
                                }
                            }
                        });
                    });

                    function getTheme(data, isDropdownList) {
                        var theme = "<div>" + (data.hasOwnProperty("name") ?
                            data.name :
                            data.id) + "</div>";
                        return theme;
                    }
                });

            }, 1000);
        </script>
    @append


</x-admin.layout.admin-layout>
