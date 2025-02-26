
<div class="flex w-full flex-col gap-4 p-4 lg:w-1/2" data-accordion="open"
     data-active-classes="ring-2 ring-primary-900 dark:ring-primary-700"
     data-inactive-classes="ring-0 ring-transparent dark:ring-transparent">
    <div class="header flex justify-between gap-4">
        <h3 class="text-xl font-semibold">{{ __('admin.Tables') }}</h3>
        <button
            class="dark:hover:ring-primary-700 flex items-center gap-1 rounded-lg border border-gray-300 bg-gray-200 px-2.5 py-1 text-gray-900 hover:bg-gray-600 hover:text-gray-50 focus:border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-50 dark:hover:ring-2 sm:text-sm"
            data-accordion-target="#tables-filters-body" aria-expanded="false"
            aria-controls="tables-filters-body">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter h-5 w-5"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                 stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path
                    d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                </path>
            </svg>
            {{ __('admin.Filters') }}
        </button>
    </div>
    <div class="hidden text-right" id="tables-filters-body">
        <div class="mr-2 inline-flex flex-col gap-2">
            <x-admin.form.label for="tables-seats-filter" class="text-left" :required="true">{{ __('admin.Seats count') }}:</x-admin.form.label>
            <x-admin.form.select wire:model="seatFilter" id="tables-seats-filter" class="w-fit px-2.5 py-1" name="tables-seats">
                <option value="all">{{ __('admin.All') }}</option>
                @foreach ($uniqueSeats as $tab)
                    <option value="{{ $tab }}">{{ $tab }}
                        {{ $tab == 1 ? __('admin.seat') : __('admin.seats') }}</option>
                @endforeach
            </x-admin.form.select>
        </div>
        <div class="inline-flex flex-col gap-2">
            <x-admin.form.label for="tables-cycle-filter" class="text-left" :required="true">{{ __('admin.Cycle') }}:</x-admin.form.label>
            <x-admin.form.select wire:model="dayFilter" id="tables-cycle-filter" class="w-fit px-2.5 py-1" name="tables-cycle">
                <option value="day">{{ __('admin.day') }}</option>
                <option value="month">{{ __('admin.month') }}</option>
                <option value="year">{{ __('admin.year') }}</option>
                <option value="all">{{ __('admin.All') }}</option>
            </x-admin.form.select>
        </div>
    </div>
    <x-admin.chart class="aspect-video tables-chart-container" id="tables-chart" type="bar" :data="json_encode($data)"
                   axisLabels="Numer stolika;Przychody"
                   datasetLegend="false" />
</div>
