@extends('admin.layouts.master')
@section('title')
    Lead Insights
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
                        <h4 class="card-title mb-0 ">Lead Insights</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reporting</a></li>
                            <li class="breadcrumb-item active">Lead Insights</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include("admin.reports.lead_insights.partials._filter")

    <div class="" id="chartCards"></div>

    @include('admin.reports.partials.groups._loading')

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

        let date = "{{ request('date') }}";

        let groupIds = JSON.parse('<?= json_encode(request('groups') ?? []) ?>');

        $('input[name="group_id[]"]').each(function () {
            groupIds.push($(this).val());
        });

        $(document).ready(function () {
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $("#sidebarReports").addClass('collapse show');
            $('#sidebarLeadInsightsReports').addClass('active');
            $("#reportsActive").addClass('active');
        });



        //Lead Insights Starts
        function renderLeadOverviewSourceWiseChart(){
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
                series: [
                    {
                        type: 'pie',
                        radius: '70%',
                        label: {
                            show: true,
                            formatter: '{b}: {c}({d}%)'
                        },
                        data: [
                            {
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
                            }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };

            myChart.setOption(option);

            myChart.on("click", function (params) {
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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.dates,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    },
                ],
                series: [
                    {
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

            if (shouldHide){
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active");
            }
        }

        function renderLeadOverviewChart(){
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
                series: [
                    {
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
                    }
                ]
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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.dates,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    },
                ],
                series: [
                    {
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
        function renderLeadInsightsData(){
            $.ajax({
                type: "POST",
                url: "{{ route('reports.lead-insights.getView') }}",
                data: {
                    groupIds,
                    type: "leadInsights",
                    date
                },
                success: function (response) {
                    $('#chartCards').append(response);
                    renderLeadOverviewSourceWiseChart();
                    renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-pf-count-date-wise-chart", true);
                    renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-bayut-count-date-wise-chart", true);
                    renderLeadCountDateWiseSourceWiseChart("lead-insights-lead-source-dubizzle-count-date-wise-chart", true);
                    renderLeadOverviewChart();
                    renderLeadCountDateWiseChart();
                    renderCallInsightsData();
                },
                error: function (xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                }
            });
        }
        //Lead Insights Ends

        //Call Insights Starts
        function renderCallOverviewChart() {

            if (!document.getElementById("call-insights-call-overview-chart")) {
                return;
            }

            let dataset = $("#call-insights-call-overview-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("call-insights-call-overview-chart"));

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b} : {c} ({d}%)'
                },
                series: [
                    {
                        type: 'pie',
                        radius: '70%',
                        label: {
                            show: true,
                            formatter: '{b}: {c}({d}%)'
                        },
                        data: [
                            {
                                name: "Completed",
                                value: dataset.completed || 0,
                                color: "green"
                            },
                            {
                                name: "No Answer",
                                value: dataset.no_answer || 0,
                                color: "blue"
                            },
                            {
                                name: "Missed",
                                value: dataset.missed || 0,
                                color: "red"
                            },
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };

            myChart.setOption(option);
        }
        function renderCallCountDateWiseChart() {
            if (!document.getElementById("call-insights-call-count-date-wise-chart")) {
                return;
            }

            let dataset = $("#call-insights-call-count-date-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let completedCounts = dataset.completed;
            let noAnswerCounts = dataset.no_answer;
            let missedCounts = dataset.missed;

            const pfCompletedCounts = dataset.pf_completed;
            const pfNoAnswerCounts = dataset.pf_no_answer;
            const pfMissedCounts = dataset.pf_missed;

            const bayutCompletedCounts = dataset.bayut_completed;
            const bayutNoAnswerCounts = dataset.bayut_no_answer;
            const bayutMissedCounts = dataset.bayut_missed;

            const dubizzleCompletedCounts = dataset.dubizzle_completed;
            const dubizzleNoAnswerCounts = dataset.dubizzle_no_answer;
            const dubizzleMissedCounts = dataset.dubizzle_missed;


            let myChart = echarts.init(document.getElementById("call-insights-call-count-date-wise-chart"));

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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.dates,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    },
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'line',
                        data: completedCounts,
                        color: "green",
                        symbol: 'none'
                    },
                    {
                        name: 'No Answer',
                        type: 'line',
                        data: noAnswerCounts,
                        color: "blue",
                        symbol: 'none'
                    },
                    {
                        name: 'Missed',
                        type: 'line',
                        data: missedCounts,
                        color: "red",
                        symbol: 'none'
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

            function regenerateChart(selectedValues = []){
                completedCounts = [];
                noAnswerCounts = [];
                missedCounts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.completed.length; i++){
                        let completed = 0;
                        let noAnswer = 0;
                        let missed = 0;
                        if (includePf){
                            completed += parseFloat(pfCompletedCounts[i]);
                            noAnswer += parseFloat(pfNoAnswerCounts[i]);
                            missed += parseFloat(pfMissedCounts[i]);
                        }
                        if (includeBayut){
                            completed += parseFloat(bayutCompletedCounts[i]);
                            noAnswer += parseFloat(bayutNoAnswerCounts[i]);
                            missed += parseFloat(bayutMissedCounts[i]);
                        }
                        if (includeDubizzle){
                            completed += parseFloat(dubizzleCompletedCounts[i]);
                            noAnswer += parseFloat(dubizzleNoAnswerCounts[i]);
                            missed += parseFloat(dubizzleMissedCounts[i]);
                        }
                        completedCounts.push(completed);
                        noAnswerCounts.push(noAnswer);
                        missedCounts.push(missed);
                    }
                }else{
                    completedCounts = dataset.completed;
                    noAnswerCounts = dataset.no_answer;
                    missedCounts = dataset.missed;
                }
                const series = option.series;
                series[0].data = completedCounts;
                series[1].data = noAnswerCounts;
                series[2].data = missedCounts;
                myChart.setOption({ series: series});
            }

            $("#call-insights-call-date-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#call-insights-call-date-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderCallResponseRateDateWiseChart() {
            if (!document.getElementById("call-call-insights-call-response-rate-date-wise-chart")) {
                return;
            }
            let dataset = $("#call-call-insights-call-response-rate-date-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            const pfCounts = dataset.pf_counts;
            const bayutCounts = dataset.bayut_counts;
            const dubizzleCounts = dataset.dubizzle_counts;
            let counts = dataset.counts;

            let myChart = echarts.init(document.getElementById("call-call-insights-call-response-rate-date-wise-chart"));

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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.dates,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter(value) {
                                return `${value}%`;
                            }
                        }
                    },
                ],
                series: [
                    {
                        type: 'line',
                        data: counts,
                        color: 'rgba(238, 162, 111, 1)',
                        symbol: 'none',
                        tooltip: {
                            valueFormatter(value) {
                                return `${value}%`;
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

            $("#call-call-insights-call-response-rate-date-wise-chart").parent(".tab-pane").removeClass("show active");

            function regenerateChart(selectedValues = []){
                counts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.counts.length; i++){
                        let total = 0;
                        if (includePf){
                            total += parseFloat(pfCounts[i]);
                        }
                        if (includeBayut){
                            total += parseFloat(bayutCounts[i]);
                        }
                        if (includeDubizzle){
                            total += parseFloat(dubizzleCounts[i]);
                        }
                        total = (total / selectedLength).toFixed(2);
                        counts.push(total);
                    }
                }else{
                    counts = dataset.counts;
                }
                const series = option.series;
                series[0].data = counts;
                myChart.setOption({ series: series});
            }

            $("#call-insights-call-date-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#call-insights-call-date-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderCallCountWeekDayWiseChart() {

            if (!document.getElementById("call-insights-call-count-week-day-wise-chart")) {
                return;
            }

            let dataset = $("#call-insights-call-count-week-day-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let completedCounts = dataset.completed;
            let noAnswerCounts = dataset.no_answer;
            let missedCounts = dataset.missed;

            const pfCompletedCounts = dataset.pf_completed;
            const pfNoAnswerCounts = dataset.pf_no_answer;
            const pfMissedCounts = dataset.pf_missed;

            const bayutCompletedCounts = dataset.bayut_completed;
            const bayutNoAnswerCounts = dataset.bayut_no_answer;
            const bayutMissedCounts = dataset.bayut_missed;

            const dubizzleCompletedCounts = dataset.dubizzle_completed;
            const dubizzleNoAnswerCounts = dataset.dubizzle_no_answer;
            const dubizzleMissedCounts = dataset.dubizzle_missed;

            let myChart = echarts.init(document.getElementById("call-insights-call-count-week-day-wise-chart"));

            let option = {
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
                        data: dataset.days
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
                        color: 'green',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: completedCounts
                    },
                    {
                        name: 'No Answer',
                        type: 'bar',
                        color: 'blue',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: noAnswerCounts
                    },
                    {
                        name: 'Missed',
                        type: 'bar',
                        color: 'red',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: missedCounts
                    }
                ]
            };

            myChart.setOption(option);

            function regenerateChart(selectedValues = []){
                completedCounts = [];
                noAnswerCounts = [];
                missedCounts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.completed.length; i++){
                        let completed = 0;
                        let noAnswer = 0;
                        let missed = 0;
                        if (includePf){
                            completed += parseFloat(pfCompletedCounts[i]);
                            noAnswer += parseFloat(pfNoAnswerCounts[i]);
                            missed += parseFloat(pfMissedCounts[i]);
                        }
                        if (includeBayut){
                            completed += parseFloat(bayutCompletedCounts[i]);
                            noAnswer += parseFloat(bayutNoAnswerCounts[i]);
                            missed += parseFloat(bayutMissedCounts[i]);
                        }
                        if (includeDubizzle){
                            completed += parseFloat(dubizzleCompletedCounts[i]);
                            noAnswer += parseFloat(dubizzleNoAnswerCounts[i]);
                            missed += parseFloat(dubizzleMissedCounts[i]);
                        }
                        completedCounts.push(completed);
                        noAnswerCounts.push(noAnswer);
                        missedCounts.push(missed);
                    }
                }else{
                    completedCounts = dataset.completed;
                    noAnswerCounts = dataset.no_answer;
                    missedCounts = dataset.missed;
                }
                const series = option.series;
                series[0].data = completedCounts;
                series[1].data = noAnswerCounts;
                series[2].data = missedCounts;
                myChart.setOption({ series: series});
            }

            $("#call-insights-call-week-day-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#call-insights-call-week-day-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderCallCountHourWiseChart() {
            if (!document.getElementById("call-insights-call-count-hour-wise-chart")) {
                return;
            }

            let dataset = $("#call-insights-call-count-hour-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let completedCounts = dataset.completed;
            let noAnswerCounts = dataset.no_answer;
            let missedCounts = dataset.missed;

            const pfCompletedCounts = dataset.pf_completed;
            const pfNoAnswerCounts = dataset.pf_no_answer;
            const pfMissedCounts = dataset.pf_missed;

            const bayutCompletedCounts = dataset.bayut_completed;
            const bayutNoAnswerCounts = dataset.bayut_no_answer;
            const bayutMissedCounts = dataset.bayut_missed;

            const dubizzleCompletedCounts = dataset.dubizzle_completed;
            const dubizzleNoAnswerCounts = dataset.dubizzle_no_answer;
            const dubizzleMissedCounts = dataset.dubizzle_missed;

            let myChart = echarts.init(document.getElementById("call-insights-call-count-hour-wise-chart"));

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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.times,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    },
                ],
                series: [
                    {
                        name: 'Completed',
                        type: 'line',
                        data: completedCounts,
                        color: "green",
                        symbol: 'none'
                    },
                    {
                        name: 'No Answer',
                        type: 'line',
                        data: noAnswerCounts,
                        color: "blue",
                        symbol: 'none'
                    },
                    {
                        name: 'Missed',
                        type: 'line',
                        data: missedCounts,
                        color: "red",
                        symbol: 'none'
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

            function regenerateChart(selectedValues = []){
                completedCounts = [];
                noAnswerCounts = [];
                missedCounts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.completed.length; i++){
                        let completed = 0;
                        let noAnswer = 0;
                        let missed = 0;
                        if (includePf){
                            completed += parseFloat(pfCompletedCounts[i]);
                            noAnswer += parseFloat(pfNoAnswerCounts[i]);
                            missed += parseFloat(pfMissedCounts[i]);
                        }
                        if (includeBayut){
                            completed += parseFloat(bayutCompletedCounts[i]);
                            noAnswer += parseFloat(bayutNoAnswerCounts[i]);
                            missed += parseFloat(bayutMissedCounts[i]);
                        }
                        if (includeDubizzle){
                            completed += parseFloat(dubizzleCompletedCounts[i]);
                            noAnswer += parseFloat(dubizzleNoAnswerCounts[i]);
                            missed += parseFloat(dubizzleMissedCounts[i]);
                        }
                        completedCounts.push(completed);
                        noAnswerCounts.push(noAnswer);
                        missedCounts.push(missed);
                    }
                }else{
                    completedCounts = dataset.completed;
                    noAnswerCounts = dataset.no_answer;
                    missedCounts = dataset.missed;
                }
                const series = option.series;
                series[0].data = completedCounts;
                series[1].data = noAnswerCounts;
                series[2].data = missedCounts;
                myChart.setOption({ series: series});
            }

            $("#call-insights-call-hour-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#call-insights-call-hour-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderCallInsightsData(){
            $.ajax({
                type: "POST",
                url: "{{ route('reports.lead-insights.getView') }}",
                data: {
                    groupIds,
                    type: "callInsights",
                    date
                },
                success: function (response) {
                    $('#chartCards').append(response);
                    renderCallOverviewChart();
                    renderCallCountDateWiseChart();
                    renderCallResponseRateDateWiseChart();
                    renderCallCountWeekDayWiseChart();
                    renderCallCountHourWiseChart();
                    renderWhatsAppLeadInsightsData();
                },
                error: function (xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                }
            });
        }
        //Call Insights Ends

        //WhatsApp Insights Starts
        function renderWhatsAppLeadOverviewChart() {

            if (!document.getElementById("whats-app-insights-lead-overview-chart")) {
                return;
            }

            let dataset = $("#whats-app-insights-lead-overview-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            let myChart = echarts.init(document.getElementById("whats-app-insights-lead-overview-chart"));

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b} : {c} ({d}%)'
                },
                series: [
                    {
                        type: 'pie',
                        radius: '70%',
                        label: {
                            show: true,
                            formatter: '{b}: {c}({d}%)'
                        },
                        data: [
                            {
                                name: "Replied",
                                value: dataset.replied || 0,
                            },
                            {
                                name: "Not Replied",
                                value: dataset.not_replied || 0,
                            }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            myChart.setOption(option);
        }
        function renderWhatsAppLeadDateWiseChart(elementId, axisLabel = "", shouldHide = false, isAverage = false) {
            if (!document.getElementById(elementId)) {
                return;
            }
            let dataset = $(`#${elementId}`).data("dataset");


            if (dataset === undefined || dataset == null) {
                return;
            }

            const pfCounts = dataset.pf_counts;
            const bayutCounts = dataset.bayut_counts;
            const dubizzleCounts = dataset.dubizzle_counts;
            let counts = dataset.counts;

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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.dates,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter(value) {
                                return `${value}${axisLabel}`;
                            }
                        }
                    },
                ],
                series: [
                    {
                        type: 'line',
                        data: counts,
                        color: 'rgba(238, 162, 111, 1)',
                        symbol: 'none',
                        tooltip: {
                            valueFormatter(value) {
                                return `${value}${axisLabel}`;
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

            if (shouldHide) {
                $(`#${elementId}`).parent(".tab-pane").removeClass("show active");
            }

            function regenerateChart(selectedValues = []){
                counts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.counts.length; i++){
                        let total = 0;
                        if (includePf){
                            total += parseFloat(pfCounts[i]);
                        }
                        if (includeBayut){
                            total += parseFloat(bayutCounts[i]);
                        }
                        if (includeDubizzle){
                            total += parseFloat(dubizzleCounts[i]);
                        }
                        if (isAverage){
                            total = (total / selectedLength).toFixed(2);
                        }
                        counts.push(total);
                    }
                }else{
                    counts = dataset.counts;
                }
                const series = option.series;
                series[0].data = counts;
                myChart.setOption({ series: series});
            }

            $("#whats-app-insights-lead-date-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#whats-app-insights-lead-date-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderWhatsAppLeadCountWeekDayWiseChart() {

            if (!document.getElementById("whats-app-insights-lead-count-week-day-wise-chart")) {
                return;
            }

            let dataset = $("#whats-app-insights-lead-count-week-day-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            const pfCounts = dataset.pf_counts;
            const bayutCounts = dataset.bayut_counts;
            const dubizzleCounts = dataset.dubizzle_counts;
            let counts = dataset.counts;


            let myChart = echarts.init(document.getElementById("whats-app-insights-lead-count-week-day-wise-chart"));

            let option = {
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
                        data: dataset.days
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        type: 'bar',
                        color: 'rgba(238, 162, 111, 1)',
                        stack: 'calls',
                        emphasis: {
                            focus: 'series'
                        },
                        data: counts
                    }
                ]
            };

            myChart.setOption(option);

            function regenerateChart(selectedValues = []){
                counts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.counts.length; i++){
                        let total = 0;
                        if (includePf){
                            total += parseFloat(pfCounts[i]);
                        }
                        if (includeBayut){
                            total += parseFloat(bayutCounts[i]);
                        }
                        if (includeDubizzle){
                            total += parseFloat(dubizzleCounts[i]);
                        }
                        counts.push(total);
                    }
                }else{
                    counts = dataset.counts;
                }
                const series = option.series;
                series[0].data = counts;
                myChart.setOption({ series: series});
            }

            $("#whats-app-insights-lead-week-day-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#whats-app-insights-lead-week-day-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderWhatsAppLeadCountHourWiseChart() {
            if (!document.getElementById("whats-app-insights-lead-count-hour-wise-chart")) {
                return;
            }

            let dataset = $("#whats-app-insights-lead-count-hour-wise-chart").data("dataset");

            if (dataset === undefined || dataset == null) {
                return;
            }

            const pfCounts = dataset.pf_counts;
            const bayutCounts = dataset.bayut_counts;
            const dubizzleCounts = dataset.dubizzle_counts;
            let counts = dataset.counts;

            let myChart = echarts.init(document.getElementById("whats-app-insights-lead-count-hour-wise-chart"));

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
                xAxis: [
                    {
                        type: 'category',
                        data: dataset.times,
                        boundaryGap: false,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    },
                ],
                series: [
                    {
                        type: 'line',
                        data: dataset.counts,
                        color: 'rgba(238, 162, 111, 1)',
                        symbol: 'none'
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

            function regenerateChart(selectedValues = []){
                counts = [];
                const selectedLength = selectedValues.length;
                if (selectedLength > 0){
                    const includePf = selectedValues.includes("pf");
                    const includeBayut = selectedValues.includes("bayut");
                    const includeDubizzle = selectedValues.includes("dubizzle");

                    for (i = 0; i < dataset.counts.length; i++){
                        let total = 0;
                        if (includePf){
                            total += parseFloat(pfCounts[i]);
                        }
                        if (includeBayut){
                            total += parseFloat(bayutCounts[i]);
                        }
                        if (includeDubizzle){
                            total += parseFloat(dubizzleCounts[i]);
                        }
                        counts.push(total);
                    }
                }else{
                    counts = dataset.counts;
                }
                const series = option.series;
                series[0].data = counts;
                myChart.setOption({ series: series});
            }

            $("#whats-app-insights-lead-hour-wise-select").select2({
                placeholder: "Select sources"
            })

            $("#whats-app-insights-lead-hour-wise-select").on("change", function (){
                regenerateChart($(this).val());
            })
        }
        function renderWhatsAppLeadInsightsData() {
            $.ajax({
                type: "POST",
                url: "{{ route('reports.lead-insights.getView') }}",
                data: {
                    groupIds,
                    type: "whatsAppInsights",
                    date
                },
                success: function (response) {
                    $('#chartCards').append(response);
                    renderWhatsAppLeadOverviewChart();
                    renderWhatsAppLeadDateWiseChart("whats-app-insights-lead-count-date-wise-chart", "");
                    renderWhatsAppLeadDateWiseChart("whats-app-insights-lead-response-rate-date-wise-chart", "%", true, true);
                    renderWhatsAppLeadDateWiseChart("whats-app-insights-lead-response-time-date-wise-chart", " Min", true);
                    renderWhatsAppLeadCountWeekDayWiseChart();
                    renderWhatsAppLeadCountHourWiseChart();

                    $('#loading-row').addClass('d-none');
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }
        //WhatsApp Insights Starts

        renderLeadInsightsData();
    </script>
@endsection
