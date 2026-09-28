<div class="row">
    <div class="col-xxl-12 col-lg-12">

        <div class="card card-height-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Listing Lead By Status</h5>
            </div>
            <div id="listingLeadStatusChart" data-set="" style="width: 100%; height: 600px;"></div>
        </div>
    </div>
</div>


<script>
    const chartDom5 = document.getElementById('listingLeadStatusChart');
    const listingLeadStatusChart = echarts.init(chartDom5);

    // Data for the chart
    const userNames5 = {!! json_encode(array_keys($report)) !!};
    const statusData5 = {!! json_encode($report) !!};

    // Get all possible statuses dynamically
    const allStatuses5 = [...new Set(Object.values(statusData5).flatMap(statusCounts => Object.keys(statusCounts)))];

    // Prepare series data for each status
    const seriesData5 = allStatuses5.map(status => ({
        name: status,
        type: 'bar',
        stack: 'total',
        data: userNames5.map(userName => statusData5[userName][status] || 0)
    }));

    const option5 = {
        title: {
            text: 'Listing Lead Status by User',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: allStatuses5,
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
            data: userNames5,
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
        series: seriesData5
    };

    listingLeadStatusChart.setOption(option5);

    // Make chart responsive
    window.addEventListener('resize', () => {
        listingLeadStatusChart.resize();
    });
</script>
