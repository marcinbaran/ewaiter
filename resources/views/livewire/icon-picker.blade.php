<div x-data="{ open: false }">
    <input type="hidden" id="icon-" name="icon" id="" value="{{ $iconId }}" />
    <label id="icon" x-on:click="open = true" class="w-12 h-12 p-2.5 cursor-pointer text-sm rounded-lg flex justify-center items-center text-gray-900 bg-gray-100 placeholder-gray-600 border-gray-300 focus:border-gray-300 focus:ring-2 focus:ring-primary-900 dark:text-gray-50 dark:bg-gray-500 dark:border-gray-700 dark:placeholder-gray-400 dark:focus:ring-2 dark:focus:ring-primary-700 disabled:opacity-75 ">
        {!!$icon!!}
    </label>

    <div x-cloak x-show="open" x-on:click.away="open = false" class="w-64 h-64 rounded-lg absolute z-50 top-12 translate-x-1/2 right-1/2 border-[1px] bg-gray-200 border-gray-300 dark:text-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 flex flex-col p-1 overflow-auto">
        <input id="search-icon" wire:model="search" type="text" x-ref="search" placeholder="{{__('admin.Search')}}" class="rounded-lg border-none focus:outline-none bg-transparent focus:ring-0 " />
        <div class="w-full h-px self-center bg-gray-300 dark:bg-gray-700"></div>
        <div class="flex flex-row flex-wrap w-full h-auto  mt-4">

            @foreach ($displayIcons as $key => $icon)
            <div class=" w-12 h-12 flex justify-center items-center gap-8">
                <button type="button" wire:click="selectIcon('{{ $key }}')" class="icons-select w-full h-full flex justify-center items-center rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none @if($icon['isActive']) bg-primary-700 @endif focus:ring-2 focus:ring-primary-700" data-iconName="{!!$icon['name'] ?? ''!!}" data-iconId="{{ $key }}">
                    {!!$icon["icon"] ?? ''!!}
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>