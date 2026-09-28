@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

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

        .ax-grad-background {
            color: #5e5e5e !important;
            background: linear-gradient(180deg, rgb(211, 161, 136) 10%, rgb(255, 255, 255) 25%, rgb(211, 161, 136) 40%, rgb(211, 161, 136) 60%, rgb(211 161 135 / 66%) 75%, rgb(211, 161, 136) 93%);
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    @include('admin.dashboard.partials._calender_notification')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    @include('admin.dashboard.v2.partials._dropdown')
                </div>
                <div class="" id="add-sections-here"></div>
                <div class="row" id="loading-row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @includeIf('admin.dashboard.v2.partials._listings_by_area')
            </div>
        </div>
    </div>


    @include('admin.events.include.modal')
    @include('admin.dashboard._annoucement_modals')
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
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{ time() }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

    <script>
        let selectedDashboard = "{{ request('dashboard') }}";
        let date = "{{ request('date') }}";
        let filters = {
            inactiveUsers: "{{ request('inactiveUsers') }}" ? 1 : 0,
            archivedLeads: "{{ request('archivedLeads') }}" ? 1 : 0,
            deletedLeads: "{{ request('deletedLeads') }}" ? 1 : 0,
            exemptListingDateFilter: "{{ request('exemptListingDateFilter') }}" === "false" ? 0 : 1,
            userId: "{{ $selectedUserId }}",
            dashboard: "{{ request('dashboard') }}",
        };


        $(document).ready(function() {
            $('#dashboard-selector').select2({
                placeholder: 'Select Dashboard',
            });

            $('#date-filter').daterangepicker({
                maxDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY'
                },
                startDate: "{{ $startDate }}",
                endDate: "{{ $endDate }}",
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });

            $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
                let url = new URL(window.location.href);

                url.searchParams.set('date', picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate
                    .format('DD/MM/YYYY'));
                if (selectedDashboard) {
                    url.searchParams.set('dashboard', selectedDashboard);
                }
                if (filters.inactiveUsers) {
                    url.searchParams.set('inactiveUsers', true);
                }
                if (filters.archivedLeads) {
                    url.searchParams.set('archivedLeads', true);
                }
                if (filters.deletedLeads) {
                    url.searchParams.set('deletedLeads', true);
                }
                if (filters.exemptListingDateFilter) {
                    url.searchParams.delete('exemptListingDateFilter');
                } else {
                    url.searchParams.set('exemptListingDateFilter', false);
                }
                if (filters.dashboard) {
                    url.searchParams.set('dashboard', filters.dashboard);

                } else {
                    url.searchParams.delete('dashboard');
                }
                window.location.href = url.toString();
            });

            $(document).on("change", ".filter-checkbox-select", function() {
                filters[$(this).val()] = $(this).is(':checked') ? 1 : 0;
            })

            $(document).on("click", "#apply-filter-button", function() {
                let url = new URL(window.location.href);
                if (date) {
                    url.searchParams.set('date', date);
                } else {
                    url.searchParams.delete('date');
                }
                if (selectedDashboard) {
                    url.searchParams.set('dashboard', selectedDashboard);
                } else {
                    url.searchParams.delete('dashboard');
                }
                if (filters.inactiveUsers) {
                    url.searchParams.set('inactiveUsers', 1);
                } else {
                    url.searchParams.delete('inactiveUsers');
                }
                if (filters.archivedLeads) {
                    url.searchParams.set('archivedLeads', 1);
                } else {
                    url.searchParams.delete('archivedLeads');
                }
                if (filters.deletedLeads) {
                    url.searchParams.set('deletedLeads', 1);
                } else {
                    url.searchParams.delete('deletedLeads');
                }
                if (filters.exemptListingDateFilter) {
                    url.searchParams.delete('exemptListingDateFilter');
                } else {
                    url.searchParams.set('exemptListingDateFilter', false);
                }
                if (filters.dashboard) {
                    url.searchParams.set('dashboard', filters.dashboard);
                } else {
                    url.searchParams.delete('dashboard');
                }
                window.location.href = url.toString();
            })

            $("#DashActive").addClass('active');
        });

        $(document).on("change", "#dashboard-selector", function() {
            let value = $(this).val();
            if (value == "all") {
                value = ""
            }
            filters.dashboard = value;
        })

        $(document).on("click", "#apply-listings-by-area-filter-button", function() {
            $("#tableAppendTwo").html("")
            areaWiseListing();
        });


        function animateIcons() {
            var icons = $('.animate-to-right i');
            var totalIcons = icons.length;
            var fadeInDuration = 200;
            var fadeOutDuration = 200;
            var delayBetweenIcons = 700;

            function showIconsSequentially(index) {
                if (index < totalIcons) {
                    icons.eq(index).fadeIn(fadeInDuration).delay(delayBetweenIcons).fadeOut(fadeOutDuration, function() {
                        showIconsSequentially(index + 1); // Recursive call for the next icon
                    });
                } else {
                    // All icons displayed, hide all and repeat
                    setTimeout(function() {
                        icons.fadeOut(fadeOutDuration, function() {
                            showIconsSequentially(0); // Start from the first icon
                        });
                    }, delayBetweenIcons);
                }
            }

            showIconsSequentially(0);
        }

        function renderPortalLevelChart(elementId, shouldHide = false) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            let seriesData = [];
            let total = 0;
            dataset.map(item => {
                total += item.count;
                seriesData.push({
                    name: item.name,
                    value: item.count
                })
            })

            const option = {
                tooltip: {
                    trigger: 'item'
                },
                title: {
                    text: 'Total',
                    subtext: total,
                    left: 'center',
                    top: 'center',
                    textStyle: {
                        fontSize: 20,
                        fontWeight: 'bold'
                    },
                    subtextStyle: {
                        fontSize: 18,
                        fontWeight: 'normal',
                        color: '#333'
                    }
                },
                series: [{
                    type: 'pie',
                    radius: ['60%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    labelLine: {
                        show: false
                    },
                    data: seriesData
                }]
            };

            myChart.setOption(option);

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active")
            }
        }


        function renderPortalQualityChart(elementId, shouldHide = false) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            const option = {
                tooltip: {
                    trigger: 'item'
                },
                series: [{
                    type: 'gauge',
                    axisLine: {
                        lineStyle: {
                            width: 10,
                            color: [
                                [0.3, '#FF6E76'],
                                [0.5, '#FFAB91'],
                                [0.7, '#FDDD60'],
                                [1, '#7CFFB2']
                            ]
                        }
                    },
                    pointer: {
                        itemStyle: {
                            color: 'auto'
                        }
                    },
                    axisTick: {
                        distance: -10,
                        length: 4,
                        lineStyle: {
                            color: '#fff',
                            width: 2
                        }
                    },
                    splitLine: {
                        distance: -10,
                        length: 10,
                        lineStyle: {
                            color: '#fff',
                            width: 4
                        }
                    },
                    axisLabel: {
                        color: 'inherit',
                        distance: 15,
                        fontSize: 10
                    },
                    detail: {
                        valueAnimation: true,
                        formatter: '{value}%',
                        color: 'inherit',
                        fontSize: 15
                    },
                    data: [{
                        value: dataset
                    }]
                }]
            };

            myChart.setOption(option);

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active")
            }
        }

        function renderPortalRankBarChart(elementId, shouldHide = true) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            const option = {
                tooltip: {
                    trigger: 'item'
                },
                xAxis: {
                    type: 'category',
                    data: ['100%', '40-50%', '50-60%', '60-70%', '70-80%', '80-90%', '90-100%']
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: [dataset.rank_100, dataset.rank_40, dataset.rank_50, dataset.rank_60, dataset.rank_70,
                        dataset.rank_80, dataset.rank_90
                    ],
                    type: 'bar'
                }]
            };

            myChart.setOption(option);

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active")
            }
        }

        function renderPortalPieChart(elementId, shouldHide = true) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let seriesData = [];
            dataset.map(item => {
                seriesData.push({
                    name: item.name,
                    value: item.count
                })
            })

            let myChart = echarts.init(document.getElementById(elementId));

            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c}({d}%)'
                },
                series: [{
                    type: 'pie',
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        formatter: '{b}: {c}({d}%)'
                    },
                    labelLine: {
                        show: false
                    },
                    data: seriesData
                }]
            };

            myChart.setOption(option);

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active")
            }
        }

        //Lead Insights Starts
        function renderLeadOverviewSourceWiseChart() {
            if (!document.getElementById("lead-insights-lead-source-wise-overview-chart")) {
                return;
            }


            let dataset = $("#lead-insights-lead-source-wise-overview-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("lead-insights-lead-source-wise-overview-chart"));

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b} : {c} ({d}%)'
                },
                series: [{
                    type: 'pie',
                    radius: '70%',
                    label: {
                        show: true,
                        formatter: '{b}: {c}({d}%)'
                    },
                    data: [{
                            name: "PF",
                            value: dataset.pf_count,
                            source: "pf"
                        },
                        {
                            name: "Bayut",
                            value: dataset.bayut_count,
                            source: "bayut"
                        },
                        {
                            name: "Dubizzle",
                            value: dataset.dubizzle_count,
                            source: "dubizzle"
                        },
                        {
                            name: "Other",
                            value: dataset.other_count,
                            source: "other"
                        }
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            };

            myChart.setOption(option);

            myChart.on("click", function(params) {
                if (params.seriesType === "pie") {
                    const source = params.data.source || "pf";
                    $(".lead-insights-lead-source-tab-pane").removeClass("show active");
                    $(`#nav-lead-insights-lead-source-${source}`).addClass("show active");

                    $(".lead-insights-lead-source-nav-link").removeClass("active");
                    $(`#nav-lead-insights-lead-source-${source}-tab`).addClass("show active");
                }
            });
        }

        function renderLeadCountDateWiseSourceWiseChart(elementId, shouldHide = false) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            let option = {
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
                xAxis: [{
                    type: 'category',
                    data: dataset.dates,
                    boundaryGap: false,
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }, ],
                series: [{
                        name: 'Email',
                        type: 'line',
                        data: dataset.email_counts,
                        color: "red",
                        symbol: 'none'
                    },
                    {
                        name: 'Calls',
                        type: 'line',
                        data: dataset.call_counts,
                        color: "blue",
                        symbol: 'none'
                    },
                    {
                        name: 'WhatsApp',
                        type: 'line',
                        data: dataset.wa_counts,
                        color: "green",
                        symbol: 'none'
                    },
                    {
                        name: 'Other',
                        type: 'line',
                        data: dataset.other_counts,
                        color: "yellow",
                        symbol: 'none'
                    },
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

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active");
            }
        }

        function renderListingLeadCountDateWiseSourceWiseChart(elementId, shouldHide = false) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            let option = {
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
                xAxis: [{
                    type: 'category',
                    data: dataset.dates,
                    boundaryGap: false,
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }, ],
                series: [{
                        name: 'Email',
                        type: 'line',
                        data: dataset.dialmydatas,
                        color: "red",
                        symbol: 'none'
                    },
                    {
                        name: 'Calls',
                        type: 'line',
                        data: dataset.ax_chats,
                        color: "blue",
                        symbol: 'none'
                    },
                    {
                        name: 'WhatsApp',
                        type: 'line',
                        data: dataset.ax_contacts,
                        color: "green",
                        symbol: 'none'
                    },
                    {
                        name: 'Other',
                        type: 'line',
                        data: dataset.other_counts,
                        color: "yellow",
                        symbol: 'none'
                    },
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

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active");
            }
        }

        function renderLeadOverviewChart() {
            if (!document.getElementById("lead-insights-lead-overview-chart")) {
                return;
            }

            let dataset = $("#lead-insights-lead-overview-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("lead-insights-lead-overview-chart"));

            const seriesData = [];

            dataset.map(item => {
                seriesData.push({
                    name: item.medium_name || "Other",
                    value: item.count || 0,
                });
            })

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b} : {c} ({d}%)'
                },
                series: [{
                    type: 'pie',
                    radius: '70%',
                    label: {
                        show: true,
                        formatter: '{b}: {c}({d}%)'
                    },
                    data: seriesData,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            };
            myChart.setOption(option);
        }

        function renderLeadCountDateWiseChart() {
            if (!document.getElementById("lead-insights-lead-count-date-wise-chart")) {
                return;
            }

            let dataset = $("#lead-insights-lead-count-date-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("lead-insights-lead-count-date-wise-chart"));

            let option = {
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
                xAxis: [{
                    type: 'category',
                    data: dataset.dates,
                    boundaryGap: false,
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }, ],
                series: [{
                        name: 'Email',
                        type: 'line',
                        data: dataset.email_counts,
                        color: "red",
                        symbol: 'none'
                    },
                    {
                        name: 'Calls',
                        type: 'line',
                        data: dataset.call_counts,
                        color: "blue",
                        symbol: 'none'
                    },
                    {
                        name: 'WhatsApp',
                        type: 'line',
                        data: dataset.wa_counts,
                        color: "green",
                        symbol: 'none'
                    },
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

        function renderLeadInsightsData() {
            renderLeadOverviewSourceWiseChart();
            renderListingLeadOverviewSourceWiseChart();
            renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-pf-count-date-wise-chart", true);
            renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-bayut-count-date-wise-chart", true);
            renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-dubizzle-count-date-wise-chart", true);
            renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-other-count-date-wise-chart", true);
            renderLeadOverviewChart();
            renderLeadCountDateWiseChart();

            renderListingLeadCountDateWiseSourceWiseChart("listing-lead-insights-lead-source-pf-count-date-wise-chart",
                true);
            renderListingLeadCountDateWiseSourceWiseChart("listing-lead-insights-lead-source-bayut-count-date-wise-chart",
                true);
            renderListingLeadCountDateWiseSourceWiseChart(
                "listing-lead-insights-lead-source-dubizzle-count-date-wise-chart", true);
            renderListingLeadCountDateWiseSourceWiseChart("listing-lead-insights-lead-source-other-count-date-wise-chart",
                true);
        }

        function renderListingLeadInsightsData() {
            renderListingLeadOverviewSourceWiseChart();

        }

        //Lead Insights Ends
        // Listing Insights Starts
        function renderListingLeadOverviewSourceWiseChart() {
            if (!document.getElementById("listing-lead-insights-lead-source-wise-overview-chart")) {
                return;
            }


            let dataset = $("#listing-lead-insights-lead-source-wise-overview-chart").data("dataset");
            console.log(dataset)
            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("listing-lead-insights-lead-source-wise-overview-chart"));

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b} : {c} ({d}%)'
                },
                series: [{
                    type: 'pie',
                    radius: '70%',
                    label: {
                        show: true,
                        formatter: '{b}: {c}({d}%)'
                    },
                    data: [{
                            name: "Dialmydata",
                            value: dataset.dialmydata,
                            source: "Dialmydata"
                        },
                        {
                            name: "ax_chat",
                            value: dataset.ax_chat,
                            source: "ax_chat"
                        },
                        {
                            name: "ax_contact",
                            value: dataset.ax_contact,
                            source: "ax_contact"
                        },
                        {
                            name: "Other",
                            value: dataset.other,
                            source: "other"
                        }
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            };

            myChart.setOption(option);

            myChart.on("click", function(params) {
                if (params.seriesType === "pie") {
                    const source = params.data.source || "pf";
                    $(".lead-insights-lead-source-tab-pane").removeClass("show active");
                    $(`#nav-lead-insights-lead-source-${source}`).addClass("show active");

                    $(".lead-insights-lead-source-nav-link").removeClass("active");
                    $(`#nav-lead-insights-lead-source-${source}-tab`).addClass("show active");
                }
            });
        }
        // Listing Insights Ends

        function renderPropertyFinderCharts() {
            renderPortalLevelChart("pf-level-chart");
            renderPortalQualityChart("pf-gauge-chart");
            renderPortalRankBarChart("pf-rank-bar-chart");
            renderPortalPieChart("pf-status-pie-chart");
            renderPortalPieChart("pf-verified-pie-chart");
            $("#pf-rank-tab").removeClass("show active");
        }

        function renderBayutCharts() {
            renderPortalLevelChart("bayut-level-chart");
            renderPortalQualityChart("bayut-gauge-chart");
            renderPortalRankBarChart("bayut-rank-bar-chart");
            renderPortalPieChart("bayut-verified-pie-chart");
            $("#bayut-rank-tab").removeClass("show active");
        }

        function renderDubizzleCharts() {
            renderPortalLevelChart("dubizzle-level-chart");
            renderPortalQualityChart("dubizzle-gauge-chart");
            renderPortalRankBarChart("dubizzle-rank-bar-chart");
            renderPortalPieChart("dubizzle-verified-pie-chart");
            $("#dubizzle-rank-tab").removeClass("show active");
        }

        $(document).on("click", ".expand-report", function() {
            var parentCol = $(this).closest(".col-xl-4");

            var dFlexItems = parentCol.find(".list-content");

            var expandedContent = $('<div class="row"></div>');
            dFlexItems.each(function() {
                var colMd3 = $('<div class="col-md-3 p-2"></div>');
                colMd3.append($(this).clone());
                expandedContent.append(colMd3);
            });
            $('#report-title').html($(this).data('title'));
            // add .list-content-style to the expanded content where .list-content is present
            expandedContent.find(".list-content").addClass("list-content-style");
            $("#put-data-here").html(expandedContent);
            $("#fullscreeexampleModal").modal("show");
            animateIcons();
        });

        function makeExtendedListItemElement(item, countSuffix = "") {
            return `<div class="col-md-3 p-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-1">
                                    <img
                                        src="${item.user_thumbnail || item.thumb_url}"
                                        alt="" class="avatar-xs rounded">
                                </div>
                                <div class="ms-2">
                                    <div class="fw-bold">${item.user_name}</div>
                                    ${item.zone_name ? item.zone_name + " | " : ""} ${item.parent_name}
                                </div>
                            </div>
                            <span class="badge bg-dark rounded-pill">${item.count}${countSuffix}</span>
                        </li>
                    </div>`
        }


        function showNumberOfLeadsInModal(data, title, countSuffix = "") {

            let showHigh = data.high_counts?.length > 0;
            let showLeast = data.least_counts?.length > 0;
            let showOverall = data.overall_counts?.length > 0;
            let showPf = data.property_finder_counts?.length > 0;
            let showBayut = data.bayut_counts?.length > 0;
            let showDubizzle = data.dubizzle_counts?.length > 0;


            let highContent = `<div class="row">`;
            data.high_counts?.map(item => {
                highContent += makeExtendedListItemElement(item, countSuffix);
            })
            highContent += "</div>";

            let leastContent = `<div class="row">`;
            data.least_counts?.map(item => {
                leastContent += makeExtendedListItemElement(item, countSuffix);
            })
            leastContent += "</div>";

            let overallContent = `<div class="row">`;
            data.overall_counts?.map(item => {
                overallContent += makeExtendedListItemElement(item, countSuffix);
            })
            overallContent += "</div>";

            let pfContent = `<div class="row">`;
            data.property_finder_counts?.map(item => {
                pfContent += makeExtendedListItemElement(item, countSuffix);
            })
            pfContent += "</div>";

            let bayutContent = `<div class="row">`;
            data.bayut_counts?.map(item => {
                bayutContent += makeExtendedListItemElement(item, countSuffix);
            })
            bayutContent += "</div>";

            let dubizzleContent = `<div class="row">`;
            data.dubizzle_counts?.map(item => {
                dubizzleContent += makeExtendedListItemElement(item, countSuffix);
            })
            dubizzleContent += "</div>";

            let tabs = "";
            let tabContent = "";

            if (showHigh) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-high-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-high-count" type="button" role="tab"
                                aria-controls="nav-extended-high-count" aria-selected="true">High
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-high-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-extended-high-count-tab"
                                >${highContent}</div>`;
            }
            if (showLeast) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-least-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-least-count" type="button" role="tab"
                                aria-controls="nav-extended-least-count" aria-selected="true">Least
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-least-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-extended-least-count-tab"
                                >${leastContent}</div>`;
            }
            if (showOverall) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-overall-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-overall-count" type="button" role="tab"
                                aria-controls="nav-extended-overall-count" aria-selected="true">Overall
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-overall-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-extended-overall-count-tab"
                                >${overallContent}</div>`;
            }
            if (showPf) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-pf-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-pf-count" type="button" role="tab"
                                aria-controls="nav-extended-pf-count" aria-selected="true">PF
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-pf-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-extended-pf-count-tab"
                                >${pfContent}</div>`;
            }
            if (showBayut) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-bayut-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-bayut-count" type="button" role="tab"
                                aria-controls="nav-extended-bayut-count" aria-selected="true">Bayut
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-bayut-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-extended-bayut-count-tab"
                                >${bayutContent}</div>`;
            }
            if (showDubizzle) {
                let isActive = tabContent === "";
                tabs += `<button class="nav-link ${isActive ? 'active' : ''}" id="nav-extended-dubizzle-count-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-extended-dubizzle-count" type="button" role="tab"
                                aria-controls="nav-extended-dubizzle-count" aria-selected="true">Dubizzle
                        </button>`;
                tabContent += `<div
                                    class="tab-pane fade ${isActive ? 'show active' : ''}"
                                    id="nav-extended-dubizzle-count"
                                    role="tabpanel"
                                    aria-labelledby="nav-dubizzle-dubizzle--count-tab"
                                >${dubizzleContent}</div>`;
            }

            let content = `<div class="nav nav-tabs" id="nav-extended-tab" role="tablist" style="width: auto">${tabs}</div>
                            <div class="tab-content">${tabContent}</div>`;

            $('#report-title').html(title);

            $("#put-data-here").html(content);
            $("#fullscreeexampleModal").modal("show");
            animateIcons();
        }


        $(document).on("click", ".expand-no-of-list", function() {
            let type = $(this).data("type");
            let title = $(this).data("title");
            $.ajax({
                type: "POST",
                url: "{{ route('root.v2.get-data') }}",
                data: {
                    type,
                    date,
                    dashboard: selectedDashboard,
                    inactiveUsers: filters.inactiveUsers,
                    archivedLeads: filters.archivedLeads,
                    deletedLeads: filters.deletedLeads,
                    exemptListingDateFilter: filters.exemptListingDateFilter,
                },
                success: function(response) {
                    if (response.data) {
                        showNumberOfLeadsInModal(response.data, title, type === "no_of_quality_scores" ?
                            "%" : "")
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        const types = {
            counters: {
                type: "counters"
            },
            callLogs: {
                type: "callLogs"
            },
            callLogsAgents: {
                type: "callLogsAgents"
            },
            callConnections: {
                type: "callConnections"
            },
            callConnectionsTime: {
                type: "callConnectionsTime"
            },
            portalListings: {
                type: "portalListings"
            },
            agentInsights: {
                type: "agentInsights"
            },
            leadInsights: {
                type: "leadInsights"
            },
            leadStatusReport: {
                type: "leadStatusReport"
            },
            listingLeadStatusReport: {
                type: "listingLeadStatusReport"
            },

            listingLeadInsights: {
                type: "listingLeadInsights"
            },
            performanceReports: {
                type: "performanceReports"
            },
            listingStatusReport: {
                type: "listingStatusReport"
            },
            eventTypesReport: {
                type: "eventTypesReport"
            }

        }

        function getNextType(currentType) {
            if (currentType === "counters") {
                return types.portalListings;
            }

            // if (currentType === "counters") {
            //     return types.portalListings;
            // }


            if (currentType === "portalListings") {
                return types.agentInsights;
            }

            if (currentType === "agentInsights") {
                return types.leadInsights;
            }
            if (currentType === "leadInsights") {
                return types.leadStatusReport;
            }

            if (currentType === "leadStatusReport") {
                return types.listingLeadStatusReport;
            }

            if (currentType === "listingLeadStatusReport") {
                return types.listingStatusReport;
            }


            if (currentType === "listingStatusReport") {
                return types.listingLeadInsights;
            }

            if (currentType === "listingLeadInsights") {
                return types.eventTypesReport;
            }

            if (currentType === "eventTypesReport") {
                return types.performanceReports;
            }
            if (currentType === "performanceReports") {
                return types.callLogs;
            }
            if (currentType === "callLogs") {
                return types.callLogsAgents;
            }

            if (currentType === "callLogsAgents") {
                return types.callConnections;
            }
            if (currentType === "callConnections") {
                return types.callConnectionsTime;
            }


            return null
        }

        function getView(type) {
            $.ajax({
                type: "POST",
                url: "{{ route('root.v2.get-view') }}",
                data: {
                    type,
                    date,
                    dashboard: selectedDashboard,
                    inactiveUsers: filters.inactiveUsers,
                    archivedLeads: filters.archivedLeads,
                    deletedLeads: filters.deletedLeads,
                    exemptListingDateFilter: filters.exemptListingDateFilter,
                },
                success: function(response) {
                    $('#add-sections-here').append(response);
                    const currentType = types[type];
                    if (currentType.type === types.portalListings.type) {
                        // renderPropertyFinderCharts();
                        // initializeBayutChart();
                        // renderBayutCharts();
                        // renderDubizzleCharts();
                    }
                    if (currentType.type === types.leadInsights.type) {
                        renderLeadInsightsData();
                    }
                    if (currentType.type === types.callLogs.type) {}
                    // console.log(currentType.type);
                    // console.log(types.listingLeadInsights.type);

                    if (currentType.type === types.listingLeadInsights.type) {
                        renderListingLeadInsightsData();
                    }
                    const nextType = getNextType(type);
                    if (nextType) {
                        getView(nextType.type);
                    } else {
                        $('#loading-row').addClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                }
            });
        }

        getView(types.counters.type)


        function formatAgent(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }


        if ($("#report_agent").hasClass("select2")) {
            $('#report_agent').select2({
                placeholder: 'Select agent',
                allowClear: true,
                focusOnOpen: false,
                templateResult: formatAgent,
                templateSelection: formatAgent
            });
        }

        if ($("#portal_id").hasClass("select2")) {
            $('#portal_id').select2({
                placeholder: 'Select Portal',
                allowClear: true,
                focusOnOpen: false,
            });
        }


        areaWiseListing();
        $(document).on("click", ".getlocationdetails", function() {
            let location_name = $(this).data('location-name');
            let agent_id = filters.userId;
            $('#tableAppendTwo').html('');
            $('#location_community_name').text(location_name);
            $('#location-loading-div').removeClass('d-none');
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 1000);

            $.ajax({
                url: "{{ route('root.v2.agent-sub-communities-details') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    location_name: location_name,
                    agent_id: agent_id,
                },
                success: function(result) {
                    $('#location-loading-div').addClass('d-none');
                    $('#tableAppendTwo').html(result);
                    $('.showCountSubCommunity').each(function() {
                        let sum = 0;
                        $('#buttons-datatables-community').find('td:nth-child(' + ($(this)
                                .index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });

                    // at .showPercentage show percentage of each column as total is in last column
                    $('.showPercentageSubCommunity').each(function() {
                        let sum = 0;
                        $('#buttons-datatables-community').find('td:nth-child(' + ($(this)
                                .index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(((sum / $('.showCountSubCommunity').last().text()) * 100)
                            .toFixed(2) + '%');
                    });

                    $('.showPercentageSubCommunity').each(function() {
                        var columnIndex = $(this).index();
                        if ($(this).text() === '0.00%') {
                            $('#buttons-datatables-community tr').find('td:eq(' + columnIndex +
                                '), th:eq(' +
                                columnIndex + ')').hide();
                        }
                    });

                    const table = new DataTable('#buttons-datatables-community', {
                        dom: 'lrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": true, // Changed to true
                        "paging": false,
                        "searching": false,
                        "sorting": true,
                        "responsive": false, // Added responsive option
                        order: []
                    });

                    $('#exportBTNs').html('');
                    table.buttons().container().appendTo('#exportBTNs');

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

                    $('.process_header').each(function() {
                        let text = $(this).text();
                        let newText = text.substring(text.indexOf('_') + 1);
                        $(this).text(newText);
                    });

                    communityWiseGraph('tableAppendTwoGraphTwo', 'Sub Community Wise Listing',
                        'location-names-sub', 'location-counts-sub', 'location-percentage-sub');
                },
                error: function(result) {
                    console.log(result);
                }
            });

        })

        $(document).on("click", ".listingStatus", function() {
            $('.listingStatus').removeClass('active');
            $(this).addClass('active');
            areaWiseListing();
        })

        $(document).on("change", "#portal_id", function() {
            areaWiseListing();
        })

        $(document).on("change", "#report_agent", function() {
            filters.userId = $(this).val();
        })



        function areaWiseListing() {
            let status = $('.listingStatus.active').data('status');
            let is_live = $('.listingStatus.active').data('is_live');
            let portal = $('#portal_id').val();
            if (status === undefined) {
                status = null;
            }
            if (is_live === undefined) {
                is_live = null;
            }
            $('#tableAppend').html('');
            $('#divLoader').removeClass('d-none')
            $.ajax({
                url: "{{ route('root.v2.agent-listing-report') }}",
                type: "POST",
                dataType: 'html',
                data: {
                    'user_id': filters.userId,
                    'status': status,
                    'is_live': is_live,
                    'portal': portal,

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
                        dom: 'lrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": true, // Changed to true
                        "paging": false,
                        "searching": false,
                        "sorting": true,
                        "responsive": false, // Added responsive option
                        order: []
                    });

                    // add export buttons in #exportBTNs
                    $('#exportBTNs').html('');
                    table.buttons().container().appendTo('#exportBTNs');

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
                    // add export buttons

                    $('#portal_id').select2({
                        placeholder: 'Select Portal',
                        allowClear: true,
                        focusOnOpen: false,
                    });

                    $('.showPercentage').each(function() {
                        var columnIndex = $(this).index();
                        if ($(this).text() === '0.00%') {
                            $('table tr').find('td:eq(' + columnIndex + '), th:eq(' +
                                columnIndex + ')').hide();
                        }
                    });


                    communityWiseGraph('tableAppendTwoGraph', 'Area Wise Listing', 'location-names',
                        'location-counts', 'location-percentage');
                }
            });
        }

        function communityWiseGraph(divid = 'tableAppendTwoGraph', title = 'Area Wise Listing', location_names_class =
            'location-names', location_counts_class = 'location-counts', location_percentage_class = 'location-percentage'
        ) {
            var chartDom = $('#' + divid)[0];
            var myChart = echarts.init(chartDom);
            var agent_name = $('.agent-name').text();
            var option;
            var locations = [];
            var locations_counts = [];
            var locations_percentage = [];

            $('.' + location_names_class).each(function() {
                let custom_name = $(this).data('title');
                locations.push(custom_name);
            });

            $('.' + location_counts_class).each(function() {
                locations_counts.push($(this).text());
            });

            $('.' + location_percentage_class).each(function() {
                locations_percentage.push($(this).text());
            });
            var graphData = [];
            for (var i = 0; i < locations_counts.length; i++) {
                if (locations_counts[i] > 0) { // Check if count is not equal to 0
                    // make percentage to 1 decimal point
                    locations_percentage[i] = parseFloat(locations_percentage[i]).toFixed(1);

                    graphData.push({
                        value: locations_counts[i],
                        name: locations[i]
                    });
                }
            }
            graphData.sort(function(a, b) {
                return b.value - a.value;
            });
            var xValues = graphData.map(item => item.name);

            option = {
                title: {
                    text: title,
                    subtext: agent_name,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis', // Use axis trigger for bar chart
                    axisPointer: {
                        type: 'shadow' // Show shadow for better visualization
                    }
                },
                legend: {
                    show: false // Hide legend for bar chart
                },
                xAxis: {
                    type: 'category',
                    data: xValues,
                    axisLabel: {
                        rotate: 45 // Rotate the labels by 45 degrees
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Listings',
                    type: 'bar',
                    data: graphData.map(item => item.value),
                    label: { // Adding label configuration
                        show: true, // Show labels
                        position: 'top', // Position labels on top of the bars
                        color: '#333', // Label text color
                        fontSize: 12, // Label font size
                        formatter: '{c}', // Format label text to display the value
                        emphasis: { // Emphasis style when hovering
                            show: true,
                            textStyle: {
                                color: '#fff', // Label text color on emphasis
                                fontSize: 14 // Label font size on emphasis
                            }
                        }
                    },
                    emphasis: {
                        focus: 'series' // Emphasize on series for better interaction
                    }
                }],
                dataZoom: [{ // Enable data zoom
                    type: 'slider',
                    start: 0,
                    end: 19, // Display only the first 10 records initially
                    showDataShadow: false,
                    showDetail: false,
                }]
            };

            option && myChart.setOption(option);
        }



        /**
         * Function to create a donut chart from the `data-dataset` attribute
         * @param {string} containerId - The ID of the container div
         */
        function createDonutChartFromDataset(containerId) {
            const chartElement = document.getElementById(containerId);
            if (!chartElement) {
                console.error(`Container with ID "${containerId}" not found.`);
                return;
            }

            // Parse the `data-dataset` attribute
            let dataset = chartElement.dataset.dataset;
            let parsedData = [];
            let totalCount = 0;

            try {
                const rawData = JSON.parse(dataset.replace(/&quot;/g, '"')); // Handle HTML-encoded quotes
                parsedData = Object.keys(rawData).map(key => ({
                    name: key,
                    value: rawData[key]
                }));
                totalCount = Object.values(rawData).reduce((sum, count) => sum + count, 0); // Calculate total count
            } catch (error) {
                console.error("Failed to parse data-dataset:", error);
                return;
            }

            // Initialize the ECharts instance
            const chart = echarts.init(chartElement);

            // Define chart options
            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b}: {c} ({d}%)' // Show name, value, and percentage in tooltip
                },
                legend: {
                    bottom: '2%',
                    left: 'center'
                },
                series: [{
                    name: 'Levels',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 10,
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: true,
                        position: 'center',
                        formatter: `{total|${totalCount}}\nTotal`,
                        rich: {
                            total: {
                                fontSize: 10,
                                fontWeight: 'bold',
                                color: '#333'
                            }
                        }
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: 10,
                            fontWeight: 'bold',
                            formatter: `{total|${totalCount}}\nTotal`, // Keep the total fixed on hover
                            rich: {
                                total: {
                                    fontSize: 10,
                                    fontWeight: 'bold',
                                    color: '#333'
                                }
                            }
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: parsedData
                }]
            };

            // Render the chart
            chart.setOption(option);
        }

        /**
         * Function to create a bar chart from the `data-dataset` attribute
         * @param {string} containerId - The ID of the container div
         */
        function createBarChartFromDataset(containerId) {
            const chartElement = document.getElementById(containerId);
            if (!chartElement) {
                console.error(`Container with ID "${containerId}" not found.`);
                return;
            }

            // Parse the `data-dataset` attribute
            let dataset = chartElement.dataset.dataset;
            console.log(dataset)
            let parsedData = [];
            let categories = [];

            try {
                const rawData = JSON.parse(dataset); // Parse the JSON data
                categories = Object.keys(rawData); // Extract categories like 0-40, 41-60, etc.
                parsedData = Object.values(rawData); // Extract the count values
            } catch (error) {
                console.error("Failed to parse data-dataset:", error);
                return;
            }

            // Initialize the ECharts instance
            const chart = echarts.init(chartElement);

            // Define chart options for a bar chart
            const option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: categories,
                    axisLabel: {
                        interval: 0, // Prevent label overlap by rotating them if necessary
                        rotate: 30
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Property Ranges',
                    type: 'bar',
                    data: parsedData,
                    itemStyle: {
                        color: '#4CAF50' // You can customize the color as needed
                    }
                }]
            };

            // Render the chart
            chart.setOption(option);
        }

        function renderPortalPieChartTruChecked(elementId) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let val1 = $(`#${elementId}`).data("val1");
            let val2 = $(`#${elementId}`).data("val2");
            let lab1 = $(`#${elementId}`).data("lab1");
            let lab2 = $(`#${elementId}`).data("lab2");



            let seriesData = [];
            seriesData.push({
                name: lab1,
                value: val1
            }, {
                name: lab2,
                value: val2
            });

            let myChart2 = echarts.init(document.getElementById(elementId));

            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c}({d}%)'
                },
                series: [{
                    type: 'pie',
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        formatter: '{b}: {c}({d}%)'
                    },
                    labelLine: {
                        show: false
                    },
                    data: seriesData
                }]
            };

            myChart2.setOption(option);


                $(`#${elementId}`).parent(".tab-pane").removeClass("show active")

        }

        // Announcement modal jQuery
        $(document).ready(function() {
            // uncomment next line for testing. This will reset the acknowledgment status
            // localStorage.setItem('announcementFourAcknowledged', 'false');
            // Get the current date
            const currentDate = new Date();
            const cutoffDate = new Date('2025-02-12'); // February 12, 2025

            // Check if the current date is before the cutoff date
            if (currentDate < cutoffDate) {
                // Check if the announcement has already been acknowledged
                if (localStorage.getItem('announcementFourAcknowledged') === 'true') {
                    $('#annoucementModel').modal('hide');
                } else {
                    $('#annoucementModel').modal('show');
                }
            } else {
                // Do not show the modal if the date is February 3, 2025, or later
                $('#annoucementModel').modal('hide');
            }

            // Handle the acknowledgment button click
            $('#acknowledge-btn').on('click', function() {
                $('#annoucementModel').modal('hide');

                if ($('#acknowledged').is(':checked')) {
                    localStorage.setItem('announcementFourAcknowledged', 'true');
                } else {
                    localStorage.setItem('announcementFourAcknowledged', 'false');
                }
            });
        });
    </script>
@endsection
