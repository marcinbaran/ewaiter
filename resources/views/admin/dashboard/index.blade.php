<x-admin.layout.admin-layout>
    <div id="dashboard-app" class="h-96 overflow-hidden">
        <hero-widget />
    </div>
    @section('navigation-header-buttons')
        <a class="open-hero-widget-fullscreen text-gray-900 dark:text-gray-50 hover:text-primary-900 dark:hover:text-primary-700"
           href="{{route('admin.dashboard.ordersFullscreen')}}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 icon icon-tabler icon-tabler-arrows-maximize"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M16 4l4 0l0 4"></path>
                <path d="M14 10l6 -6"></path>
                <path d="M8 20l-4 0l0 -4"></path>
                <path d="M4 20l6 -6"></path>
                <path d="M16 20l4 0l0 -4"></path>
                <path d="M14 14l6 6"></path>
                <path d="M8 4l-4 0l0 4"></path>
                <path d="M4 4l6 6"></path>
            </svg>
        </a>
    @endsection

</x-admin.layout.admin-layout>
