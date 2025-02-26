<x-admin.layout.admin-layout>
    @php
        $action = $data->id ? route('admin.promotions.update', ['promotion' => $data->id]) : route('admin.promotions.store');;
    @endphp
    <x-admin.form.form id="tags" formWide="w-1/2" role="form" method="POST" :redirectUrl="$redirectUrl"
                       :action="$action"
                       enctype="multipart/form-data">
        <div class="promotion-type-tabs">
            @if (!$data->id)
                <ul class="flex gap-6 text-gray-600 dark:text-gray-400">
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.dish')}}"
                           class="p-2 flex items-center justify-center w-full h-full text-primary-900 dark:text-primary-700 border-b-2 border-primary-900 dark:border-primary-700">
                            {{ __('admin.On dish') }}
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.category')}}"
                           class="p-2 flex items-center justify-center w-full h-full hover:text-gray-900 dark:hover:text-gray-50 border-b-2 border-transparent">
                            {{ __('admin.On category') }}
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="{{route('admin.promotions.create.bundle')}}"
                           class="p-2 flex items-center justify-center w-full h-full hover:text-gray-900 dark:hover:text-gray-50 border-b-2 border-transparent">
                            {{ __('admin.On bundle') }}
                        </a>
                    </li>
                </ul>
            @endif
        </div>
        <livewire:promotions :data="$data" />
    </x-admin.form.form>

</x-admin.layout.admin-layout>
