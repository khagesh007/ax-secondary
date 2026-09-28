<?php $__env->startSection('title'); ?>
    Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('assets/libs/jsvectormap/jsvectormap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        #pf_rank_meter,
        #bayut_rank_meter,
        #dubbizle_rank_meter {
            max-width: 600px;
            max-height: 400px;
            /* Add other styles as needed */
        }
    </style>
    <style>
        .morris-chart {
            font-family: Arial, sans-serif;
        }

        .morris-chart .morris-label {
            font-size: 14px;
            fill: #333333;
        }

        .selection .select2-selection--single {
            width: 300px !important;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">

        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1"><?php echo e(timeGreetings()); ?>, <?php echo e(auth()->user()->name); ?>!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your properties today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0 ">
                                <form method="GET">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">

                                            <select name="agent" class="select2" id="report_agent">
                                                <?php if(request()->agent == null || request()->agent == ''): ?>
                                                    <option value="" selected>Select Agent</option>
                                                <?php endif; ?>

                                                <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option data-image="<?php echo e($agent->thumbnail); ?>"
                                                        data-image="<?php echo e($agent->thumbnail); ?>"
                                                        data-status="<?php echo e($agent->status); ?>"
                                                        <?php if(request()->agent && request()->agent == $agent->id): ?> selected <?php endif; ?>
                                                        value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?>

                                                        <?php echo e($agent->last_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" id="report_time_filter" name="dates"
                                                    class="form-control border-0 dash-filter-picker shadow"
                                                    data-provider="flatpickr" data-range-date="true"
                                                    data-date-format="d M, Y"
                                                    <?php if(request()->dates): ?> data-default-date="<?php echo e(request()->dates); ?>"
                                                       value="<?php echo e(request()->dates); ?>"
                                                         <?php else: ?>
                                                   <?php //   data-deafult-date="01 Jan 2022 to 31 Jan 2022"
                                                   ?> <?php endif; ?>>
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-auto">
                                            <button type="submit" class="btn  action-add">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card overflow-hidden">
                            <div class="card-body bg-marketplace d-flex">
                                <div class="flex-grow-0"> <img src="<?php echo e($user->image_url); ?>" alt=""
                                        style="height: 125px;" class="img-fluid">
                                </div>
                                <div class="flex-grow-1" style="margin-left: 30px;">
                                    <h4 class="fs-18 lh-base mb-0"><?php echo e($user->name); ?> </h4>
                                    <p class="mb-0 pt-1 text-muted"><?php echo e($user->roles[0]?->name); ?></p>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            Account Status
                                            <br>
                                            <?php if($user->status): ?>
                                                <span class="text-success">Active</span>
                                            <?php else: ?>
                                                <span class="text-danger">Inactive</span>
                                            <?php endif; ?>

                                        </div>
                                        <div class="col-6">
                                            Call Tracking <br>
                                            <?php if($user->call_tracking): ?>
                                                <span class="text-success">Enabled</span>
                                            <?php else: ?>
                                                <span class="text-danger">Disabled</span>
                                            <?php endif; ?>

                                        </div>


                                    </div>
                                    <div class="row mt-2">

                                        <div class="col-6">
                                            <i class="ri-telegram-fill text-info"></i> Telegram Notification <br>
                                            <?php if($user->telegram_chatid != null): ?>
                                                <span class="text-success">Enabled</span>
                                            <?php else: ?>
                                                <span class="text-danger">Disabled</span>
                                            <?php endif; ?>

                                        </div>
                                        <div class="col-6 d-none">
                                            <i class="ri-whatsapp-fill text-success"></i> Response Time <br>
                                            <span class="text-success">1min</span>
                                        </div>
                                        <div class="col-6">
                                            <i class="  ri-whatsapp-fill text-success"></i> WhatsApp Response <br>

                                        </div>
                                    </div>
                                    <div class="row mt-2">


                                        <div class="col-6">

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Published Listings


                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="1000">0</span></h4>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Leads

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="200">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Contacts

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="300">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Deals

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="2">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->



                        </div>
                    </div>
                </div>

                <div class="row">


                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Listing Overview</h4>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <div id="eventsMonthlyOverviewChart"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>

                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Agents Availblity</h4>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <div id="agentsOfficeReport"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>

                    <div class="col-xl-12">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Events Stats</h4>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <div id="eventChartByMonths"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Events Distributions</h4>
                            </div>
                            <div class="card-body">
                                <div class="row" id="divLoader">
                                    <div class="col-md-12 text-center">
                                        <div class="spinner-grow text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="tableAppend"></div>

                            </div>
                        </div>
                    </div>


                </div>

            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="<?php echo e(URL::asset('/assets/libs/chart.js/chart.js.min.js')); ?>"></script>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('/assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.js')); ?>"></script>
    <!-- dashboard init -->
    <script src="<?php echo e(URL::asset('/assets/js/pages/dashboard-ecommerce.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>


    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>


    <script>
        let isEventPage = false;
        let currentAgentObj = {};
        let dates = "<?php echo e(request()->get('dates') ?? ''); ?>";
        <?php if($user): ?>
            currentAgentObj = {
                id: <?php echo e($user->id); ?>,
            };
        <?php endif; ?>

        $(document).ready(function() {
            $.ajax({
                url: "<?php echo e(route('admin.reports.eventsOverview')); ?>",
                type: "POST",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
                    agent: "172",
                    dates: "<?php echo e(request()->dates); ?>",
                },
                success: function(result) {
                    if (result.status == '1') {
                        getEventsMonthlyOverviewBarChart(result.data.events)
                        getAgentOfficeChart(result.data.employees.in_office_agents_count, result.data
                            .employees.out_office_agents_count)
                        getEventChartByMonths(result.data.eventsMonthly.data, result.data.eventsMonthly
                            .labels)
                    }
                }
            });

            agentsEventsStats()
        })


        function getEventsOverviewBarChart() {
            var options = {
                series: [{
                    name: 'Published',
                    data: [44, 55, 57, 56, 61, 58, 63, 30, 16]
                }, {
                    name: 'Unpublished',
                    data: [76, 83, 23, 98, 37, 105, 91, 124, 24]
                }, {
                    name: 'Draft',
                    data: [76, 35, 301, 98, 82, 105, 91, 134, 34]
                }, {
                    name: 'Verified',
                    data: [76, 45, 11, 98, 47, 75, 21, 144, 94]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '20%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false,
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: 40
                },
                colors: ['#556ee6', '#f1b44c'],
                fill: {
                    opacity: 1
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                },
                yaxis: {
                    title: {
                        text: undefined
                    },
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + "K"
                        }
                    }
                },
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            stacked: false,
                        },
                        legend: {
                            show: false,
                        },
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#eventsOverviewChart"), options);
            chart.render();
        }

        function getEventsMonthlyOverviewBarChart(stats) {
            const labels = Object.keys(stats);
            const data = Object.values(stats);

            const options = {
                chart: {
                    type: 'bar'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                    }
                },
                xaxis: {
                    categories: labels
                },
                series: [{
                    data: data,
                    name: 'Count',
                }],
                colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: '100%'
                        }
                    }
                }]
            };
            const chart = new ApexCharts(document.querySelector("#eventsMonthlyOverviewChart"), options);
            chart.render();
        }


        function __getEventsMonthlyOverviewBarChart(data) {
            const labels = data.labels;
            const series = Object.keys(data.data).map(key => {
                const values = data.data[key];
                return {
                    name: key,
                    data: labels.map(label => values[label] || 0)
                };
            });

            const options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: labels,
                    title: {
                        text: 'Month'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Count'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val;
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#eventsMonthlyOverviewChart'), options);
            chart.render();
            chart.updateSeries(series);
        }

        function getAgentOfficeChart(inOffice, outOffice) {
            const options = {
                series: [inOffice, outOffice],
                chart: {
                    width: 380,
                    type: 'donut',
                },
                colors: ['#556ee6', '#f1b44c'],
                labels: ['In Office', 'Out of Office'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#agentsOfficeReport"), options);
            chart.render();
        }


        function getEventChartByMonths(data, labels) {
            const seriesData = [];
            const labelss = Object.keys(data);

            // Create an empty series array to store each event as a separate series
            const series = [];

            // Loop through each event and create a series
            for (const event in data[labels[0]]) {
                if (data[labels[0]].hasOwnProperty(event)) {
                    const eventSeries = {
                        name: event,
                        data: labels.map((label) => data[label][event] || 0) // Use 0 for missing data
                    };
                    series.push(eventSeries);
                }
            }

            // Reverse the series array to change the order
            series.reverse();

            var options = {
                series: series,
                chart: {
                    height: 350,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'date',
                    categories: labels
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                },
            };

            const chart = new ApexCharts(document.querySelector("#eventChartByMonths"), options);

            chart.render();
        }





        function agentsEventsStats() {
            $('#tableAppend').html('');
            $('#divLoader').removeClass('d-none')
            $.ajax({
                url: "<?php echo e(route('admin.reports.agent.events.report')); ?>",
                type: "POST",
                dataType: 'html',
                data: {
                    'dates': dates,
                    'user_id': currentAgentObj.id
                },
                success: function(result) {
                    $('#divLoader').addClass('d-none')
                    $('#tableAppend').html(result);

                    const table = new DataTable('#buttons-datatables', {
                        dom: 'Bfrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            // right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": false,
                        "paging": false,
                    });

                    // add export buttons in #exportBTNs
                    table.buttons().container().appendTo('#exportBTNs');
                    // append search bar in #searchBar




                    table.page.len(20).draw();
                    // $('.dataTables_length').hide();
                    // $('.dataTables_info').hide();

                    table.on('mouseenter', 'td', function() {
                        let colIdx = table.cell(this).index().column;
                        table
                            .cells()
                            .nodes()
                            .each((el) => el.classList.remove('highlight'));

                        table
                            .column(colIdx)
                            .nodes()
                            .each((el) => el.classList.add('highlight'));
                    });
                    // add export buttons



                    $('.showCount').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });



                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/reports/calendar.blade.php ENDPATH**/ ?>