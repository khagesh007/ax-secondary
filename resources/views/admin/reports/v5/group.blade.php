@extends('admin.layouts.master')
@section('title')
    Group Report
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
        .card-header{
            padding-bottom: 0 !important;
        }
        /*.select2-selection__rendered {*/
        /*    overflow: hidden;*/
        /*    white-space: nowrap;*/
        /*    text-overflow: ellipsis;*/
        /*    width: 100%;*/
        /*    box-sizing: border-box;*/
        /*}*/
        /*.select2-results__option.hide {*/
        /*    display: none;*/
        /*}*/
    </style>

@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Group Reporting</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reporting</a></li>
                            <li class="breadcrumb-item active">Group Reporting</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include("admin.reports.v5.partials._filter")

    <div class="row">
        <div class="col-xxl-12">
            <div class="card d-none">
                <div class="card-body">
                    @foreach ($groups as $group)
                        <input type="hidden" name="group_id[]" value="{{ $group->id }}" class="formInput">
                    @endforeach
                </div>
            </div>
            <div class="" id="chartCards"></div>

            @include('admin.reports.partials.groups._loading')
        </div>
    </div><!--end col-->
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

            $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
                let url = new URL(window.location.href);
                url.searchParams.set('duration', 'custom');
                url.searchParams.set('from', picker.startDate.format('DD-MM-YYYY'));
                url.searchParams.set('to', picker.endDate.format('DD-MM-YYYY'));
                window.location.href = url.toString();
            });

            $("#sidebarReports").addClass('collapse show');
            $('#sidebarGroupV5Reports').addClass('active');
            $("#reportsActive").addClass('active');
        });

        const types = {
            eventStatistics: {
                type: "eventStatistics",
                chartElementId: "event-statistics-chart",
                percentChartElementId: "event-statistics-percent-chart",
                tableElementId: "event-statistics-table",
                select2ElementId: "event-statistics-status-select",
            },
            daysWithoutDeals: {
                type: "daysWithoutDeals",
                chartElementId: "days-without-deals-overall-chart"
            },
            listingStatistics: {
                type: "listingStatistics",
                chartElementId: "listing-statistics-chart",
                percentChartElementId: "listing-statistics-percent-chart",
                tableElementId: "listing-statistics-table",
                select2ElementId: "listing-statistics-status-select",
            },
            leadStatistics: {
                type: "leadStatistics",
                chartElementId: "lead-statistics-chart",
                percentChartElementId: "lead-statistics-percent-chart",
                tableElementId: "lead-statistics-table",
                select2ElementId: "lead-statistics-status-select",
            },
            leadSourceStatistics: {
                type: "leadSourceStatistics",
                chartElementId: "lead-source-statistics-chart",
                percentChartElementId: "lead-source-statistics-percent-chart",
                tableElementId: "lead-source-statistics-table",
                select2ElementId: "lead-source-statistics-status-select",
            },
            listingLeadStatistics: {
                type: "listingLeadStatistics",
                chartElementId: "listing-lead-statistics-chart",
                percentChartElementId: "listing-lead-statistics-percent-chart",
                tableElementId: "listing-lead-statistics-table",
                select2ElementId: "listing-lead-statistics-status-select",
            },
            listingLeadSourceStatistics: {
                type: "listingLeadSourceStatistics",
                chartElementId: "listing-lead-source-statistics-chart",
                percentChartElementId: "listing-lead-source-statistics-percent-chart",
                tableElementId: "listing-lead-source-statistics-table",
                select2ElementId: "listing-lead-source-statistics-status-select",
            },
            locationStatistics: {
                type: "locationStatistics",
                chartElementId: "location-statistics-chart",
                percentChartElementId: "location-statistics-percent-chart",
                tableElementId: "location-statistics-table",
                select2ElementId: "location-statistics-status-select",
            },
            listingQualityStatistics: {
                type: "listingQualityStatistics",
                chartElementId: "listing-quality-statistics-chart",
                tableElementId: "listing-quality-statistics-table",
                select2ElementId: "listing-quality-statistics-status-select",
            },
            listingLevelStatistics: {
                type: "listingLevelStatistics",
                chartElementClass: "listing-product-level-statistics-chart"
            },
            listingVerifiedStatistics: {
                type: "listingVerifiedStatistics",
                chartElementClass: "listing-verified-statistics-chart"
            },
            dealStatistics: {
                type: "dealStatistics",
                chartElementId: "deal-statistics-chart",
                percentChartElementId: "deal-statistics-percent-chart",
                tableElementId: "deal-statistics-table",
                select2ElementId: "deal-statistics-status-select",
            },
            userMetaStatistics: {
                type: "userMetaStatistics",
                chartElementId: "user-meta-statistics-chart",
                percentChartElementId: "user-meta-statistics-percent-chart",
                tableElementId: "user-meta-statistics-table",
                select2ElementId: "user-meta-statistics-status-select",
            },
            callLogStatistics: {
                type: "callLogStatistics",
                chartElementId: "call-log-statistics-chart",
                percentChartElementId: "call-log-statistics-percent-chart",
                tableElementId: "call-log-statistics-table",
                select2ElementId: "call-log-statistics-status-select",
            },
        }


        function renderDaysWithoutDealsCharts() {
            const renderOverallChart = () => {
                if (!document.getElementById("days-without-deals-overall-chart")) {
                    return
                }

                let dataset = $("#days-without-deals-overall-chart").data("dataset");

                if (dataset.data === undefined || dataset.data == null) {
                    return;
                }

                let myChart = echarts.init(document.getElementById("days-without-deals-overall-chart"));

                let categoriesData = [];
                let seriesData = [];

                Object.keys(dataset.data).map(key => {
                    categoriesData.push(dataset.data[key].name)
                    seriesData.push({
                        value: dataset.data[key].total,
                        itemStyle: {
                            color: dataset.data[key].color
                        }
                    })
                })

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
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: [
                        {
                            type: 'category',
                            data: categoriesData,
                            axisTick: {
                                alignWithLabel: true
                            },
                            axisLabel: {
                                interval: 0,
                                rotate: 40
                            }
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value',
                        }
                    ],
                    series: [
                        {
                            name: 'Days',
                            type: 'bar',
                            barWidth: '60%',
                            data: seriesData
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

            const renderGroupCharts = () => {
                if (!document.getElementById("days-without-deals-group-charts")) {
                    return
                }

                let dataset = $("#days-without-deals-group-charts").data("dataset");

                if (dataset.data === undefined || dataset.data == null) {
                    return;
                }

                Object.keys(dataset.data).map(groupId => {
                    const data = dataset.data[groupId];
                    if (data.users.length >= 1) {
                        let elementId = `days-without-deals-group-charts-${groupId}`;
                        let card = `
                                    <div class="graph-container" id="${elementId}"></div>`;

                        $("#days-without-deals-group-charts").append(card);

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
                                bottom: '3%',
                                containLabel: true
                            },
                            xAxis: [
                                {
                                    type: 'category',
                                    data: dataset.data[groupId].users,
                                    axisTick: {
                                        alignWithLabel: true
                                    },
                                    axisLabel: {
                                        interval: 0,
                                        rotate: 40
                                    }
                                }
                            ],
                            yAxis: [
                                {
                                    type: 'value'
                                }
                            ],
                            series: [
                                {
                                    name: 'Days',
                                    type: 'bar',
                                    barWidth: '60%',
                                    data: dataset.data[groupId].counts,
                                    color: dataset.data[groupId].color,
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
                })
                $("#nav-days-without-deals-group-charts").removeClass("show active");
            }

            renderOverallChart();
            renderGroupCharts();
        }

        const renderPortalCharts = (elementClass) => {
            $(`.${elementClass}`).each(function (index, element) {
                const elementId = $(this).attr("id");

                if (!document.getElementById(elementId)) {
                    return
                }

                let dataset = $(`#${elementId}`).data("dataset");

                if (dataset === undefined || dataset == null) {
                    return;
                }

                let myChart = echarts.init(document.getElementById(elementId));

                let series = [];

                let categoriesData = [];

                Object.keys(dataset).map((key, index) => {
                    if (!categoriesData.length && dataset[key].statuses.length > 1){
                        categoriesData = dataset[key].statuses;
                    }
                    series.push({
                        name: dataset[key].name,
                        type: 'bar',
                        data: dataset[key].counts,
                        color: dataset[key].color
                    })
                })

                let option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross',
                            crossStyle: {
                                color: '#999'
                            }
                        }
                    },
                    legend: {},
                    xAxis: [
                        {
                            type: 'category',
                            data: categoriesData,
                            axisPointer: {
                                type: 'shadow'
                            },
                            axisLabel: {
                                interval: 0,
                                rotate: 40
                            }
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value'
                        }
                    ],
                    series: series
                };

                myChart.setOption(option);
                if (index !== 0){
                    $(`#${elementId}`).parent(".tab-pane").removeClass("show active")
                }
            })

        }


        function renderSingleBarChart(elementId, tableElementId = null, select2ElementId = null, valueFormat = "", yAxisLabelFormat = "", isAverage = false) {
            if (!document.getElementById(elementId)) {
                return;
            }

            let dataset = $(`#${elementId}`).data("dataset");

            if (dataset.data === undefined || dataset.data == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById(elementId));

            let series = [];

            let xAxis = [
                {
                    type: 'category',
                    data: [...dataset.statuses],
                    axisPointer: {
                        type: 'shadow'
                    },
                    axisLabel: {
                        interval: 0,
                        rotate: 40
                    }
                }
            ]

            let percentages = {};

            if (isAverage) {
                Object.keys(dataset.data).forEach(key => {
                    let data = [];
                    Object.keys(dataset.data[key].status_wise_averages).forEach(statusSlug => {
                        const totalAverage = dataset.total_averages[statusSlug] || 0;
                        const percent = totalAverage > 0 ? (dataset.data[key].status_wise_averages[statusSlug] / totalAverage) * 100 : 0;
                        data.push(percent.toFixed(2));
                    });
                    percentages[key] = [...data];

                    series.push({
                        id: dataset.data[key].id,
                        name: dataset.data[key].name,
                        type: 'bar',
                        data: data,
                        color: dataset.data[key].color,
                        tooltip: {
                            valueFormatter(value) {
                                return `${value}${valueFormat || ""}`;
                            }
                        },
                    });
                });
            } else {
                Object.keys(dataset.data).forEach(key => {
                    series.push({
                        id: dataset.data[key].id,
                        name: dataset.data[key].name,
                        type: 'bar',
                        data: [...dataset.data[key].counts],
                        color: dataset.data[key].color,
                        tooltip: {
                            valueFormatter(value) {
                                return `${value}${valueFormat || ""}`;
                            }
                        },
                    });
                });
            }

            let dataZoom = [];

            if (dataset.statuses.length > 12) {
                dataZoom.push({
                    type: 'slider',
                    start: 0,
                    end: 29,
                    showDataShadow: true,
                    showDetail: false
                });
            }

            let option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                legend: {},
                xAxis: xAxis,
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter(value) {
                                return `${value}${yAxisLabelFormat || ""}`;
                            }
                        }
                    }
                ],
                series: series,
                dataZoom: dataZoom
            };

            myChart.setOption(option);

            function toggleCategoryVisibility(categoryName, visible) {
                let index = dataset.statuses.indexOf(categoryName);
                if (index !== -1) {
                    if (visible) {
                        series.forEach(s => {
                            let value = dataset.data[s.id].counts[index];
                            if (isAverage){
                                value = percentages[s.id][index]
                            }
                            s.data.splice(index, 0, value);
                        });
                        xAxis[0].data.splice(index, 0, dataset.statuses[index]);
                        if (tableElementId){
                            const status = categoryName.replace(/[^a-zA-Z0-9]/g, '_');
                            $(`#${tableElementId}-${status}`).removeClass("d-none");
                        }
                    } else {
                        let dataIndex = xAxis[0].data.indexOf(categoryName);
                        if (dataIndex !== -1){
                            series.forEach(s => {
                                s.data.splice(dataIndex, 1)
                            });
                            xAxis[0].data.splice(dataIndex, 1);
                        }
                        if (tableElementId){
                            const status = categoryName.replace(/[^a-zA-Z0-9]/g, '_');
                            $(`#${tableElementId}-${status}`).addClass("d-none");
                        }

                    }
                    myChart.setOption({ series: series, xAxis: xAxis });
                }
            }

            if (isAverage) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active");
            }

            if (select2ElementId){
                $(`#${select2ElementId}`).select2({
                    placeholder: "Select options",
                    allowClear: true,
                    closeOnSelect: false,
                });

                $(`#${select2ElementId}`).on("select2:select", function (e) {
                    const option = e.params.data.id;
                    toggleCategoryVisibility(option, true)
                });

                $(`#${select2ElementId}`).on("select2:unselect", function (e) {
                    const option = e.params.data.id;
                    toggleCategoryVisibility(option, false)
                });
            }
        }



        function getNextType(currentType) {
            if (currentType === "eventStatistics") {
                return types.daysWithoutDeals;
            }

            if (currentType === "daysWithoutDeals") {
                return types.listingStatistics;
            }

            if (currentType === "listingStatistics") {
                return types.leadStatistics;
            }

            if (currentType === "leadStatistics") {
                return types.leadSourceStatistics;
            }

            if (currentType === "leadSourceStatistics") {
                return types.listingLeadStatistics;
            }

            if (currentType === "listingLeadStatistics") {
                return types.listingLeadSourceStatistics;
            }

            if (currentType === "listingLeadSourceStatistics") {
                return types.locationStatistics;
            }

            if (currentType === "locationStatistics") {
                return types.listingQualityStatistics;
            }

            if (currentType === "listingQualityStatistics") {
                return types.listingLevelStatistics;
            }

            if (currentType === "listingLevelStatistics") {
                return types.listingVerifiedStatistics;
            }

            if (currentType === "listingVerifiedStatistics") {
                return types.dealStatistics;
            }

            if (currentType === "dealStatistics") {
                return types.userMetaStatistics;
            }

            if (currentType === "userMetaStatistics") {
                return types.callLogStatistics;
            }

            return null
        }

        function getView(type) {
            $.ajax({
                type: "POST",
                url: "{{ route('reports.group.v5.getView') }}",
                data: {
                    groupIds,
                    type,
                    duration
                },
                success: function (response) {
                    $('#chartCards').append(response);
                    let valueFormat = "";
                    let yAxisLabelFormat = "";
                    if (type === types.listingQualityStatistics.type){
                        valueFormat = "%";
                        yAxisLabelFormat = "%";
                    }
                    const currentType = types[type];
                    if (currentType.type === types.daysWithoutDeals.type) {
                        renderDaysWithoutDealsCharts();
                    }else if(currentType.type === types.listingLevelStatistics.type || currentType.type === types.listingVerifiedStatistics.type){
                        renderPortalCharts(currentType.chartElementClass);
                    }else {
                        renderSingleBarChart(currentType.chartElementId, currentType.tableElementId, currentType.select2ElementId, valueFormat, yAxisLabelFormat);
                        if (currentType.percentChartElementId){
                            renderSingleBarChart(currentType.percentChartElementId, null, currentType.select2ElementId, "%", "%", true);
                        }
                    }
                    const nextType = getNextType(type);
                    if (nextType) {
                        getView(nextType.type);
                    } else {
                        $('#loading-row').addClass('d-none');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                }
            });
        }

        getView(types.eventStatistics.type)
    </script>
@endsection
