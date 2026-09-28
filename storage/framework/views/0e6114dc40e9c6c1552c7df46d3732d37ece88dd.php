<div class="row">
    <div class="col-xxl-12 col-lg-12">
        <div class="card card-height-100 mb-3  pt-3">
            <div class="card-header">
                <h5 class="card-title">Connected and Missed Calls by User</h5>
            </div>
            
            <div id="callConnectionChart" style="width: 100%; height: 400px;"></div>

        </div>
    </div>
</div>

<script>
    // Prepare data for the chart
    const userName = <?php echo json_encode(array_keys($report)); ?>;
    const connectedCalls = <?php echo json_encode(array_column($report, 'connected_calls')); ?>;
    const missedCalls = <?php echo json_encode(array_column($report, 'missed_calls')); ?>;

    // Initialize the ECharts instance
    const chartDomv2 = document.getElementById('callConnectionChart');
    const myChartv2 = echarts.init(chartDomv2);

    // Set the chart options
    let options = {
        // title: {
        //     text: 'Connected and Missed Calls by User (Last 7 Days)',
        //     left: 'center'
        // },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['Connected Calls', 'Missed Calls'],
            top: 'bottom'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: userName,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Call Count'
        },
        series: [
            {
                name: 'Connected Calls',
                type: 'bar',
                data: connectedCalls,
                itemStyle: {
                    color: '#4CAF50' // Green for connected calls
                }
            },
            {
                name: 'Missed Calls',
                type: 'bar',
                data: missedCalls,
                itemStyle: {
                    color: '#F44336' // Red for missed calls
                }
            }
        ],
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
    myChartv2.setOption(options);

    // Make the chart responsive
    window.addEventListener('resize', () => {
        myChartv2.resize();
    });
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_call_connections_agents.blade.php ENDPATH**/ ?>