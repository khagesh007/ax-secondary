<div class="row">
    <div class="col-xxl-12 col-lg-12">
        <div class="card card-height-100 mb-3  pt-3">
            <div class="card-header">
                <h5 class="card-title">Average Call Duration per User</h5>
            </div>
            
            <div id="averageDurationChart" style="width: 100%; height: 400px;"></div>

        </div>
    </div>
</div>

<script>
    // Prepare data for the chart
    const averageDurations = <?php echo json_encode(array_column($report, 'average_duration_seconds')); ?>;
    const userNames2 = <?php echo json_encode(array_keys($report)); ?>;

    // Initialize the ECharts instance
    const chartDom2 = document.getElementById('averageDurationChart');
    const averageDurationChart = echarts.init(chartDom2);

    // Set the chart options
    const durationOption = {
        // title: {
        //     text: 'Average Call Duration per User (Last 7 Days)',
        //     left: 'center'
        // },
        tooltip: {
            trigger: 'axis',
            formatter: '{b}: {c} seconds'
        },
        xAxis: {
            type: 'category',
            data: userNames2,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Duration (seconds)'
        },
        series: [{
            name: 'Average Duration',
            type: 'bar',
            data: averageDurations,
            itemStyle: {
                color: '#3b82f6' // Blue for average duration bars
            }
        }],
        dataZoom: [
            {
                type: 'slider', // Slider for zooming
                show: true,
                xAxisIndex: [0],
                start: 0,
                end: 40
            },
            {
                type: 'inside', // Enable zooming within the chart itself
                xAxisIndex: [0],
                start: 0,
                end: 100
            }
        ]
    };

    // Use the option to display the chart
    averageDurationChart.setOption(durationOption);

    // Make the chart responsive
    window.addEventListener('resize', () => {
        averageDurationChart.resize();
    });
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_call_connections_average_agents.blade.php ENDPATH**/ ?>