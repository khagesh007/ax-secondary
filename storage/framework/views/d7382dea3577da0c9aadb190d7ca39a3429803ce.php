<div class="row">
    <div class="col-xxl-12 col-lg-12">
        <div class="card card-height-100 mb-3  pt-3">
            <div class="card-header">
                <h5 class="card-title">Call Log User Report </h5>
            </div>
            
            <div id="userCallLogChart" style="width: 100%; height: 400px;"></div>

        </div>
    </div>
</div>


<script>
    // Prepare data for the chart
    const userNames = <?php echo json_encode(array_keys($report)); ?>;
    const callTypes = <?php echo json_encode($callTypes); ?>;
    const report = <?php echo json_encode($report); ?>;

    // Prepare the series data for each call type
    const seriesData = callTypes.map(type => {
        return {
            name: type,
            type: 'bar',
            stack: 'total',
            data: userNames.map(userName => report[userName][type])
        };
    });

    // Initialize the ECharts instance
    const chartDom = document.getElementById('userCallLogChart');
    const myChart = echarts.init(chartDom);

    // Set the chart options
    const option = {
        // title: {
        //     text: 'Weekly Call Log Report per User'
        // },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow' // Shows the shadow when hovering over a bar
            }
        },
        legend: {
            data: callTypes
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: userNames,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value'
        },
        series: seriesData,
      dataZoom: [
        {
          show: true,
          start: 1,
          end: 40
        },
        {
          type: 'inside',
          start: 94,
          end: 100
        },
        {
          show: true,
          yAxisIndex: 0,
          filterMode: 'empty',
          width: 30,
          height: '80%',
          showDataShadow: false,
          left: '93%'
        }
      ],
    };

    // Use the option to display the chart
    myChart.setOption(option);

    // Responsive resizing
    window.addEventListener('resize', () => {
        myChart.resize();
    });
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_call_logs_agents.blade.php ENDPATH**/ ?>