@props(['perPage' => 8, 'class' => ''])
<div class="hero-widget-fullscreen backdrop-blur-lg p-3 {{ $class }}" {!!$attributes!!}>
    <div class="hero-widget-fullscreen--content w-full h-full">
        <x-admin.dashboard.hero-widget :perPage="$perPage" :isFullscreen="true"/>
    </div>
    <a class="hero-widget-fullscreen--minimize p-6 absolute top-0 right-0 text-gray-900 dark:text-gray-50 hover:text-primary-900 dark:hover:text-primary-700" href="{{route('admin.dashboard.index')}}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-arrows-minimize" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 9l4 0l0 -4" /><path d="M3 3l6 6" /><path d="M5 15l4 0l0 4" /><path d="M3 21l6 -6" /><path d="M19 9l-4 0l0 -4" /><path d="M15 9l6 -6" /><path d="M19 15l-4 0l0 4" /><path d="M15 15l6 6" /></svg>
    </a>
</div>
