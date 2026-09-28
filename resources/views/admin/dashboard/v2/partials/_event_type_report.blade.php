<div class="row">
    <div class="col-xxl-12 col-lg-12">

        <div class="card card-height-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Event Type</h5>
            </div>
            <div id="eventTypeChart" data-set="" style="width: 100%; height: 600px;"></div>
        </div>
    </div>
</div>


<script>
    const chartDom7 = document.getElementById('eventTypeChart');
    const eventTypeChart = echarts.init(chartDom7);

    // Data for the chart
    const userNames7 = {!! json_encode(array_keys($report)) !!};
    const statusData7 = {!! json_encode($report) !!};

    // Get all possible statuses dynamically
    const allStatuses7 = [...new Set(Object.values(statusData7).flatMap(statusCounts => Object.keys(statusCounts)))];

    // Prepare series data for each status
    const seriesData7 = allStatuses7.map(status => ({
        name: status,
        type: 'bar',
        stack: 'total',
        data: userNames7.map(userName => statusData7[userName][status] || 0)
    }));

    const option7 = {
        title: {
            text: 'Event Type Report',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: allStatuses7,
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
            data: userNames7,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Lead Count'
        },
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
        ],
        series: seriesData7
    };

    eventTypeChart.setOption(option7);

    // Make chart responsive
    window.addEventListener('resize', () => {
        eventTypeChart.resize();
    });
</script>
