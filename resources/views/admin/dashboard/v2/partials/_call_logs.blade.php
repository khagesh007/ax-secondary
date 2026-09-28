<div class="row">
    <div class="col-xxl-12 col-lg-12">

        <div class="card card-height-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Last 7 Days Report</h5>
            </div>
            <canvas id="weeklyCallLogChart" data-set="" style="height: 400px;"></canvas>
        </div>
    </div>
</div>


<script>
    // Prepare data for the chart
    const datesv = {!! json_encode(array_keys($report)) !!};
    const callTypesV = {!! json_encode($callTypes) !!};
    const reportV = {!! json_encode($report) !!};

    // Prepare datasets for each call type
    const datasetsv = callTypesV.map(type => {
        const counts = datesv.map(date => reportV[date][type]);

        // Generate a random color for each call type
        const color =
            `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`;
        const backgroundColor = color.replace('1)', '0.2)');

        return {
            label: type,
            data: counts,
            borderColor: color,
            backgroundColor: backgroundColor,
            fill: false
        };
    });

    // Initialize the chart
    const ctxv = document.getElementById('weeklyCallLogChart').getContext('2d');
    new Chart(ctxv, {
        type: 'bar',
        data: {
            labels: datesv,
            datasets: datasetsv
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Call Count'
                    }
                }
            }
        }
    });
</script>
