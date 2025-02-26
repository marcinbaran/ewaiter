@props(['id', 'data' => null, 'locales' => null])

@php
    $myLocales = $locales ?? ($data->getLocales() ?? []);
@endphp

<div class="text-gray-600 dark:text-gray-400">
    <ul class="flex gap-2 border-gray-300 text-center text-sm font-medium dark:border-gray-700"
        data-tabs-toggle="#{{ $id }}" role="tablist">
        @foreach ($myLocales as $locale)
            <li role="presentation">
                <button
                    class="{{ $locale == 'pl' ? 'active' : '' }} aria-selected:text-primary-900 aria-selected:dark:!text-primary-700 flex items-center justify-center rounded-t-lg border-l border-r border-t border-gray-300 bg-gray-200 p-4 aria-selected:bg-gray-300 dark:!border-gray-700 dark:!bg-gray-800 aria-selected:dark:!bg-gray-700"
                    data-tabs-target="#{{ $locale }}" type="button" href="#" role="tab"
                    aria-current="page" aria-controls="{{ $locale }}"
                    @if ($locale == 'pl') aria-selected="true" @endif>
                    <img class="mr-2 inline-block h-4" src="/images/flags/{{ $locale }}.png" />
                    {{ __('admin.languages.' . $locale) }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" data-button-translation="{{__('admin.Translate')}}"
         data-locales="{{ json_encode($myLocales) }}"
         data-toast-danger="{{__("admin.Failed to translate the text")}}"
         id="{{ $id }}">
        {{ $slot }}
    </div>
</div>
