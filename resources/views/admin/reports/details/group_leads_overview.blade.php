@extends('admin.layouts.master')
@section('title')
    Group Report
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />

    </head>
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
        .sticky-top-with-margin {
            top: 110px;
            z-index: 99999;
        }

        .sticky-top-with-margin-bottom {
            top: 170px;
            z-index: 9;
            background-color: #f5dccf !important;
        }

        .sticky-top-duration {
            top: 75px;
            z-index: 99999;
        }

        .chart-container {
            width: 100%;
            overflow-x: auto;
            /* Enable horizontal scrolling */
            white-space: nowrap;
            /* Prevent line breaks in the chart */
        }

        .chart {
            /* width: 400px; */
            overflow: scroll;
            /* Set a fixed width for the chart container */
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
@endsection
@section('content')
    <input type="hidden" name="group_id" id="group_id" value="{{ request()->group_id }}">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Group Reporting</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Leads</a></li>
                            <li class="breadcrumb-item active">Leads Overview</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('admin.reports.partials.groups._filter')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 ">
                                        <img src="{{ $group->user->thumb_url }}" alt=""
                                            class="avatar-sm rounded-circle">
                                    </div>
                                    <div class="flex-grow-1  ms-3 my-auto">
                                        <h4 class="">{{ $group->name }} |<small
                                                class="text-muted fs-15 fw-normal">{{ $group->user->full_name }}</small>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>


                        @include('admin.reports.partials.groups._group_member', ['agents' => $agents])




                    </div>
                </div>
            </div>



        </div>
    </div>
    <div id="overalldata">

    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-md-3 row-cols-1">

                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Whatsapp Response
                                    <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-whatsapp-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"> <span
                                                style="">{{ getSecondsTohumanReadableReports($whatsapp_avg, false) }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">AVERAGE PER DAY <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $averageLeadsPerDay }}">{{ $averageLeadsPerDay }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Total Leads <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-account-cash display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $leads_received }}">{{ $leads_received }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Lead Success rate <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-trophy-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $successRate }}">{{ $successRate }}</span>%
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end d-none">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    30 Days Leads <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-account-cash display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $leads_received_thirty }}">{{ $leads_received_thirty }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg d-none">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Success Rate <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-trophy-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $successRate_thirty }}">{{ $successRate_thirty }}</span>%
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Super Agent
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="{{$super_agent->thumb_url}}" alt="" class="avatar-sm rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="mb-0">{{$super_agent->full_name}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->

                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>





    <div class="row d-none leadSplit" id="leadSplit">

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4>Lead Statuses</h4>
                </div>
                <div class="card-body">
                    <div id="leadStatusChart"
                        data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                        class="apex-charts leadStatusChart" dir="ltr" data-names="" data-counts=""></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">

            <div class="card h-100">
                <div class="card-header">
                    <h4>Lead Types</h4>
                </div>
                <div class="card-body">
                    <div id="leadTypeChart"
                        data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                        class="apex-charts leadTypeChart" dir="ltr" data-names="" data-counts=""></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4>Lead Mediums</h4>
                </div>
                <div class="card-body">
                    <div id="leadSMediumsChart"
                        data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                        class="apex-charts leadSMediumsChart" dir="ltr" data-names="" data-counts=""></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4>Lead Sources</h4>
                </div>
                <div class="card-body">
                    <div id="leadSourceChart"
                        data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                        class="apex-charts leadSourceChart" dir="ltr" data-names="" data-counts=""></div>
                </div>
            </div>

        </div>
    </div>

    <div class="row d-none leadSplit">


    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Whatsapp Response Time</h5>
            </div>
            <div class="card-body">
                <table id="whatsAppResponseTable"
                    class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>

                            <th>Name</th>
                            <th>Time</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($whatsapp_avg_agents_collection as $key => $time)
                            <tr>



                                <td><span class="">{{ $key }}</span></td>
                                <td>{!! getSecondsTohumanReadableReportsTimeBase($time, false) !!}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12 d-non" id="leadsScatter">
        <div class="card">
            <div class="card-header">
                <h4>Lead Receiving Stats</h4>
            </div>
            <div class="card-body">
                {{-- <div id="leadScatterChart" class="apex-charts leadScatterChart" dir="ltr" data-names=""
                        data-counts=""></div> --}}
                <canvas id="leadScatterChart"></canvas>

            </div>
        </div>
    </div>
    </div>
    @include('admin.reports.partials.groups._loading')

@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>


    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>
@section('global_scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection

    <script>
        var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';

        $(document).ready(function() {
            propertySplit();

            $('.dateFilter').click(function() {
                var duration = $(this).val();
                var url = new URL(window.location.href);
                url.searchParams.set('duration', duration);

                window.location.href = url.toString();
            })
            $('#whatsAppResponseTable').dataTable();
            $('#datefilters').daterangepicker({
            maxDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }

        });
        })
        $(document).on('click', '.applyBtn', function() {
        var dates = $('#datefilters').val();
        dates = dates.split(' - ');
        dates[0] = dates[0].replace(/\//g, '-');
        dates[1] = dates[1].replace(/\//g, '-');
        from_date = dates[0];
        to_date = dates[1];
        var url = new URL(window.location.href);
        url.searchParams.set('duration', 'custom');
        url.searchParams.set('from', from_date);
        url.searchParams.set('to', to_date);
        window.location.href = url.toString();
    });

        function propertySplit() {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.reports.processData') }}",
                data: {
                    groupIds: $('#group_id').val(),
                    'type': 'leadsSplit',
                    'duration': duration
                },
                success: function(response) {

                    $('.leadSplit').removeClass('d-none');
                    var chartClass = 'leadStatusChart';
                    var names_1 = response.data.statusNames;
                    var counts_1 = response.data.leadCounts;
                    leadSplit(chartClass, names_1, counts_1, 'pie');




                    var chartClass_2 = 'leadSMediumsChart';
                    var names_2 = response.data.mediumNames;
                    var counts_2 = response.data.leadMediumCounts;
                    leadSplit(chartClass_2, names_2, counts_2, 'pie');



                    var chartClass_3 = 'leadSourceChart';
                    var names_3 = response.data.sourceNames;
                    var counts_3 = response.data.leadSourceCounts;
                    leadSplit(chartClass_3, names_3, counts_3, 'pie');


                    var chartClass_4 = 'leadTypeChart';
                    var names_4 = response.data.typeNames;
                    var counts_4 = response.data.leadTypeCounts;
                    leadSplit(chartClass_4, names_4, counts_4, 'pie');

                    createScatterChartAjax();

                    $('#loading-row').addClass('d-none');




                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }



        function leadSplit(chartClass, names, counts, type) {
            var colors = ['#424141', '#d3a188', '#d3a188', '#000']; // Red, Blue, Green, Yellow

            var options = {
                series: counts,
                chart: {
                    width: 380,
                    type: type,
                },
                colors: colors,
                labels: names,
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

            var chart = new ApexCharts(document.querySelector(`#${chartClass}`), options);
            chart.render();

        }


        function createScatterChartAjax() {
            $.ajax({

                type: "POST",
                url: "{{ route('admin.reports.processData') }}",
                data: {
                    groupIds: $('#group_id').val(),
                    'type': 'leadsSplitScatter',
                    'duration': duration
                },

                success: function(data) {

                    createLeadsScatter(data)
                },
                error: function(err) {
                    console.error('Error fetching scatter chart data: ', err);
                }
            });
        }

        function _createLeadsScatter(data) {
            $('#leadsScatter').removeClass('d-none');
            const options = {
                chart: {
                    type: 'scatter',
                    height: 350,

                },
                xaxis: {
                    title: {
                        text: 'Created At Time',
                    },
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    type: 'datetime',
                    title: {
                        text: 'Created At Date',
                    },
                    lines: {
                        show: true
                    }
                },
            };

            const series = [{
                name: "Lead Data",
                data: data.data.map(item => ({
                    x: new Date(item.x), // Assuming "x" is the date
                    y: item.y // Assuming "y" is the time
                }))
            }];

            options.series = series;

            const chart = new ApexCharts(document.querySelector("#leadScatterChart"), options);
            chart.render();

        }



        function createLeadsScatter(data) {
            $('#leadsScatter').removeClass('d-none');
            var formattedData = data.data.map(function(point) {
                return {
                    x: new Date(point.x),
                    y: new Date('1970-01-01T' + point.y)
                };
            });

            var ctx = document.getElementById('leadScatterChart').getContext('2d');
            var scatterChart = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Time vs Date',
                        data: formattedData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    }]
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            position: 'bottom',
                            time: {
                                unit: 'day', // Adjust the time unit as needed
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            type: 'time',
                            position: 'left',
                            time: {
                                unit: 'hour', // Adjust the time unit as needed
                            },
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            });
        }
        $("#sidebarReports").addClass('collapse show');
        $('#sidebarGroupReports').addClass('active');
        $("#reportsActive").addClass('active');
    </script>


    @yield('pageDT')
    @stack('in_page_scripts')
@endsection
