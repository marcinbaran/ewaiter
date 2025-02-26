@props(['title' => '', 'value' => '', 'id' => ''])
<li class="py-4" id="{{ $id }}">
    <div class="flex flex-col lg:flex-row items-center justify-between space-x-4">
        <p class="text-gray-500 dark:text-white  text-sm break-words leading-4">
            {{ $title }}
        </p>
        <p class="text-gray-500 dark:text-white font-semibold text-sm break-all leading-4">
            {!! $value !!}
        </p>
    </div>
</li>
