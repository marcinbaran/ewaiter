@props(['name' => null, 'type' => 'text', 'class' => 'timepicker-ui-input', 'value' => null, 'placeholder' => null, 'isInputGroup' => false, 'id' => null, 'required' => true, 'prepend' => '', 'append' => ''])

@if(!$isInputGroup)
<div class="timepicker flex flex-row justify-start">
    <x-admin.form.input :name="$name" :type="$type" class="{{$class}} rounded-r-none" :placeholder="$placeholder" :value="$value" />
    <button type="button" class="timepicker-clear-btn text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700 border-none hover:bg-gray-200/70 dark:hover:bg-gray-700/70 outline-none rounded-r-lg px-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x w-6 h-6" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M18 6l-12 12"></path>
            <path d="M6 6l12 12"></path>
        </svg>
    </button>
</div>
@else
<div class="timepicker flex">
    <x-admin.form.input-group :id="$id" class="{{$class}} rounded-r-none"  :name="$name" :type="$type" :value="$value" :required="true" prepend='{!!$prepend!!}' :append="$append" />
    <button type="button" class="timepicker-clear-btn text-gray-900 dark:text-gray-50 bg-gray-200 dark:bg-gray-700 border-none hover:bg-gray-200/70 dark:hover:bg-gray-700/70 outline-none rounded-r-lg px-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x w-6 h-6" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M18 6l-12 12"></path>
            <path d="M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif