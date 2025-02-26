@props(['id', 'type' => 'pie', 'data', 'axisLabels' => null, 'datasetLegend' => "true"])

<div {!! $attributes->merge(['class' => 'chart--normal']) !!}>
    <canvas class="chart--canvas" id="{{ $id }}" data-chart-type="{{ $type }}"
        data-chart-data="{{ $data }}" data-chart-labels="{{ $axisLabels }}" data-chart-dataset-legend="{{ $datasetLegend }}" ></canvas>
</div>
