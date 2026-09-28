@extends('admin.layouts.master')
@section('title')
    KPI
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .flex-container {
            display: flex;
            flex-wrap: wrap;
        }

        .flex-item {
            flex: 1;
        }

        .group-name {
            font-size: 14px;
            font-weight: 600;
        }

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
            top: 40px;
            z-index: 99999;
        }

        .sticky-top-with-margin-bottom {
            top: 95px;
            z-index: 9;
            background-color: #fff !important;
        }

        .sticky-top-duration {
            top: 10px;
            z-index: 99999;
        }

        .sticky-top-with-margin-bottom .card-body {
            padding: 6px !important;
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

        .responsive-div {
            width: 100%;
            height: 100%;
            min-height: 300px;
            /* Minimum height to ensure visibility */
            max-height: 400px;
            /* Maximum height as per your requirement */
            box-sizing: border-box;
        }
    </style>
    <style>
        .graph-container {
            overflow-x: auto;
            /* Horizontal scroll */
            overflow-y: hidden;
            /* Hide vertical scroll */
            max-width: 100%;
            /* Adjust the maximum width */
            height: 400px;
            /* Set the desired height */
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KPI Progress</a></li>
                            <li class="breadcrumb-item active">{{ $agent->full_name }}</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6">
            <div class="card  row-eq-height">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            <img src="{{ $agent->thumb_url }}"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">

                        </div>
                        <h5 class="fs-16 mb-1">{{ $agent->full_name }}</h5>
                        <p class="text-muted mb-0"><b>{{ $agent->position }}</b></p>

                        <div class="text-start ">
                            <p class="text-muted mt-2 mb-0"><b> WA Response Time:
                                </b>{{ getSecondsTohumanReadable($wa_average_diff, false) }}
                            </p>
                            <p class="text-muted mb-0"><span title="Your Total Number of listings in All States/Statuses"><i
                                        class=" ri-information-line"></i></span><b>Total Listings: </b>{{ $total_listings }}
                            </p>
                            <p class="text-muted mb-0"><span
                                    title="Average leads per day is calculated by dividing the total number of leads received by the agent since joining by the number of days since the agent joined. This provides an estimate of the average number of leads the agent receives daily since they started."><i
                                        class=" ri-information-line"></i></span><b> Average lead/Day:
                                </b>{{ $averageLeadsPerDay }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card  row-eq-height">
                <div class="card-header">
                    <h4 class="card-title">KPI Progress</h4>
                </div>
                <div class="card-body">
                    <div id="target-achieved" data-achieved="{{ $total_achieved }}" data-target="{{ $total_target }}"
                        class="responsive-div"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="row sticky-top-duration position-sticky">
        <div class="col-md-12">
            <div class="card p-2"
                style="
            /* background: #e9e9ee; */
            border: none;
            box-shadow: none;
            padding: 5px 5px !important;
        ">
                <div class="card-body p-1" style="overflow: scroll">
                    <div class="d-flex flex-nowrap">
                        <div class="me-1" style="min-width: 150px!important">
                            <input type="date" class="form-control-sm form-control" id="datefilters_start_dates"
                                name="search_start_dates" data-provider="flatpickr" data-date-format="d M, Y"
                                data-multiple-date="true" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="me-1" style="min-width: 150px!important">
                            <input type="date" class="form-control-sm form-control" id="datefilters_end_dates"
                                name="search_end_dates" data-provider="flatpickr" data-date-format="d M, Y"
                                data-multiple-date="true">
                        </div>
                        <div class="me-1">
                            <button class="btn btn-primary btn-sm applyBtn">Apply</button>
                        </div>
                        <div class="me-1">
                            <button class="btn btn-primary btn-sm resetBtn">Reset</button>
                        </div>

                        <div class="me-1">
                            <button class="btn text-truncate btn-outline-primary btn-primary btn-sm w-100 select-kpi"
                                data-kpi="All">All</button>
                        </div>
                        @foreach ($unique as $kpi)
                            <div class="me-1">
                                <button class="btn text-truncate btn-outline-primary btn-sm w-100 select-kpi"
                                    data-kpi="{{ $kpi->kpi }}">{{ $kpi->kpi }}</button>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>


    @foreach ($kpis as $kpi)
        @if ($kpi->stats)
            @php
                $startDate = strtotime($kpi->stats->start_date);
                $endDate = strtotime($kpi->stats->end_date);
                $duration = ($endDate - $startDate) / (60 * 60 * 24) + 1;
                $agentDailyAverage = $kpi->stats->achieved / $duration;
                $predictedKPI = $agentDailyAverage * $duration;

                $currentDate = time();

                // Calculate total duration in days
                $totalDuration = ($endDate - $startDate) / (60 * 60 * 24) + 1; // Adding 1 to include the end date

                // Calculate elapsed duration in days
                $elapsedDuration = ($currentDate - $startDate) / (60 * 60 * 24) + 1; // Adding 1 to include the current day

                // Calculate remaining duration in days
                $remainingDuration = $totalDuration - $elapsedDuration;

                // Calculate agent's daily average based on achieved so far
                $agentDailyAverage = $kpi->stats->achieved / $elapsedDuration;

                // Calculate predicted KPI value for the remaining duration
                $predictedRemainingKPI = $agentDailyAverage * $remainingDuration;

                // Calculate predicted total KPI value by the end of the period
                $predictedTotalKPI = $kpi->stats->achieved + $predictedRemainingKPI;
            @endphp
            @if ($kpi->kpi=='WhatsApp Response')
            <div class="row mb-3 selected-{{ replaceSpaces($kpi->kpi) }} all-kpis"
                data-target="{{ $kpi->stats?->target ?? 0 }}" data-achieved="{{ $kpi->stats?->achieved ?? 0 }}">
                <div class="col-md-6">
                    <div class="card row-eq-height">
                        <div class="card-header">
                            <h4 class="card-title">{{ $kpi->kpi }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                                <table class="table table-bordered table-striped table-hover">

                                    <tbody>
                                        <tr>
                                            <td><b>Status</b></td>
                                            <td><span
                                                    class="badge
                                                    {{ $kpi->is_processed == 1 ? 'bg-primary' : '' }}
                                                    {{ $kpi->is_processed == 2 ? 'bg-info' : '' }}
                                                    {{ $kpi->is_processed == 0 ? 'bg-warning' : '' }}">
                                                    {{ $kpi->is_processed == 1 ? 'Processed' : '' }}
                                                    {{ $kpi->is_processed == 2 ? 'In Progress' : '' }}
                                                    {{ $kpi->is_processed == 0 ? 'Pending' : '' }} </span></td>
                                        </tr>
                                        <tr>
                                            <td><b>KPI</b></td>
                                            <td>{{ $kpi->kpi }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Target</b></td>
                                            <td>{{ $kpi->stats->target }} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Daily Target Average</b> <span
                                                    title="The agent must maintain this average to achieve the target."><i
                                                        class=" ri-information-line"></i></span></td>
                                            <td> <span
                                                    title="{{ number_format($kpi->stats->average_per_day, 2) }}">{{ roundOff($kpi->stats->average_per_day) }}</span>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Agent Daily Average</b> <span
                                                    title="This value is based on the agent's performance so far."><i
                                                        class=" ri-information-line"></i></span></td>
                                            <td><span
                                                    class="{{ $agentDailyAverage > $kpi->stats->average_per_day ? 'text-danger' : 'text-success' }}">{{ number_format($agentDailyAverage, 2) }}</span>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td><b>Predicted KPI Value</b></td>
                                            <td>{{ number_format($predictedKPI, 2) }}</td>
                                        </tr> --}}

                                        {{-- <tr>
                                            <td><b>Achieved</b></td>
                                            <td>{{ $kpi->stats->achieved }}
                                                ({{ number_format($kpi->stats->achieved_percentage, 2) }}%)
                                                {!! $kpi->stats->achieved < $kpi->stats->target
                                                    ? ' <span class="text-danger">Below Target</span>'
                                                    : ' <span class="text-success">Above Target</span>' !!}
                                            </td>
                                        </tr> --}}

                                        @if ($currentDate < $endDate)
                                            <tr>
                                                <td><b>Predicted Total KPI Value</b><span
                                                        title="This value is based on your daily average."><i
                                                            class=" ri-information-line"></i></span></td>
                                                <td><span
                                                        class="{{ $predictedTotalKPI > $kpi->stats->target ? 'text-danger' : 'text-success' }}">{{ number_format($predictedTotalKPI, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><b>Duration</b></td>
                                            <td>{{ date('d/m/y', strtotime($kpi->stats->start_date)) }} -
                                                {{ date('d/m/y', strtotime($kpi->stats->end_date)) }}
                                                ({{ $kpi->duration }})
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card row-eq-height">
                        <div class="card-header">
                            <h4 class="card-title">{{ $kpi->kpi }}</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="kpi-data-{{ $kpi->id }}" class="kpi-dataset kpi-data-{{ $kpi->id }}"
                                data-dataset="{{ $kpi->stats->data }}" data-average="{{ $kpi->stats->average_per_day }}"
                                data-id="{{ $kpi->id }}"></canvas>
                        </div>

                    </div>
                </div>
            </div>
            @else
            <div class="row mb-3 selected-{{ replaceSpaces($kpi->kpi) }} all-kpis"
                data-target="{{ $kpi->stats?->target ?? 0 }}" data-achieved="{{ $kpi->stats?->achieved ?? 0 }}">
                <div class="col-md-6">
                    <div class="card row-eq-height">
                        <div class="card-header">
                            <h4 class="card-title">{{ $kpi->kpi }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                                <table class="table table-bordered table-striped table-hover">

                                    <tbody>
                                        <tr>
                                            <td><b>Status</b></td>
                                            <td><span
                                                    class="badge
                                                    {{ $kpi->is_processed == 1 ? 'bg-primary' : '' }}
                                                    {{ $kpi->is_processed == 2 ? 'bg-info' : '' }}
                                                    {{ $kpi->is_processed == 0 ? 'bg-warning' : '' }}">
                                                    {{ $kpi->is_processed == 1 ? 'Processed' : '' }}
                                                    {{ $kpi->is_processed == 2 ? 'In Progress' : '' }}
                                                    {{ $kpi->is_processed == 0 ? 'Pending' : '' }} </span></td>
                                        </tr>
                                        <tr>
                                            <td><b>KPI</b></td>
                                            <td>{{ $kpi->kpi }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Target</b></td>
                                            <td>{{ $kpi->stats->target }} </td>
                                        </tr>
                                        <tr>
                                            <td><b>Daily Target Average</b> <span
                                                    title="The agent must maintain this average to achieve the target."><i
                                                        class=" ri-information-line"></i></span></td>
                                            <td> <span
                                                    title="{{ number_format($kpi->stats->average_per_day, 2) }}">{{ roundOff($kpi->stats->average_per_day) }}</span>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Agent Daily Average</b> <span
                                                    title="This value is based on the agent's performance so far."><i
                                                        class=" ri-information-line"></i></span></td>
                                            <td><span
                                                    class="{{ $agentDailyAverage < $kpi->stats->average_per_day ? 'text-danger' : 'text-success' }}">{{ number_format($agentDailyAverage, 2) }}</span>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td><b>Predicted KPI Value</b></td>
                                            <td>{{ number_format($predictedKPI, 2) }}</td>
                                        </tr> --}}

                                        <tr>
                                            <td><b>Achieved</b></td>
                                            <td>{{ $kpi->stats->achieved }}
                                                ({{ number_format($kpi->stats->achieved_percentage, 2) }}%)
                                                {!! $kpi->stats->achieved < $kpi->stats->target
                                                    ? ' <span class="text-danger">Below Target</span>'
                                                    : ' <span class="text-success">Above Target</span>' !!}
                                            </td>
                                        </tr>

                                        @if ($currentDate < $endDate)
                                            <tr>
                                                <td><b>Predicted Total KPI Value</b><span
                                                        title="This value is based on your daily average."><i
                                                            class=" ri-information-line"></i></span></td>
                                                <td><span
                                                        class="{{ $predictedTotalKPI < $kpi->stats->target ? 'text-danger' : 'text-success' }}">{{ number_format($predictedTotalKPI, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><b>Duration</b></td>
                                            <td>{{ date('d/m/y', strtotime($kpi->stats->start_date)) }} -
                                                {{ date('d/m/y', strtotime($kpi->stats->end_date)) }}
                                                ({{ $kpi->duration }})
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card row-eq-height">
                        <div class="card-header">
                            <h4 class="card-title">{{ $kpi->kpi }}</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="kpi-data-{{ $kpi->id }}" class="kpi-dataset kpi-data-{{ $kpi->id }}"
                                data-dataset="{{ $kpi->stats->data }}" data-average="{{ $kpi->stats->average_per_day }}"
                                data-id="{{ $kpi->id }}"></canvas>
                        </div>

                    </div>
                </div>
            </div>
            @endif

        @endif
    @endforeach
@endsection
@section('script')

    <!-- dashboard init -->

    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>


    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>



    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>

@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

<script>
    var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';

    $(document).ready(function() {
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        function getDefaultDates() {
            var date = new Date();
            var start_of_month = new Date(date.getFullYear(), date.getMonth(), 1);
            var seven_days_later = new Date(start_of_month);
            seven_days_later.setDate(seven_days_later.getDate() + 7);

            return {
                start_from: formatDate(start_of_month),
                start_to: formatDate(seven_days_later),
                end_from: formatDate(start_of_month),
                end_to: formatDate(seven_days_later)
            };
        }

        var defaultDates = getDefaultDates();

        var start_from = getUrlParameter('start_from') || defaultDates.start_from;
        var start_to = getUrlParameter('start_to') || defaultDates.start_to;
        var end_from = getUrlParameter('end_from') || defaultDates.end_from;
        var end_to = getUrlParameter('end_to') || defaultDates.end_to;

        $('#datefilters_start_dates').flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            mode: "range",
            defaultDate: [start_from, start_to]
        });

        $('#datefilters_end_dates').flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            mode: "range",
            defaultDate: [end_from, end_to]
        });
    });


    $(document).ready(function() {



        $(document).on('click', '.applyBtn', function() {
            var start_dates = $('#datefilters_start_dates').val();
            if (start_dates.includes(' to ')) {
                start_dates = start_dates.split(' to ');
                start_dates[0] = start_dates[0].replace(/\//g, '-');
                start_dates[1] = start_dates[1].replace(/\//g, '-');
                from_date = start_dates[0];
                to_date = start_dates[1];
            } else {
                from_date = start_dates.replace(/\//g, '-');
                to_date = start_dates.replace(/\//g, '-');
            }



            var end_dates = $('#datefilters_end_dates').val();
            if (end_dates.includes(' to ')) {
                end_dates = end_dates.split(' to ');
                end_dates[0] = end_dates[0].replace(/\//g, '-');
                end_dates[1] = end_dates[1].replace(/\//g, '-');
                end_from_date = end_dates[0];
                end_to_date = end_dates[1];
            } else {
                end_from_date = end_dates.replace(/\//g, '-');
                end_to_date = end_dates.replace(/\//g, '-');
            }



            var url = new URL(window.location.href);
            url.searchParams.set('start_from', from_date);
            url.searchParams.set('start_to', to_date);
            url.searchParams.set('end_from', end_from_date);
            url.searchParams.set('end_to', end_to_date);

            window.location.href = url.toString();
        });



        $(document).on('click', '.resetBtn', function() {
            var url = new URL(window.location.href);
            url.searchParams.delete('start_from');
            url.searchParams.delete('start_to');
            url.searchParams.delete('end_from');
            url.searchParams.delete('end_to');

            window.location.href = url.toString();

        });

        $('.kpi-dataset').each(function() {
            var data = $(this).data('dataset');
            var average = $(this).data('average');
            var id = $(this).data('id');
            var chartClass = 'kpi-data-' + id;

            $average = $(this).data('average');

            var dates = Object.keys(data);
            var counts = Object.values(data);

            var average = Array(counts.length).fill(average);


            meetingGraph(chartClass, counts, average, dates, 'Module', 'line');
        });


        $('.select-kpi').click(function() {
            $('.select-kpi').removeClass('btn-primary');
            $(this).addClass('btn-primary');
            var totalTarget = 0;
            var totalAchieved = 0;
            var kpi = $(this).data('kpi');
            if (kpi == 'All') {
                $('.all-kpis').show();

                $('.all-kpis').each(function() {
                    totalTarget += $(this).data('target');
                    totalAchieved += $(this).data('achieved');
                });
                createGaugeChart('target-achieved', totalTarget, totalAchieved);
                return;
            }
            $('.all-kpis').hide();
            $('.selected-' + replaceSpaces(kpi)).show();

            // foreach .selected-replaceSpaces(kpi) and sum up the target and achieved

            $('.selected-' + replaceSpaces(kpi)).each(function() {
                totalTarget += $(this).data('target');
                totalAchieved += $(this).data('achieved');
            });
            createGaugeChart('target-achieved', totalTarget, totalAchieved);


        });
        // target-achieved
        createGaugeChart('target-achieved', $('#target-achieved').data('target'), $('#target-achieved').data(
            'achieved'));
    });

    function replaceSpaces(string) {
        return string.replace(/\s/g, '-');
    }

    function meetingGraph(element, leads, average, names, text, type) {
        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');

        var gradientStrokeTwo = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStrokeTwo.addColorStop(0, '#646262');
        gradientStrokeTwo.addColorStop(1, '#646262');

        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.5, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');

        gradientTwo = ctx.createLinearGradient(0, 0, 0, 450);
        gradientTwo.addColorStop(0, 'rgba(66, 65, 65, 1)');
        gradientTwo.addColorStop(0.5, 'rgba(66, 65, 65, 0)');
        gradientTwo.addColorStop(1, 'rgba(255, 0, 0, 0)');

        var myChart = new Chart(ctx, {
            type: type,
            data: {
                labels: names,
                datasets: [{
                        borderColor: gradientStroke,
                        pointBorderColor: gradientStroke,
                        pointBackgroundColor: gradientStroke,
                        pointHoverBackgroundColor: gradientStroke,
                        pointHoverBorderColor: gradientStroke,
                        borderWidth: 1,
                        backgroundColor: gradient,
                        pointBorderColor: "#fff",
                        fill: true,
                        label: text, // Label for the second dataset
                        data: leads,
                        barThickness: 5, // Set the width of the bars

                    },
                    {
                        borderColor: gradientStrokeTwo,
                        pointBorderColor: gradientStrokeTwo,
                        pointBackgroundColor: gradientStrokeTwo,
                        pointHoverBackgroundColor: gradientStrokeTwo,
                        pointHoverBorderColor: gradientStrokeTwo,
                        borderWidth: 1,
                        backgroundColor: gradientTwo,
                        pointBorderColor: "#fff",
                        fill: false,
                        label: 'Average', // Label for the second dataset
                        data: average,
                        barThickness: 5, // Set the width of the bars
                        pointRadius: 0, // Points are not visible

                    }
                ]
            },
            options: {
                indexAxis: 'x', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 2.5,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: true,
                            drawBorder: false,
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                }
            }
        });
    }

    function createGaugeChart(element, target, achieved) {
        var chartElement = document.getElementById(element);

        var chart = echarts.init(chartElement);

        if (chart) {
            chart.dispose();
            chart = echarts.init(chartElement);
        }

        let value = achieved;
        var option = {
            series: [{
                type: 'gauge',
                startAngle: 180,
                endAngle: 0,
                min: 0,
                max: target,
                splitNumber: 12,
                itemStyle: {
                    color: '#58D9F9',
                    shadowColor: 'rgba(0,138,255,0.45)',
                    shadowBlur: 10,
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                },
                progress: {
                    show: true,
                    roundCap: true,
                    width: 6 // Reduced width for smaller chart
                },
                pointer: {
                    icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                    length: '75%',
                    width: 2, // Reduced width for smaller chart
                    offsetCenter: [0, '5%']
                },
                axisLine: {
                    lineStyle: {
                        width: 15,
                        color: [
                            [0.3, '#fd666d'],
                            [0.7, '#efe707'],
                            [1, '#07ef3f']
                        ]
                    }
                },
                axisTick: {
                    show: false // Hide the split lines
                },
                splitLine: {
                    show: true // Hide the split lines
                },
                axisLabel: {
                    distance: 20,
                    color: '#999',
                    fontSize: 8,
                    formatter: function(value) {
                        return Math.ceil(value);
                    }
                },
                title: {
                    show: true
                },
                detail: {
                    backgroundColor: '#fff',
                    borderColor: '#999',
                    borderWidth: 2, // Reduced border width
                    width: '60%',
                    lineHeight: 15, // Adjusted for smaller chart
                    height: 15, // Adjusted for smaller chart
                    borderRadius: 1, // Adjusted for smaller chart
                    offsetCenter: [0, '45%'], // Adjusted position
                    valueAnimation: true,
                    formatter: function(achieved) {
                        return '{value|' + achieved.toFixed(0) + '/' + target + '}';
                    },
                    rich: {
                        value: {
                            fontSize: 12, // Smaller font size for value
                            fontWeight: 'bolder',
                            color: '#777'
                        },
                        unit: {
                            fontSize: 6, // Smaller font size for unit
                            color: '#999',
                            padding: [0, 0, -5, 5] // Adjusted padding for smaller chart
                        }
                    }
                },
                data: [{
                    value: achieved
                }]
            }]
        };

        chart.setOption(option);

        // Resize chart on window resize
        window.addEventListener('resize', function() {
            chart.resize();
        });
    }

    function __createGaugeChart(element, target, achieved) {
        var chartElement = document.getElementById(element);

        var chart = echarts.init(chartElement);

        if (chart) {
            chart.dispose();
            chart = echarts.init(chartElement);
        }

        let value = achieved;
        var option = {
            backgroundColor: '#000',
            tooltip: {
                formatter: '{a} <br/>{b} : {c}%'
            },
            toolbox: {
                feature: {
                    restore: {},
                    saveAsImage: {}
                }
            },
            series: [
                // left
                {
                    name: 'gauge 0',
                    type: 'gauge',
                    min: -200,
                    max: 250,
                    startAngle: -30,
                    endAngle: -315,
                    splitNumber: 9,
                    radius: '35%',
                    center: ['21%', '55%'],
                    axisLine: {
                        lineStyle: {
                            color: [
                                [1, '#AE96A6']
                            ]
                        }
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: false
                    },
                    anchor: {},
                    pointer: {
                        show: false
                    },
                    detail: {
                        show: false
                    },
                    title: {
                        fontSize: 12,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        color: '#fff',
                        offsetCenter: [0, '-60%']
                    },
                    progress: {
                        show: true,
                        width: 3,
                        itemStyle: {
                            color: '#fff'
                        }
                    },
                    data: [{
                        value: 250,
                        name: 'km/h'
                    }]
                },
                {
                    name: 'gauge 1',
                    type: 'gauge',
                    min: 0,
                    max: 250,
                    startAngle: -140,
                    endAngle: -305,
                    splitNumber: 5,
                    radius: '35%',
                    center: ['21%', '55%'],
                    axisLine: {
                        lineStyle: {
                            color: [
                                [1, '#AE96A6']
                            ]
                        }
                    },
                    splitLine: {
                        distance: -7,
                        length: 12,
                        lineStyle: {
                            color: '#fff',
                            width: 4
                        }
                    },
                    axisTick: {
                        distance: -8,
                        length: 8,
                        lineStyle: {
                            color: '#fff',
                            width: 2
                        }
                    },
                    axisLabel: {
                        distance: 14,
                        fontSize: 18,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        color: '#fff'
                    },
                    anchor: {},
                    pointer: {
                        icon: 'path://M-36.5,23.9L-41,4.4c-0.1-0.4-0.4-0.7-0.7-0.7c-0.5-0.1-1.1,0.2-1.2,0.7l-4.5,19.5c0,0.1,0,0.1,0,0.2v92.3c0,0.6,0.4,1,1,1h9c0.6,0,1-0.4,1-1V24.1C-36.5,24-36.5,23.9-36.5,23.9z M-39.5,114.6h-5v-85h5V114.6z',
                        width: 5,
                        length: '40%',
                        offsetCenter: [0, '-58%'],
                        itemStyle: {
                            color: '#f00',
                            shadowColor: 'rgba(255, 0, 0)',
                            shadowBlur: 5,
                            shadowOffsetY: 2
                        }
                    },
                    title: {
                        color: '#fff',
                        fontSize: 14,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        offsetCenter: [0, 0]
                    },
                    detail: {
                        show: false
                    },
                    data: [{
                        value: 0,
                        name: '当前位置：\n \n 中科路'
                    }]
                },
                // middle
                {
                    name: 'gauge 2',
                    type: 'gauge',
                    min: 0,
                    max: 8,
                    z: 10,
                    startAngle: 210,
                    endAngle: -30,
                    splitNumber: 8,
                    radius: '50%',
                    center: ['50%', '50%'],
                    axisLine: {
                        show: true,
                        lineStyle: {
                            width: 0,
                            color: [
                                [0.825, '#fff'],
                                [1, '#f00']
                            ]
                        }
                    },
                    splitLine: {
                        distance: 20,
                        length: 15,
                        lineStyle: {
                            color: 'auto',
                            width: 4,
                            shadowColor: 'rgba(255, 255, 255, 0.5)',
                            shadowBlur: 15,
                            shadowOffsetY: -10
                        }
                    },
                    axisTick: {
                        distance: 20,
                        length: 8,
                        lineStyle: {
                            color: 'auto',
                            width: 2,
                            shadowColor: 'rgba(255, 255, 255)',
                            shadowBlur: 10,
                            shadowOffsetY: -10
                        }
                    },
                    axisLabel: {
                        distance: 10,
                        fontSize: 35,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        color: '#fff'
                    },
                    anchor: {},
                    pointer: {
                        icon: 'path://M-36.5,23.9L-41,4.4c-0.1-0.4-0.4-0.7-0.7-0.7c-0.5-0.1-1.1,0.2-1.2,0.7l-4.5,19.5c0,0.1,0,0.1,0,0.2v92.3c0,0.6,0.4,1,1,1h9c0.6,0,1-0.4,1-1V24.1C-36.5,24-36.5,23.9-36.5,23.9z',
                        width: 10,
                        offsetCenter: [0, '-10%'],
                        length: '75%',
                        itemStyle: {
                            color: '#f00',
                            shadowColor: 'rgba(255, 0, 0)',
                            shadowBlur: 5,
                            shadowOffsetY: 3
                        }
                    },
                    title: {
                        color: '#fff',
                        fontSize: 12,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        offsetCenter: [0, '-50%']
                    },
                    data: [{
                        value: value,
                        name: '1/min x 1000'
                    }],
                    detail: {
                        show: false
                    }
                },
                {
                    name: 'gauge 3',
                    type: 'gauge',
                    min: 0,
                    max: 8,
                    z: 10,
                    splitNumber: 8,
                    radius: '50%',
                    axisLine: {
                        lineStyle: {
                            width: 14,
                            color: [
                                [1, '#000']
                            ]
                        }
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: false
                    },
                    anchor: {},
                    pointer: {
                        show: false
                    },
                    title: {
                        show: false
                    },
                    detail: {
                        offsetCenter: ['25%', '50%'],
                        formatter: '{a|{value}}{b|km/h}',
                        rich: {
                            a: {
                                fontSize: 60,
                                fontWeight: 800,
                                fontFamily: 'Arial',
                                color: '#fff',
                                align: 'center',
                                padding: [0, 5, 0, 0]
                            },
                            b: {
                                fontSize: 14,
                                fontWeight: 800,
                                fontFamily: 'Arial',
                                color: '#fff',
                                padding: [0, 0, 20, 0]
                            }
                        }
                    },
                    data: [{
                        value: value,
                        name: ''
                    }]
                },
                // right
                {
                    name: 'gauge 4',
                    type: 'gauge',
                    min: 0,
                    max: 8,
                    startAngle: 135,
                    endAngle: -150,
                    splitNumber: 8,
                    radius: '35%',
                    center: ['79%', '55%'],
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        distance: -7,
                        length: 12,
                        lineStyle: {
                            color: '#fff',
                            width: 4
                        }
                    },
                    axisTick: {
                        distance: -8,
                        length: 8,
                        lineStyle: {
                            color: '#fff',
                            width: 2
                        }
                    },
                    axisLabel: {
                        distance: 14,
                        fontSize: 18,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        color: '#fff'
                    },
                    anchor: {},
                    pointer: {
                        icon: 'path://M-36.5,23.9L-41,4.4c-0.1-0.4-0.4-0.7-0.7-0.7c-0.5-0.1-1.1,0.2-1.2,0.7l-4.5,19.5c0,0.1,0,0.1,0,0.2v92.3c0,0.6,0.4,1,1,1h9c0.6,0,1-0.4,1-1V24.1C-36.5,24-36.5,23.9-36.5,23.9z M-39.5,114.6h-5v-85h5V114.6z',
                        width: 5,
                        length: '40%',
                        offsetCenter: [0, '-58%'],
                        itemStyle: {
                            color: '#f00',
                            shadowColor: 'rgba(255, 0, 0)',
                            shadowBlur: 5,
                            shadowOffsetY: 2
                        }
                    },
                    title: {
                        color: '#fff',
                        fontSize: 14,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        offsetCenter: [0, 0]
                    },
                    detail: {
                        show: false
                    },
                    data: [{
                        value: value,
                        name: '1/min x 1000'
                    }]
                },
                {
                    name: 'gauge 5',
                    type: 'gauge',
                    min: -200,
                    max: 250,
                    startAngle: 30,
                    endAngle: -255,
                    splitNumber: 9,
                    radius: '35%',
                    center: ['79%', '55%'],
                    axisLine: {
                        lineStyle: {
                            color: [
                                [1, '#AE96A6']
                            ]
                        }
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        show: false
                    },
                    anchor: {},
                    pointer: {
                        show: false
                    },
                    detail: {
                        show: false
                    },
                    title: {
                        fontSize: 12,
                        fontWeight: 800,
                        fontFamily: 'Arial',
                        color: '#fff',
                        offsetCenter: [0, '-60%']
                    },
                    progress: {
                        show: true,
                        width: 3,
                        itemStyle: {
                            color: '#fff'
                        }
                    },
                    data: [{
                        value: 250,
                        name: 'km/h'
                    }]
                }
            ]
        };

        chart.setOption(option);

        // Resize chart on window resize
        window.addEventListener('resize', function() {
            chart.resize();
        });
    }

    function ddcreateGaugeChart(element, target, achieved) {
        var chartElement = document.getElementById(element);

        var chart = echarts.init(chartElement);

        if (chart) {
            chart.dispose();
            chart = echarts.init(chartElement);
        }

        var option = {
            series: [{
                type: 'gauge',
                startAngle: 180,
                endAngle: 0,
                min: 0,
                max: target,
                splitNumber: 12,
                itemStyle: {
                    color: '#58D9F9',
                    shadowColor: 'rgba(0,138,255,0.45)',
                    shadowBlur: 10,
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                },
                progress: {
                    show: true,
                    roundCap: true,
                    width: 6
                },
                pointer: {
                    icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                    length: '75%',
                    width: 4,
                    offsetCenter: [0, '5%']
                },
                axisLine: {
                    lineStyle: {
                        width: 30,
                        color: [
                            [0.3, '#67e0e3'],
                            [0.7, '#37a2da'],
                            [1, '#fd666d']
                        ]
                    }
                },
                axisTick: {
                    distance: -30,
                    length: 8,
                    lineStyle: {
                        color: '#fff',
                        width: 2
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
                    distance: 40,
                    fontSize: 20
                },
                detail: {
                    backgroundColor: '#fff',
                    borderColor: '#999',
                    borderWidth: 1,
                    width: '60%',
                    lineHeight: 15,
                    height: 15,
                    borderRadius: 3,
                    offsetCenter: [0, '45%'],
                    valueAnimation: true,
                    formatter: function(achieved) {
                        return '{value|' + achieved.toFixed(0) + '/' + target + '}';
                    },
                    rich: {
                        value: {
                            fontSize: 10,
                            fontWeight: 'bolder',
                            color: '#777'
                        },
                        unit: {
                            fontSize: 6,
                            color: '#999',
                            padding: [0, 0, -5, 5]
                        }
                    }
                },
                data: [{
                    value: achieved
                }]
            }]
        };

        chart.setOption(option);

        // Resize chart on window resize
        window.addEventListener('resize', function() {
            chart.resize();
        });


    }
</script>




@yield('pageDT')
@stack('in_page_scripts')

@endsection
