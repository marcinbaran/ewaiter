<div>
    <x-admin.layout.delete-modal :showDeleteModal="$showDeleteModal" />
    <div class="md:flex md:justify-between mb-2 md:mb-0 px-4 md:p-0">
        <div class="w-full mb-2 md:mb-0 md:w-2/4 md:flex space-y-4 md:space-y-0 md:space-x-2">
            <div class="flex rounded-md shadow-sm">
                <input wire:model.debounce.200ms='search' type="text" placeholder="{{__('admin.Search')}}"
                       class="block w-full border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm sm:leading-5 dark:bg-gray-700 dark:text-white dark:border-gray-600  focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
            </div>
        </div>
        <div class="md:flex md:items-center space-y-4 md:space-y-0 md:space-x-2">
            <div>
                <select wire:model='perPage'
                        class="block w-full border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm sm:leading-5 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <x-admin.button class="flex" color="success" type="link"
                            href="{{ route('admin.categories.create') }}">{{ __('admin.Create') }}</x-admin.button>
        </div>
    </div>
    <div id="foodDatatable"
         class="shadow border-b border-gray-200 dark:border-gray-700 sm:rounded-lg overflow-x-auto xl:overflow-hidden">
        <table class="mt-4 min-w-full divide-y  divide-gray-200 dark:divide-none table-auto rounded-lg ">
            <thead class="bg-gray-50 rounded-lg">
            <tr class="[&>*:last-child]:rounded-tr-lg [&>*:first-child]:rounded-tl-lg">
                <th
                    class=" sm:table-cell px-6 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 uppercase tracking-wider dark:bg-gray-800 dark:text-gray-400">
                </th>
                @foreach ($headerRows as $item)
                    <th
                        class="sm:table-cell px-6 py-3 text-left text-xs font-medium whitespace-nowrap text-gray-500 uppercase tracking-wider dark:bg-gray-800 dark:text-gray-400">
                        {{ __('admin.' . $item) }}</th>

                @endforeach
            </tr>
            </thead>
            @if(count($dataTree) > 0)
                <tbody wire:loading.class.delay="opacity-50 dark:bg-gray-900 dark:opacity-60"
                       class="food-category-body "></tbody>
            @else
                <tbody>
                <tr>
                    <td colspan="9" class="space-x-2 dark:bg-gray-800">
                        <p class="text-center font-medium py-8 text-gray-400 text-lg dark:text-white">
                            {{__('admin.zero_items')}}
                        </p>
                    </td>
                </tr>
                </tbody>
            @endif
        </table>
    </div>
    <x-admin.datatable.pagination :numberOfPages="$numberOfPages" :displayingPages="$displayingPages" :page="$page"
                                  :fromItems="$fromItems"
                                  :toItems="$toItems" :numberOfItems="$numberOfItems" />
</div>
