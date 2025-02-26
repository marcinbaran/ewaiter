<div
    class="editable"
    data-type="{{ $type }}"
    @if($type == 'select')
        data-options="{{ json_encode($options) }}"
    @endif
    data-url="{{ $url }}"
    data-id="{{ $id }}"
    data-model="{{ $class }}"
    data-column="{{ $column }}"
    data-toast-success="{{ __('bills.Changes successfully saved') }}"
    data-toast-danger="{{ __('bills.Something went wrong') }}"
    data-validation="{{ $validationRules }}"
>
    {!! $label !!}
</div>
