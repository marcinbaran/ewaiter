
<li class="{{ $attributes['class'] }}">

    <a
        href="{{ $route }}"
        class="flex items-center p-2 text-base rounded-lg group hover:bg-gray-600 dark:hover:bg-gray-700
        @if($isActive)
        text-primary-900 hover:text-primary-500 dark:text-primary-700 dark:hover:text-primary-700
        @else
        text-gray-600 hover:text-gray-50 dark:text-gray-400 dark:hover:text-gray-50
        @endif"

    >
        <svg class="w-6 h-6
        @if($isActive)
        text-primary-900 dark:text-primary-700 group-hover:text-primary-500 dark:group-hover:text-primary-700
        @else
        text-gray-600 dark:text-gray-400 group-hover:text-gray-50 dark:group-hover:text-gray-50
        @endif"
             aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            {!! $iconPath !!}
        </svg>
        <span class="ml-3" sidebar-toggle-item>{{ $label }}</span>
    </a>
</li>
