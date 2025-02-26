@props(['id', 'item', 'hidden' => true, 'isExpanded' => false,'user_roles'=>[]])
<li class="sidebar-expandable-item">
    <button
        class="sidebar-expandable-item__button group flex w-full items-center rounded-lg p-2 text-base {{ $item['active'] ? 'text-primary-900 hover:text-primary-500 dark:text-primary-700 hover:bg-gray-600 dark:hover:bg-gray-700' : 'text-gray-600 dark:text-gray-400 hover:text-gray-50 dark:hover:text-gray-50 hover:bg-gray-600 dark:hover:bg-gray-700'}}"
        data-accordion-target="#{{ $id }}" aria-expanded="{{ $isExpanded ? 'true' : 'false' }}">
        <svg class="sidebar-expandable-item__icon h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
             stroke-linejoin="round">
            {!! $item['icon_path'] !!}
        </svg>
        <span class="ml-3 flex-1 whitespace-nowrap text-left">
            {{ __($item['label_key']) }}
        </span>
        <svg class="sidebar-expandable-item__arrow h-3 w-3" aria-hidden="{{ $hidden }}"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6" data-accordion-icon>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
        </svg>
    </button>
    <ul id="{{ $id }}" class="sidebar-expandable-item__list {{ $hidden? 'hidden':'' }} space-y-2 py-2">
        @foreach ($item['children'] as $id => $conf)
            @can($conf['can_key'], App\Models\User::class)
                @if (!empty(array_intersect($conf['roles'], $user_roles)))
                    <x-admin.layout.sidebar-list-item class="pl-6" :route="route($conf['route'])"
                                                      :icon-path="$conf['icon_path']"
                                                      label="{{ __($conf['label_key']) }}"
                                                      :isActive="$conf['active']" />
                @endif
            @endcan
        @endforeach
    </ul>
</li>
