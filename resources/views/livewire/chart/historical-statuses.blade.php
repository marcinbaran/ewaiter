
<div class="flex w-full flex-col gap-4 p-4 lg:w-1/2" data-accordion="open"
     data-active-classes="ring-2 ring-primary-900 dark:ring-primary-700"
     data-inactive-classes="ring-0 ring-transparent dark:ring-transparent">
    <div class="header flex justify-between items-center gap-4">
        <h3 class="text-xl font-semibold">{{ __('admin.Historical statuses') }}</h3>
        <button
            class="dark:hover:ring-primary-700 flex items-center gap-1 rounded-lg border border-gray-300 bg-gray-200 px-2.5 py-1 text-gray-900 hover:bg-gray-600 hover:text-gray-50 focus:border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-50 dark:hover:ring-2 sm:text-sm"
            data-accordion-target="#historical-statuses-filters-body" aria-expanded="false"
            aria-controls="historical-statuses-filters-body">
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
    <div class="hidden text-right" wire:ignore.self id="historical-statuses-filters-body">
        <div class="inline-flex flex-col gap-2">
            <x-admin.form.label for="historical-status-day" class="text-left" :required="true">{{ __('admin.Day') }}:</x-admin.form.label>
            <div class="datepicker">
                <x-admin.form.input
                    wire:model="dayFilter"
                    wire:change="onChangeFilter"
                    id="historical-status-day"
                    class="w-fit px-2.5 py-1 flatpickr"
                    type="text"
                    data-date-format="yyyy-mm-dd"
                    data-time-format="hh:ii"
                    data-enable-time="false"
                />
            </div>
        </div>
    </div>
    <x-admin.chart class="aspect-video historical-statuses-chart-container" id="historical-statuses-chart" type="pie"
                   :data="json_encode($data)"
                   datasetLegend="false" />
</div>

@section('bottomscripts')

    {{-- <script type="text/javascript">
        setTimeout(function() {
            $(document).ready(function () {
                flatpickr("#historical-status-day", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    maxDate: new Date(),
                    locale: {
                        "firstDayOfWeek": 1
                    }
                });
                $('body').on('change, paste, keyup, propertychange, input, focusout', '#historical-status-day', function(e) {
                    //window.livewire.emit("dateChanged");
                    //$('.datepicker-dropdown').addClass('hidden');
                });
            });
        }, 1000);
    </script> --}}
@append

