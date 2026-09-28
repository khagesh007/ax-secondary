<div class="row">
    <div class="col-xxl-12 col-lg-12">

        <div class="card card-height-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Lead By Status</h5>
            </div>
            <div id="leadStatusChart" data-set="" style="width: 100%; height: 600px;"></div>
        </div>
    </div>
</div>


<script>
    const chartDom3 = document.getElementById('leadStatusChart');
    const leadStatusChart = echarts.init(chartDom3);

    // Data for the chart
    const userNames3 = <?php echo json_encode(array_keys($report)); ?>;
    const statusData = <?php echo json_encode($report); ?>;

    // Get all possible statuses dynamically
    const allStatuses = [...new Set(Object.values(statusData).flatMap(statusCounts => Object.keys(statusCounts)))];

    // Prepare series data for each status
    const seriesData3 = allStatuses.map(status => ({
        name: status,
        type: 'bar',
        stack: 'total',
        data: userNames3.map(userName => statusData[userName][status] || 0)
    }));

    const option3 = {
        title: {
            text: 'Lead Status by User',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: allStatuses,
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
            data: userNames3,
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
        series: seriesData3
    };

    leadStatusChart.setOption(option3);

    // Make chart responsive
    window.addEventListener('resize', () => {
        leadStatusChart.resize();
    });
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_lead_status_report.blade.php ENDPATH**/ ?>