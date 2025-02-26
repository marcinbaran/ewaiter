@props(['class' => '', 'name' => null, 'checkedValue' => null, 'uncheckedValue' => null, 'id' => ''])
<div class="toggle inline-flex">
    <label class="relative inline-flex cursor-pointer items-center">
        <x-admin.form.checkbox class="{{ $class }} peer sr-only" :id="$id" :name="$name" :attributes="$attributes" :checkedValue="$checkedValue" :uncheckedValue="$uncheckedValue" />
        <div
            class="peer h-6 w-11 rounded-full border
            bg-gray-200 dark:bg-gray-600 border-gray-300 dark:border-gray-700
            peer-focus:ring-primary-900 dark:peer-focus:ring-primary-700 peer-focus:ring-2
            peer-checked:bg-primary-900 peer-checked:dark:bg-primary-700 peer-checked:after:translate-x-full

            after:absolute after:left-[2px] after:top-[50%] after:h-5 after:w-5 after:-translate-y-1/2 after:rounded-full after:border after:transition-all after:content-[''] after:bg-gray-50 after:border-gray-300">
        </div>
    </label>
    <label class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center select-none" for="{{$id}}">
        {{ $slot }}
    </label>
</div>

