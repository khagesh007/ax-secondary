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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                            <li class="breadcrumb-item active">User Overview</li>
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
    @include('admin.reports.partials.groups._portals_meta_user')

    <div id="overalldata">

    </div>

    @include('admin.reports.partials.groups._loading')
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    let propertyFinder = {};
    let bayutObj = {};
    let dubizzleObj = {};

    var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';
    $(document).ready(function() {
        portalsData();

        $('.dateFilter').click(function() {
            var duration = $(this).val();
            var url = new URL(window.location.href);
            url.searchParams.set('duration', duration);
            window.location.href = url.toString();
        });
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

    function getChartColorsArray(chartId) {
        if (document.getElementById(chartId) !== null) {
            var colors = document.getElementById(chartId).getAttribute("data-colors");
            colors = JSON.parse(colors);
            return colors.map(function(value) {
                var newValue = value.replace(" ", "");
                if (newValue.indexOf(",") === -1) {
                    var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                    if (color) return color;
                    else return newValue;;
                } else {
                    var val = value.split(',');
                    if (val.length == 2) {
                        var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                        rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                        return rgbaColor;
                    } else {
                        return newValue;
                    }
                }
            });
        }
    }

    function portalsData() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: $('#group_id').val(),
                'type': 'userPropertyMetaReport',
                'duration': duration,
            },
            success: function(result) {
                $('#portals_data').removeClass('d-none');
                propertyFinder.total = result.data.propertyfinder.portal_count;
                propertyFinder.level = result.data.propertyfinder.level;
                propertyFinder.pf_avg_rank = result.data.propertyfinder.avg_pf_rank;
                propertyFinder.pf_ranks = result.data.propertyfinder.pf_rank;
                propertyFinder.online = result.data.propertyfinder.online;
                propertyFinder.offline = result.data.propertyfinder.offline;
                propertyFinderBar = result.data.propertyfinder.pf_rank;
                propertyFinder.whatsapp_avg_res = result.data.propertyfinder.whatsapp_avg_res;
                $(".pf_whatssapp_avg_time").html(convertSecondsToDHMS(propertyFinder.whatsapp_avg_res));
                propertyFinderPieChart(propertyFinder);
                $(".pf_count").html(result.data.propertyfinder.portal_count);
                $(".pf_standard").html(result.data.propertyfinder.level.Standard + ' Listings');
                $(".pf_featured").html(result.data.propertyfinder.level.Featured + ' Listings');
                $(".pf_premium").html(result.data.propertyfinder.level.Premium + ' Listings');

                cardChartPercentage('#pfVerifiedPieChart', result.data.propertyfinder.pf_verified, result
                    .data.propertyfinder.portal_count, 300, 'Verified', '1.2vw', "2vw")


                bayutObj.level = result.data.bayut.level;
                bayutObj.ranks = result.data.bayut.ranks;
                bayutObj.total = result.data.bayut.portal_count;
                bayutObj.verified = result.data.bayut.verified;
                bayutObj.trucheck = result.data.bayut.trucheck;
                bayutObj.checked = result.data.bayut.checked;
                bayutObj.avg_rank = result.data.bayut.avg_rank;
                bayutPieChart(bayutObj);
                $(".bayut_pie_count").html(result.data.bayut.portal_count);
                $(".bayut_standard").html(result.data.bayut.level?.Standard + ' Listings');
                $(".bayut_signature").html(result.data.bayut.level?.Signature + ' Listings');
                $(".bayut_hot").html(result.data.bayut.level?.Hot + ' Listings');
                $(".bayut_verfied_count").html(result.data.bayut.verified);


                dubizzleObj.level = result.data.dubizzle.level;
                dubizzleObj.ranks = result.data.dubizzle.ranks;
                dubizzleObj.total = result.data.dubizzle.portal_count;
                dubizzleObj.verified = result.data.dubizzle.verified;
                dubizzleObj.trucheck = result.data.dubizzle.trucheck;
                dubizzleObj.avg_rank = result.data.dubizzle.avg_rank;
                //push data to dubizzleObj object
                dubizzleObj.status = {
                    'Total': result.data.dubizzle.portal_count,
                    'Verified': result.data.dubizzle.verified,
                    'Trucheck': result.data.dubizzle.trucheck,
                    'Checked': result.data.dubizzle.checked,
                }

                dubizzelePieChart(dubizzleObj)

                $(".dubizzle_total_count").html(result.data.dubizzle.portal_count);
                $(".dubizzle_standard").html(result.data.dubizzle.level?.Standard + ' Listings');
                $(".dubizzle_featured").html(result.data.dubizzle.level?.Featured + ' Listings');

                userMetaSplit();

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function cardChartPercentage(element, data, goal, width = 250, label = '', lableFontSize = "16px", counterFontSize =
        "20px") {
        let percentage = (data / goal) * 100;
        percentage = percentage.toFixed(0);
        let options = {
            series: [percentage],
            chart: {
                type: "radialBar",
                width: "100%",
                sparkline: {
                    enabled: !0
                }
            },
            dataLabels: {
                enabled: 1
            },
            plotOptions: {
                radialBar: {

                    backgroundColor: "#fff",
                    hollow: {
                        margin: 0,
                        size: "65%"
                    },
                    track: {
                        margin: 1,
                        background: 'rgb(240, 101, 72)',
                    },
                    dataLabels: {
                        show: !0,
                        name: {
                            show: label != '' ? 1 : 0,
                        },
                        value: {
                            show: !0,
                            fontSize: counterFontSize,
                            fontWeight: 500,
                            offsetY: 30,
                            // style: {
                            //     margin: `20px 0 0 0` // Adjust the margin as needed
                            // },
                            color: "#495057"
                        },
                        total: {
                            color: "#495057",
                            show: !0,
                            label: label,
                            fontWeight: 500,
                            style: {
                                margin: `40px 0 0 0` // Adjust the margin as needed
                            },
                            fontSize: lableFontSize,
                            formatter: function(e) {
                                return data;
                            }
                        }
                    },

                }
            },
            colors: ["rgb(69, 203, 133)"],
            //change the bar background color


        }
        let chart = new ApexCharts(document.querySelector(element), options);
        chart.render()
    }

    function pfStatusPieChart() {


        let online = propertyFinder.online;
        let offline = propertyFinder.offline;


        var chartPieBasicColors = getChartColorsArray("pf-status-pie");
        if (chartPieBasicColors) {
            var options = {

                series: [online, offline],
                chart: {
                    height: 300,
                    type: 'pie',
                },
                labels: ["Listings Online", "Listings Offline"],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    }
                },
                colors: chartPieBasicColors
            };

            var chart = new ApexCharts(document.querySelector("#pf-status-pie"), options);
            chart.render();
        }

    }

    function dubizzelePieChart(data) {
        let standard = data.standard;
        let featured = data.featured;


        var donutchartProjectsStatusColors = getChartColorsArray("dubizzle-doghnout-chart");

        if (donutchartProjectsStatusColors) {
            let chartData = [];
            let chartLabel = [];
            let total = data.total;

            jQuery.each(data.level, function(index, item) {
                chartData.push(item);
                chartLabel.push(index);
                total = total - item;
            });
            chartData.push(total);
            chartLabel.push('Others');
            var options = {
                series: chartData,
                labels: chartLabel,
                chart: {
                    type: "donut",
                    height: 170,
                },


                plotOptions: {
                    pie: {
                        size: 100,
                        offsetX: 0,
                        offsetY: 0,
                        donut: {
                            size: "80%",
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            },

                        },
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                legend: {
                    show: false,
                },
                stroke: {
                    lineCap: "round",
                    width: 0
                },
                colors: donutchartProjectsStatusColors,
            };
            var chart = new ApexCharts(document.querySelector("#dubizzle-doghnout-chart"), options);
            chart.render();
        }

    }

    function pfRankBarChart() {
        if (typeof propertyFinderBar == 'object') {
            let data = propertyFinderBar;
            let chartData = [];
            let chartLabel = [];
            jQuery.each(data, function(index, item) {
                chartData.push(item);
                chartLabel.push(index + '%');
            });

            let element = document.getElementById('pf_bar_chart');
            barChart(chartLabel, chartData, element);

        }
    }

    function barChart(chartLabel, chartData, element) {
        var options = {
            series: [{
                name: 'Listings having this quality',
                data: chartData
            }],
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    borderRadius: 1,
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
                    colors: ["#878a99"]
                }
            },

            xaxis: {
                categories: chartLabel,
                position: 'bottom',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: true
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
                    show: true,
                    formatter: function(val) {
                        return val;
                    }
                }

            }

        };

        var chart = new ApexCharts(element, options);
        chart.render();

    }

    function userMetaSplit() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: $('#group_id').val(),
                'type': 'userMetaSplit',
                'duration': duration,
            },
            success: function(response) {
                $('#overalldata').append(response);

                let warnings = $('#userMetaSplit').data('warnings')
                let suspensions = $('#userMetaSplit').data('suspensions')
                let warningsPercentage = $('#userMetaSplit').data('warnings_percentage')
                let suspensionsPercentage = $('#userMetaSplit').data('suspensions_percentage')

                createBarChart('userMetaSplit', warnings, suspensions, warningsPercentage,
                    suspensionsPercentage);
                whatsappAverageResponse();

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function whatsappAverageResponse() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: $('#group_id').val(),
                'type': 'getAvgResponseRateOfAgents',
                'duration': duration,
            },
            success: function(response) {
                $('#loading-row').addClass('d-none');
                $('#overalldata').append(response);
                $('#whatsAppResponseTable').DataTable({
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });

                getTotalNumberOfWarningsandSuspensions();
                var data = $('#useActivityData').data('dataset');
                var names = [];
                var counts = [];

                $.each(data, function(key, value) {
                    names.push(key);
                    counts.push(value);
                });
                $('#userActivityReport').removeClass('d-none');

                createUserActivity(names, counts)
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function getTotalNumberOfWarningsandSuspensions() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: $('#group_id').val(),
                'type': 'getTotalNumberOfWarningsandSuspensions',
                'duration': duration,
            },
            success: function(response) {
                $('#overalldata').append(response);
                $('#agentTerminationReport').DataTable({
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }



    function createBarChart(chartClass, warnings, suspensions, warningsPercentage, suspensionsPercentage) {

        var options = {
            series: [warnings, suspensions],
            chart: {
                type: 'pie',
                height: 350
            },
            labels: ['Warnings', 'Suspensions'],
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    // make val 2 decimal places
                    val = val.toFixed(2);
                    return opts.w.config.labels[opts.seriesIndex] + ': ' + val + (opts.seriesIndex === 0 ?
                        ' (' + warningsPercentage + '%)' : ' (' + suspensionsPercentage + '%)');
                }
            },
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        return val + ' (' + (opts.seriesIndex === 0 ? warningsPercentage + '%' :
                            suspensionsPercentage + '%') + ')';
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }


    function createUserActivity(names, counts) {


        var options = {
            series: [{
                name: "Events Counts",
                data: counts
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: true
                },
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
            },
            dataLabels: {
                enabled: false
            },

            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Most Active Users',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            markers: {
                size: 1
            },
            xaxis: {
                categories: names,
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };
        var chart = new ApexCharts(document.querySelector("#useActivityData"), options);
        chart.render();
    }

    function propertyFinderPieChart(data) {
        var donutchartProjectsStatusColors = getChartColorsArray("pf-status");

        if (donutchartProjectsStatusColors) {
            let chartData = [];
            let chartLabel = [];
            let total = data.total;

            jQuery.each(data.level, function(index, item) {
                chartData.push(item);
                chartLabel.push(index);
                total = total - item;
            });
            chartData.push(total);
            chartLabel.push('Others');
            var options = {
                series: chartData,
                labels: chartLabel,
                chart: {
                    type: "donut",
                    height: 170,
                },


                plotOptions: {
                    pie: {
                        size: 100,
                        offsetX: 0,
                        offsetY: 0,
                        donut: {
                            size: "80%",
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            },

                        },
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                legend: {
                    show: false,
                },
                stroke: {
                    lineCap: "round",
                    width: 0
                },
                colors: donutchartProjectsStatusColors,
            };
            var chart = new ApexCharts(document.querySelector("#pf-status"), options);
            chart.render();
        }
    }


    function pfRankMeterChart() {
        let dom = document.getElementById('pf_rank_meter');
        let value = propertyFinder.pf_avg_rank;
        rankMeterChart(dom, value);
    }

    function bayutRankMeterChart() {
        let dom = document.getElementById('bayut_rank_meter');
        let value = bayutObj.avg_rank;
        rankMeterChart(dom, value);
    }

    function dubizzleRankMeterChart() {
        let element = document.getElementById('dubbizle_rank_meter');
        let value = dubizzleObj.avg_rank;
        rankMeterChart(element, value);

    }

    function rankMeterChart(element, value) {
        var myChart = echarts.init(element, null, {
            renderer: 'canvas',
            useDirtyRect: false
        });
        var app = {};

        var option;

        option = {
            series: [{
                type: 'gauge',
                axisLine: {
                    lineStyle: {
                        width: 15,
                        color: [
                            [0.3, '#d22500'],
                            [0.5, '#f06547'],
                            [0.7, '#f6b84b'],
                            [1, '#46cb85']
                        ]
                    }
                },
                pointer: {
                    itemStyle: {
                        color: 'inherit'
                    }
                },
                axisTick: {
                    distance: -30,
                    length: 2,
                    lineStyle: {
                        color: '#fff',
                        width: 1
                    }
                },
                splitLine: {
                    distance: -30,
                    length: 30,
                    lineStyle: {
                        color: '#fff',
                        width: 4
                    }
                },
                axisLabel: {
                    color: 'inherit',
                    distance: 25,
                    fontSize: 15
                },
                detail: {
                    valueAnimation: true,
                    formatter: '{value} %',
                    color: 'inherit',
                    fontSize: 25
                },
                data: [{
                    value: value
                }]
            }]
        };

        myChart.setOption({
            series: [{
                data: [{
                    value: 50
                }]
            }]
        });
        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }
        window.addEventListener('resize', myChart.resize);
    }


    function bayutPieChart(data) {

        let chartData = [];
        let chartLabel = [];
        let total = data.total;
        jQuery.each(data.level, function(index, item) {
            chartData.push(item);
            chartLabel.push(index);
            total = total - item;
        });


        var donutchartProjectsStatusColors = getChartColorsArray("bayut-pie-chart");
        if (donutchartProjectsStatusColors) {
            var options = {
                series: chartData,
                labels: chartLabel,
                chart: {
                    type: "donut",
                    height: 170,
                },
                plotOptions: {
                    pie: {
                        size: 100,
                        offsetX: 0,
                        offsetY: 0,
                        donut: {
                            size: "80%",
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            }
                        },
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                legend: {
                    show: false,
                },
                stroke: {
                    lineCap: "round",
                    width: 0
                },
                colors: donutchartProjectsStatusColors,
            };
            var chart = new ApexCharts(document.querySelector("#bayut-pie-chart"), options);
            chart.render();
        }
    }

    function bayutVerfiedChart() {

        let total = bayutObj.total;
        let verified = bayutObj.verified;
        let trucheck = bayutObj.trucheck;
        let checked = bayutObj.checked;


        var chartPieBasicColors = getChartColorsArray("bayut-verified-chart");
        if (chartPieBasicColors) {
            var options = {

                series: [verified, trucheck, checked],
                chart: {
                    height: 300,
                    type: 'pie',
                },
                labels: ["Verified", "Trucheck", "Checked"],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    }
                },
                colors: chartPieBasicColors
            };

            var chart = new ApexCharts(document.querySelector("#bayut-verified-chart"), options);
            chart.render();
        }

    }

    $("#sidebarReports").addClass('collapse show');
    $('#sidebarGroupReports').addClass('active');
    $("#reportsActive").addClass('active');
</script>

@yield('pageDT')
@stack('in_page_scripts')
@endsection
