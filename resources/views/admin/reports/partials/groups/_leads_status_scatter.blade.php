<!-- resources/views/leads/scatter_chart.blade.php -->

<div id="scatter-chart"></div>

@push('in_page_scripts')
<script>
    var datasets = @json($datasets);
    var options = {
        chart: {
            type: 'scatter',
            height: 350,
        },
        xaxis: {
            type: 'datetime',
        },
        yaxis: {
            title: {
                text: 'Created At Time',
            },
        },
    };

    var series = datasets.map(function(dataset) {
        return {
            name: dataset.name,
            data: dataset.data,
            color: dataset.color,
        };
    });

    options.series = series;

    var chart = new ApexCharts(document.querySelector("#scatter-chart"), options);
    chart.render();
</script>
@endpush

