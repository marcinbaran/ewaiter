<div class="flex w-full flex-col gap-4 p-4 lg:w-1/2">
    <h3 class="text-xl font-semibold">{{ __('admin.Statuses') }}</h3>
    <x-admin.chart class="aspect-video" id="statuses-chart" type="pie" :data="json_encode($data)"
                   datasetLegend="false" />
</div>
