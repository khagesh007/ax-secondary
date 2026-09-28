<div class="row">
    <div class="col-xxl-12 col-lg-12">

        <div class="card card-height-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Listing By Status</h5>
            </div>
            <div id="propertyStatusChart" data-set="" style="width: 100%; height: 600px;"></div>
        </div>
    </div>
</div>

<script>
    // Prepare data for the chart
    const agentNames4 = <?php echo json_encode(array_keys($report)); ?>;
    const reportData4 = <?php echo json_encode($report); ?>;

    // Dynamically get all unique publish statuses
    const allStatuses4 = [...new Set(Object.values(reportData4).flatMap(statusCounts => Object.keys(statusCounts)))];

    // Define fixed colors for specific statuses (8 colors)
    const fixedColors = {
        "PUBLISHED": "#45cb85", // Example color for Status1
        "UNPUBLISHED": "#f06548", // Example color for Status2
        "DRAFT": "#4b38b3", // Example color for Status3
        "PENDING": "#ffbe0b", // Example color for Status4
        "REFUSED": "#e978601a", // Example color for Status5
        "POCKET": "#3577f1", // Example color for Status6
        "ARCHIVED": "#f065481a", // Example color for Status7
    };

    // Fallback palette for extra statuses
    const fallbackPalette = [
        "#45cb85", "#f06548", "#4b38b3", "#ffbe0b", "#f06548", "#3577f1", "#f06548", "#33FFFF"
    ];

    // Assign colors to all statuses
    const statusColors = allStatuses4.map((status, index) => fixedColors[status] || fallbackPalette[index % fallbackPalette.length]);

    // Prepare series data for each publish status
    const seriesData4 = allStatuses4.map((status, index) => ({
        name: status,
        type: 'bar',
        stack: 'total',
        data: agentNames4.map(agent => reportData4[agent][status] || 0), // Default to 0 if status is missing
        itemStyle: {
            color: statusColors[index] // Assign color for the status
        }
    }));

    const option4 = {
        // title: {
        //     text: 'Property Status by Agent',
        //     left: 'center'
        // },
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            formatter: function (params) {
                let tooltip = params[0].axisValue + '<br/>';
                params.forEach(item => {
                    tooltip += item.marker + item.seriesName + ': ' + item.data + '<br/>';
                });
                return tooltip;
            }
        },
        legend: {
            data: allStatuses4,
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
            data: agentNames4,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Property Count'
        },
        dataZoom: [
            {
                type: 'slider', // Slider for zooming
                show: true,
                xAxisIndex: [0],
                start: 0,
                end: 20
            },
            {
                type: 'inside', // Enable zooming within the chart itself
                xAxisIndex: [0],
                start: 0,
                end: 100
            }
        ],
        series: seriesData4
    };

    // Initialize the chart
    const propertyStatusChart = echarts.init(document.getElementById('propertyStatusChart'));
    propertyStatusChart.setOption(option4);

    // Make the chart responsive
    window.addEventListener('resize', () => {
        propertyStatusChart.resize();
    });
</script>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_listing_status_report.blade.php ENDPATH**/ ?>