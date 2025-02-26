@props(['label' => '', 'items' => []])

<div class="flex flex-col justify-start items-start px-2 py-4" x-data="{ expanded: false }">
    <label class="text-2xl cursor-pointer font-semibold text-gray-900 dark:text-gray-50 flex justify-between w-full" @click="expanded = !expanded" >
        <span>{{$label}} ({{ count($items) }})</span>
        <span class="text-sm">
            <svg x-show="!expanded" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6 9l6 6l6 -6"></path>
             </svg>
             <svg x-show="expanded" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6 15l6 -6l6 6"></path>
             </svg>
        </span>
    </label>
    <div class="flex flex-col justify-start items-start gap-6 mt-2 w-full" x-show="expanded">
        @foreach ($items as $item)
            <x-admin.layout.result-item :item="$item" />
        @endforeach
    </div>
</div>
