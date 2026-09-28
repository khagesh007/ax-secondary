@extends('admin.layouts.master')
@section('title')
    Agent Insights
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
                        <h4 class="card-title mb-0 ">Agent Insights</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reporting</a></li>
                            <li class="breadcrumb-item active">Agent Insights</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include("admin.reports.agent_insights.partials._filter")
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
            $('#sidebarAgentInsightsReports').addClass('active');
            $("#reportsActive").addClass('active');
        });

        function renderOverviewInsightsView() {
            $.ajax({
                type: "POST",
                url: "{{ route('reports.agent-insights.getView') }}",
                data: {
                    groupIds,
                    type: "overviewInsights",
                    date
                },
                success: function (response) {
                    $('#chartCards').append(response);
                    renderAgentWiseInsightsView();
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function renderAgentWiseInsightsView() {
            $.ajax({
                type: "POST",
                url: "{{ route('reports.agent-insights.getView') }}",
                data: {
                    groupIds,
                    type: "agentWiseInsights",
                    date
                },
                success: function (response) {
                    $('#chartCards').append(response);

                    $('#loading-row').addClass('d-none');
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        renderOverviewInsightsView();
    </script>
@endsection
