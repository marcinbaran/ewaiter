@props(['route' => '', 'icon' => '', 'text' => ''])
<a href="{{$route}}" class="group flex cursor-pointer flex-row items-center justify-start gap-2 rounded p-2 text-gray-600 hover:bg-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-50">
    @if($icon == 'plus-icon')
    <x-admin.icons.plus-icon class="h-5 w-5" />
    @elseif($icon == 'filter-icon')
    <x-admin.icons.filter-icon class="h-5 w-5" />
    @endif
    <span class="text-sm">{{ __('admin.'.$text) }}</span>
</a>