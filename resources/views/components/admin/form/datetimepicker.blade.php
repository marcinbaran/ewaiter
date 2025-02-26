@props(['value'])
<div
    x-data
    x-init="flatpickr($refs.datetimewidget, {wrap: true, enableTime: true, dateFormat: 'Y-m-d H:i:S'});"
    x-ref="datetimewidget"
    class="flatpickr container mx-auto col-span-6 sm:col-span-6 "
>
    <div class="flex align-middle align-content-center h-8">
        <input
            x-ref="datetime"
            type="text"
            id="datetime"
            data-input
            placeholder="{{__('admin.Select date')}}"
            class="block w-full px-2 text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-l-md shadow-sm dark:text-black"
            value="{{$value}}"
        >

        <a
            class="h-8 w-8 input-button flex justify-center items-center cursor-pointer rounded-r-md bg-transparent border-gray-300 border-t border-b border-r"
            title="clear" data-clear
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="white">
                <path fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        </a>
    </div>

</div>
