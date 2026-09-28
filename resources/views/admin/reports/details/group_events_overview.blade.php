@extends('admin.layouts.master')
@section('title')
    Group Report
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
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
            width: 400px;
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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Events</a></li>
                            <li class="breadcrumb-item active">Events Overview</li>
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
                        <div class="col-md-11">

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

                            {{-- <div class="avatar-md">
                                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169877580020231031221000.jpg" alt="" class="img-fluid rounded-circle">
                            </div> --}}

                        </div>
                        <div class="col-md-1">
                            <div class=" text-start" id="exportBTNs"></div>
                        </div>

                        @include('admin.reports.partials.groups._group_member', ['agents' => $agents])

                        {{--  <div class="col-md-3">
                          <select name="portal_id" id="portal_id" class="form-control select2 inputs">
                                <option value="">Select Portal</option>
                                @foreach ($portals as $portal)
                                    <option value="{{ $portal->id }}">{{ $portal->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}


                    </div>


                </div>
                <div class="card-body">

                    <div id="tableAppend"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 d-none" id="eventsByMonth">
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
    </div>



    <div class="row">
        <div class="col-xl-12 d-none" id="eventsByType">
            <div class="card" style="min-height: 346px;">
                <div class="card-header">
                    <form action="">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title mb-0">Events Counts</h4>
                            </div>
                            <div class="col-md-4">
                                <select name="" class="form-control-sm select2" id="agents_names">
                                </select>
                            </div>
                        </div>
                    </form>




                </div><!-- end card header -->

                <div class="card-body">

                    <div id="eventChartByTypes"
                        data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
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
    @section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection

    <script>
        var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';
        var agent_events = [];
        var overall_events = [];
        var total_events_count = [];
        $(document).ready(function() {
            eventsOverview();
            $('.dateFilter').click(function() {
                var duration = $(this).val();
                var url = new URL(window.location.href);
                url.searchParams.set('duration', duration);
                window.location.href = url.toString();
            })
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



        function eventsOverview() {
            let group_id = $('#group_id').val();
            $('#tableAppend').html('');
            $('#divLoader').removeClass('d-none')
            $.ajax({
                url: "{{ route('admin.reports.eventsOverviewFetch') }}",
                type: "POST",
                dataType: 'html',
                data: {

                    'group_id': group_id,
                    'duration': duration,
                },
                success: function(result) {
                    $('#divLoader').addClass('d-none')
                    $('#tableAppend').html(result);

                    $('.showCount').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });

                    // at .showPercentage show percentage of each column as total is in last column
                    $('.showPercentage').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(((sum / $('.showCount').last().text()) * 100).toFixed(2) + '%');
                    });

                    const table = new DataTable('#buttons-datatables', {
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollCollapse": false,
                        "paging": false,
                        searching: false
                    });


                    table.page.len(1000).draw();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').hide();

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
                    $('#portal_id').select2({
                        placeholder: 'Select Portal',
                        allowClear: true,
                        focusOnOpen: false,
                    });

                    getEventsByMonths();
                }
            });
        }

        function getEventsByMonths() {
            let group_id = $('#group_id').val();
            $.ajax({
                url: "{{ route('admin.reports.eventsMonthlyOverview') }}",
                type: "POST",
                dataType: 'html',
                data: {
                    'group_id': group_id,
                    'duration': duration,
                },
                success: function(result) {
                    // parse the result to JSON
                    result = JSON.parse(result);

                    var labels = result.labels;
                    var data = result.data;
                    // reverse the data array to change the order


                    $('#eventsByMonth').removeClass('d-none');
                    getEventChartByMonths(data, labels)
                    getEventsByAgents()
                }
            });
        }

        function getEventsByAgents() {
            let group_id = $('#group_id').val();
            $.ajax({
                url: "{{ route('admin.reports.processData') }}",
                type: "POST",
                dataType: 'html',
                data: {
                    'groupIds': group_id,
                    'duration': duration,
                    'type': 'eventsByAgents',
                },
                success: function(result) {
                    result = JSON.parse(result);
                    console.log(result.data.all_events_count_users)
                    agent_events = result.data.all_events_count_users;
                    console.log(agent_events['Viewing'])
                    // get keys of agent data and append to select2
                    var agentNames = Object.keys(result.data.all_events_count_users);

                    // append all in #agents_names select2
                    $('#agents_names').html('');
                    $('#agents_names').append('<option value="">Select Type</option>');
                    $('#agents_names').append('<option value="Overall">Show All By Event Type</option>');
                    $('#agents_names').append('<option value="total_count">Show All Events By Agents</option>');
                    agentNames.forEach(function(agent) {
                        $('#agents_names').append('<option value="' + agent + '">' + agent + '</option>');
                    });
                    $('#agents_names').select2({
                        placeholder: 'Select Type',
                        allowClear: true,
                        focusOnOpen: false,
                    });
                    overall_events = result.data.all_events_count;
                    total_events_count = result.data.user_total_event_counts;
                    columnChartEventTypeCount(result.data.all_events_count,'Show All By Event Type')
                    $('#loading-row').addClass('d-none');

                }
            });
        }

        $('#agents_names').change(function() {
            console.log(agent_events);
            var type = $(this).val();
            let title = 'Overall Data';
            if (type == 'Overall') {
                title = 'All By Event Type'
                columnChartEventTypeCount(overall_events, title)
            }else if(type =='total_count'){
                title = 'All Events of All Agents'
                columnChartEventTypeCount(total_events_count, title)
            } else {
                title = 'All Events of '+ type
                columnChartEventTypeCount(agent_events[type], title)
            }
        })



        function getEventChartByMonths(data, labels) {

            //

            const seriesData = [];
            const labelss = Object.keys(data);

            // Create an empty series array to store each event as a separate series
            const series = [];

            // Loop through each event and create a series in reverse order
            for (const event in data[labels[0]]) {
                if (data[labels[0]].hasOwnProperty(event)) {
                    const reversedLabels = labels.slice().reverse(); // Copy and reverse the labels array
                    const reversedData = reversedLabels.map((label) => data[label][event] || 0); // Use 0 for missing data
                    const eventSeries = {
                        name: event,
                        data: reversedData
                    };
                    series.push(eventSeries);
                }
            }

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
                    categories: labels.slice().reverse() // Copy and reverse the labels array
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


        function columnChartEventTypeCount(data,title) {
            $('#eventsByType').removeClass('d-none');
            // clear the chart container and all the previous charts
            $('#eventChartByTypes').html('');

            var eventTypes = Object.keys(data);
            var eventCounts = Object.values(data);

            // rearrange types and counts in descending order
            eventTypes.sort(function(a, b) {
                return data[b] - data[a];
            });
            eventCounts.sort(function(a, b) {
                return b - a;
            });


            var options = {
                series: [{
                    name: 'Events',
                    data: eventCounts // Use the dynamic event counts here
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },

                xaxis: {
                    categories: eventTypes, // Use the dynamic event types as categories
                    position: 'top',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: false,
                        formatter: function(val) {
                            return val;
                        }
                    }

                },
                title: {
                    text: title,
                    floating: true,
                    offsetY: 330,
                    align: 'center',
                    style: {
                        color: '#444'
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#eventChartByTypes"), options);
            chart.render();
        }


        $("#sidebarReports").addClass('collapse show');
        $('#sidebarGroupReports').addClass('active');
        $("#reportsActive").addClass('active');
    </script>

    @yield('pageDT')
    @stack('in_page_scripts')
@endsection
