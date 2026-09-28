@extends('admin.layouts.master')
@section('title')
    Listing Insights
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css"/>


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <style>
        .graph-container {
            height: 250px;
        }

        .card-header {
            padding-bottom: 0 !important;
        }

        .select2-selection__rendered {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 100%;
            box-sizing: border-box;
        }
        .select2-results__option.hide {
            display: none;
        }
    </style>

@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Listing Insights</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reporting</a></li>
                            <li class="breadcrumb-item active">Listing Insights</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include("admin.reports.v5.partials._filter")

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card row-eq-height">
                <div class="card-body">
                    Online Listings
                    <h3>1324</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card row-eq-height">
                <div class="card-body">
                    Rent
                    <h3>1324</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card row-eq-height">
                <div class="card-body">
                    Sale
                    <h3>1324</h3>
                </div>
            </div>
        </div>
    </div>

    <h4>Statistics</h4>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>80% Quality Score</h4>
                        </div>
                        <div>
                            <h4>80% Verified Listings</h4>
                        </div>
                        <div>
                            <h4>80% SuperAgents' Listings</h4>
                        </div>
                        <div>
                            <h4>80% Exposure</h4>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    Online Listings
                                </div>
                                <div class="card-body"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    Leads
                                </div>
                                <div class="card-body"></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    Listing Leads
                                </div>
                                <div class="card-body"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    Listing Leads Exposure
                                </div>
                                <div class="card-body"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 mt-2">
            <div class="card row-eq-height">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="flex-grow-0">
                            <h5 class="card-title">Communities Insights</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="graph-container" id="calls-week-day-bar-chart"></div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>


    @section('global_scripts')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @endsection

    @yield('pageDT')
    @stack('in_page_scripts')
    <script>

        let duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';
        let groupIds = [];

        $('input[name="group_id[]"]').each(function () {
            groupIds.push($(this).val());
        });

        $(document).ready(function () {
            $('#date-filter').daterangepicker({
                maxDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $("#sidebarReports").addClass('collapse show');
            $('#sidebarListingInsightsReports').addClass('active');
            $("#reportsActive").addClass('active');
        });

        function renderLeadSourceOverview(){
            let myChart = echarts.init(document.getElementById("lead-source-overview-chart"));

            const data = [
                { value: 335, name: 'A' },
                { value: 310, name: 'B' },
                { value: 234, name: 'C' },
                { value: 135, name: 'D' },
                { value: 1548, name: 'E' },
            ];
            const sum = data.reduce((prev, current) => prev + current.value, 0);

            option = {
                tooltip: {
                    trigger: 'item'
                },
                series: [
                    {
                        name: 'Leads',
                        type: 'pie',
                        radius: ['50%', '70%'],
                        avoidLabelOverlap: true,
                        label: {
                            color: '#000',
                            fontSize: '20',
                            position: 'center',
                            formatter: () => {
                                return sum; // Use sum variable here
                            },
                        },
                        labelLine: {
                            show: false,
                        },
                        data: data,
                    },
                ],
            };

            myChart.setOption(option);
        }

        function renderLeadSourceWiseLine(){
            let myChart = echarts.init(document.getElementById("all-lead-source-wise-line-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                legend: {},
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        // axisLabel: {
                        //   formatter (value) {
                        //     return ``;
                        //   }
                        // }
                    },
                ],
                series: [
                    {
                        name: 'Incoming',
                        type: 'line',
                        data: [150, 230, 224, 218, 135, 147, 260],
                        color: "green",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    }
                ],
                dataZoom: [{
                    type: 'slider',
                    start: 0,
                    end: 29,
                    showDataShadow: true,
                    showDetail: false
                }]
            };

            myChart.setOption(option);
        }

        function renderAllCallsLineChart(){
            let myChart = echarts.init(document.getElementById("all-calls-line-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                legend: {},
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        // axisLabel: {
                        //   formatter (value) {
                        //     return ``;
                        //   }
                        // }
                    },
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'line',
                        data: [10, 230, 24, 28, 135, 17, 260],
                        color: "green",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    },
                    {
                        name: 'Missed/Answered',
                        type: 'line',
                        data: [150, 230, 224, 218, 135, 147, 260],
                        color: "blue",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    }
                ]
            };

            myChart.setOption(option);
        }
        function renderCallsWeekDayBarChart(){
            let myChart = echarts.init(document.getElementById("calls-week-day-bar-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {},
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'bar',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: [60, 72, 71, 74, 190, 130, 110]
                    },
                    {
                        name: 'Missed/Answered',
                        type: 'bar',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: [62, 82, 91, 84, 109, 110, 120]
                    }
                ]
            };

            myChart.setOption(option);
        }
        function renderCallsPerHourBarChart(){
            let myChart = echarts.init(document.getElementById("calls-per-hour-bar-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                legend: {},
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        // axisLabel: {
                        //   formatter (value) {
                        //     return ``;
                        //   }
                        // }
                    },
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'line',
                        data: [10, 230, 24, 28, 135, 17, 260],
                        color: "green",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    },
                    {
                        name: 'Missed/Answered',
                        type: 'line',
                        data: [150, 230, 224, 218, 135, 147, 260],
                        color: "blue",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    }
                ]
            };

            myChart.setOption(option);
        }

        function renderWALeadsLineChart(){
            let myChart = echarts.init(document.getElementById("wa-leads-per-day-line-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                legend: {},
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        // axisLabel: {
                        //   formatter (value) {
                        //     return ``;
                        //   }
                        // }
                    },
                ],
                series: [
                    {
                        name: 'Incoming',
                        type: 'line',
                        data: [150, 230, 224, 218, 135, 147, 260],
                        color: "green",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    }
                ]
            };

            myChart.setOption(option);
        }
        function renderWALeadsWeekDayBarChart(){
            let myChart = echarts.init(document.getElementById("wa-leads-week-day-bar-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {},
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'bar',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: [60, 72, 71, 74, 190, 130, 110]
                    }
                ]
            };

            myChart.setOption(option);
        }
        function renderWALeadsPerHourBarChart(){
            let myChart = echarts.init(document.getElementById("wa-leads-per-house-bar-chart"));

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                legend: {},
                xAxis: [
                    {
                        type: 'category',
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        // axisLabel: {
                        //   formatter (value) {
                        //     return ``;
                        //   }
                        // }
                    },
                ],
                series: [
                    {
                        name: 'Leads Per Hour',
                        type: 'line',
                        data: [150, 230, 224, 218, 135, 147, 260],
                        color: "green",
                        symbol: 'none',
                        tooltip: {
                            valueFormatter (value) {
                                return `${value}}`;
                            }
                        },
                    }
                ]
            };

            myChart.setOption(option);
        }

        renderLeadSourceOverview();
        renderLeadSourceWiseLine();

        renderAllCallsLineChart();
        renderCallsWeekDayBarChart();
        renderCallsPerHourBarChart();

        renderWALeadsLineChart();
        renderWALeadsWeekDayBarChart();
        renderWALeadsPerHourBarChart();
    </script>
@endsection
