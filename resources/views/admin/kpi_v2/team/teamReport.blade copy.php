@extends('admin.layouts.master')
@section('title')
    KPI
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .avatar-title {
            background: white !important;
        }

        .apexcharts-canvas {
            margin-left: auto;
            margin-right: auto;
        }

        .trend-margin-top {
            margin-top: -22px;
        }

        .bg-light-subtle {
            background-color: #f5f5f5 !important;
        }
        .info-btn{
            position: absolute;
    margin-top: 2px;
    opacity: 0.5;
        }
    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Team {{ $group->name }} - <span class="text-muted fs-12">Key Performance Indicator Report</span></h4>
                        <ol class="breadcrumb m-0 text-muted">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kpi</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('reports.team.kpi-v2.team.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">View</li>
                            <li class="breadcrumb-item active">{{ $group->name }}</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex">
                                <input type="text" class="form-control" name="month"
                                    value="{{ request('month') ? date('F Y', strtotime(request('month'))) : date('F Y') }}"
                                    id="month" placeholder="Select Month">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (count($reports) == 0)
        <div class="alert alert-warning" role="alert">
            No target set for this month
        </div>
    @endif
    @if (count($reports) > 0)
        <div class="row">
            <div class="col-3 ">
                <div class="card">
                    <div class="card-body">
                        <a class="d-flex align-items-center" aria-expanded="false" aria-controls="teamTileCard4">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light rounded-circle fs-3 ax-text">
                                    {{ $group?->name }}
                                </span>
                            </div>


                            <div class="flex-grow-1 ms-3" style="direction: rtl;
    margin-right: 5px;">
                                <h6 class="fs-14 mb-1"><span class="text-muted">{{ $group?->user?->full_name }}</span>
                                    <b></b>
                                </h6>


                                <p class="text-muted fs-10 mb-0">{{ $group->user->position }}</p>



                            </div>
                            <div class="flex-shrink-0">
                                <img src="{{ $group?->user?->thumbnail }}" alt="" class="avatar-xs rounded">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <b>Overall Progress
                            <span id="overallProgressNumber" class="text-muted fs-12"></span>
                        </b>
                        <div class="progress animated-progress custom-progress mb-1" id="overall_progress_div">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">

                    <div class="card-header p-0 border-0 bg-light-subtle">
                        <div class="row g-0 text-center">
                            <div class="col-6">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1"><span class="counter-value" data-target="900">990</span>k
                                        <span class="text-success ms-1 fs-12">49%<i
                                                class="ri-arrow-right-up-line ms-1 align-middle"></i></span>
                                    </h5>
                                    <p class="text-muted mb-0">Avg. Sales Revenue</p>
                                </div>
                            </div>
                            <!--end col-->

                            <!--end col-->
                            <div class="col-6">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">
                                    <h5 class="mb-1">
                                        <span class="counter-value" data-target="1">0</span>M
                                        <span class="text-success ms-1 fs-12">37%<i
                                                class="ri-arrow-right-up-line ms-1 align-middle"></i></span>
                                    </h5>
                                    <p class="text-muted mb-0">Avg. Rental Revenue</p>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div>
                    <div class="card-body">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            @php
                                $total_target = 0;
                                $total_achievement = 0;
                            @endphp
                            @foreach ($reports ?? [] as $target)

                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i
                                                class="ri {{ $target->kpi == 'listings' ? 'ri-question-answer-line' : ($target->kpi == 'viewings' ? 'ri-mac-line' : 'ri-earth-line') }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ getKpiFullText($target->kpi) }}
                                                ({{ formatNumberMetricPrefixes($target?->result?->achieved ?? 0) }}/{{ formatNumberMetricPrefixes($target?->target) }})
                                                @php
                                                    $total_target += $target?->target;
                                                    $total_achievement += $target?->result?->achieved;
                                                @endphp
                                                <a href="javascript:void(0)"
                                                    onclick="toggleColumnCards('{{ $target->kpi }}')" class="float-end">
                                                    <i class=" bx bx-info-circle"></i>
                                                </a>
                                            </h6>
                                            <div class="progress animated-progress custom-progress mb-1">
                                                <div class="progress-bar bg-{{ ($target?->result?->achieved_percentage ?? 0) >= 75 ? 'success' : (($target->result->achieved_percentage ?? 0) >= 50 ? 'warning' : 'danger') }}"
                                                    role="progressbar"
                                                    style="width: {{ $target?->result?->achieved_percentage ?? 0 }}%"
                                                    aria-valuenow="{{ $target?->result?->achieved_percentage ?? 0 }}"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                   <span style="margin-top:-4px;">{{ $target?->result?->achieved_percentage ?? 0 }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="row">
                            <div class="col-12 mt-2 text-muted">
                                @php
                                $achived_percentage = ($total_achievement / $total_target) * 100;

                            @endphp
                            {{round($achived_percentage,2)}}% Target Achived So Far (AED {{number_format($total_achievement)}})
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="">
                            <div class="card card-height-100">
                                <div class="d-flex">
                                    <div class="flex-grow-1 p-3">
                                        <h5 class="mb-3" >Total Deals <span class="cursor-pointer info-btn" onclick="getTeamExtraData('total_deals')">
                                            <i class="bx bx-info-circle"></i>
                                        </span></h5>
                                        <h4 class="mb-0"><span id="totalDealCountCard"></span></h4>
                                        <div class="align-self-end float-end trend-margin-top totalDealCountTrend">

                                        </div>
                                    </div>
                                    <div>
                                        <div class="apex-charts apexcharts-tooltip-active" dir="ltr" id="results_sparkline_charts3" style="min-height: 86.9565px;">
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 d-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-primary rounded-circle fs-3">
                                            <i class="mdi mdi-handshake-outline ax-text fs-20"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1"> Total Deals

                                        </p>
                                        <h4 class="mb-0" ><span>0</span>

                                        </h4>
                                        <div id="chart1"></div>
                                    </div>



                                </div>

                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-light text-primary rounded-circle fs-3">
                                            <i class="bx bx-dollar-circle ax-text fs-20"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1"> Gross Revenue
                                            <a href="javascript:void(0)" onclick="getTeamExtraData('gross_revenue')"
                                                class="float-end">
                                                <i class=" bx bx-info-circle"></i>
                                            </a>
                                        </p>
                                        <h5 class=" mb-0" id="totalGrossRevenueCountCard">AED <span
                                                class="counter-value" data-target="20000">0</span></h5>
                                    </div>

                                </div>
                                <div class="align-self-end float-end trend-margin-top totalGrossRevenueTrend">

                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-6">
                        <div class="">
                            <div class="card card-height-100">
                                <div class="d-flex">
                                    <div class="flex-grow-1 p-3">
                                        <h5 class="mb-3" >Sales Deal <span class="cursor-pointer info-btn" onclick="getTeamExtraData('sales_deal_count')">
                                            <i class="bx bx-info-circle"></i>
                                        </span></h5>
                                        <h4 class="mb-0"><span id="totalSalesDealCountCard"></span></h4>
                                        <div class="align-self-end float-end trend-margin-top totalSalesDealCountTrend">

                                        </div>
                                    </div>
                                    <div>
                                        <div class="apex-charts apexcharts-tooltip-active" dir="ltr" id="salesDealCardChart" style="min-height: 86.9565px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <div class="card card-height-100">
                                <div class="d-flex">
                                    <div class="flex-grow-1 p-3">
                                        <h6 class="mb-3" >Rental Deals <span class="cursor-pointer info-btn" onclick="getTeamExtraData('rental_deals_count')">
                                            <i class="bx bx-info-circle"></i>
                                        </span></h6>
                                        <h4 class="mb-0"><span id="totalRentalDealCountCard"></span></h4>
                                        <div class="align-self-end float-end trend-margin-top totalRentalDealCountTrend">

                                        </div>
                                    </div>
                                    <div>
                                        <div class="apex-charts apexcharts-tooltip-active" dir="ltr" id="rentalDealCardChart" style="min-height: 86.9565px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>


            <div class="col-6">
                <div class="card">

                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0"><span id="status-wise-title">Compare Data</span></p>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#compare-pi-chart-tab"
                                        role="tab" aria-selected="true">
                                      Revenue
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#compare-commmission-tab"
                                        role="tab" aria-selected="false">
                                      Commission
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#compare-chart-tab" role="tab"
                                        aria-selected="true">
                                        <i class="ri-numbers-line"></i>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#compare-table-tab" role="tab"
                                        aria-selected="false" tabindex="-1">
                                        <i class="ri-table-line"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content text-muted">
                            <div class="tab-pane active show" id="compare-pi-chart-tab" role="tabpanel">
                                <div id="comparePieChart"></div>
                            </div>
                            <div class="tab-pane hide" id="compare-commmission-tab" role="tabpanel">
                                <div id="commissionPieChart"></div>
                            </div>
                            <div class="tab-pane  hide" id="compare-chart-tab" role="tabpanel">
                                <div id="compareBarChart" style="height: 200px"></div>
                            </div>
                            <div class="tab-pane hide" id="compare-table-tab" role="tabpanel">
                                <!-- Bordered Tables -->
                                <table class="table table-bordered table-nowrap">
                                    <tbody>
                                        <tr>

                                            <td>Rental Deal Count</td>
                                            <td class="rental_deal_count">0</td>
                                            <td>Sales Deal Count</td>
                                            <td class="sales_deal_count">0</td>
                                        </tr>
                                        <tr>
                                            <td>Avg. Rental Revenue</td>
                                            <td class="avg_rental_revenue">0</td>
                                            <td>Avg. Sales Revenue</td>
                                            <td class="avg_sales_revenue">0</td>
                                        </tr>
                                        <tr>
                                            <td>Avg. Rental Commission</td>
                                            <td class="avg_rental_commission">0</td>
                                            <td>Avg. Sales Commission</td>
                                            <td class="avg_sales_commission">0</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 d-none">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0">Status Wise</p>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#status-wise-pie-chart"
                                        role="tab" aria-selected="true">
                                        <i class="ri-pie-chart-line"></i>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#status-wise--list-table"
                                        role="tab" aria-selected="false" tabindex="-1">
                                        <i class="ri-table-line"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content text-muted">
                            <div class="tab-pane active show" id="status-wise-pie-chart" role="tabpanel">
                                <div id="status-pie-chart" style="height: 200px">

                                </div>
                            </div>
                            <div class="tab-pane" id="status-wise--list-table" role="tabpanel">
                                <table class="table" id="status-table">
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 mt-2 d-none" id="dayWiseReportCard">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0"><span class="selectedKpi"></span> Day Wise Report
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="dayWiseChart" style="min-height: 365px;" class="">

                        </div>
                    </div>
                </div>
            </div>

            @foreach ($targets ?? [] as $index => $target)
                <div class="col-6 target-cols {{ $target->kpi }}-cols {{ $index != 0 ? 'd-none' : '' }}">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <div class="flex-grow-1 oveflow-hidden">
                                <p class="text-muted text-truncates mb-0">{{ $target->kpi }} Details</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <td><b>Status</b></td>
                                        <td>
                                            <span
                                                class="badge
                                        {{ $target->is_processed == 1 ? 'bg-primary' : '' }}
                                        {{ $target->is_processed == 2 ? 'bg-info' : '' }}
                                        {{ $target->is_processed == 0 ? 'bg-warning' : '' }}">
                                                {{ $target->is_processed == 1 ? 'Processed' : '' }}
                                                {{ $target->is_processed == 2 ? 'In Progress' : '' }}
                                                {{ $target->is_processed == 0 ? 'Pending' : '' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Target</b></td>
                                        <td>{{ $target->target }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Daily Target Average</b> <span
                                                title="The agent must maintain this average to achieve the target."><i
                                                    class=" ri-information-line"></i></span></td>
                                        <td>
                                            <span
                                                title="{{ number_format($target->result->average_per_day ?? 0, 2) }}">{{ roundOff($target->result->average_per_day ?? 0) }}</span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Agent Daily Average</b>
                                            <span title="This value is based on the agent's performance so far.">
                                                <i class=" ri-information-line"></i>
                                            </span>
                                        </td>
                                        <td>
                                            @php $agentDailyAverage = ($target->result->achieved ?? 0) / $target->duration; @endphp
                                            <span
                                                class="{{ $agentDailyAverage > ($target->result->average_per_day ?? 0) ? 'text-danger' : 'text-success' }}">{{ number_format($agentDailyAverage, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Achieved</b></td>
                                        <td>{{ $target->result->achieved ?? 0 }}
                                            ({{ number_format($target->result->achieved_percentage ?? 0, 2) }}%)
                                            {!! ($target->result->achieved ?? 0) < $target->target
                                                ? ' <span class="text-danger">Below Target</span>'
                                                : ' <span class="text-success">Above Target</span>' !!}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><b>Duration</b></td>
                                        <td>{{ date('d/m/y', strtotime($target->start_date)) }} -
                                            {{ date('d/m/y', strtotime($target->end_date)) }}
                                            ({{ $target->duration }})
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6 target-cols {{ $target->kpi }}-cols">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <div class="flex-grow-1 oveflow-hidden">
                                <p class="text-muted text-truncates mb-0">{{ $target->kpi }} Date Wise</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="{{ $target->kpi }}-date-wise-chart" class="date-wise-chart"
                                data-dataset="{{ json_encode($target->result->data ?? []) }}" style="height: 300px">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 mb-3">
            <div class="">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="chart-type" id="agent-report-type-revenue" checked
                            onclick="toggleAgentReportView('revenue')">
                        <label class="btn btn-outline-primary " for="agent-report-type-revenue"
                            style="font-size: 15px">Revenue</label>

                        <input type="radio" class="btn-check" name="chart-type" id="agent-report-type-kpi"
                            onclick="toggleAgentReportView('kpi')">
                        <label class="btn btn-outline-primary " for="agent-report-type-kpi"
                            style="font-size: 15px">KPI</label>
                    </div>
                </div>
            </div>


        </div>
        <div class="row" id="agent-cards"></div>
        <div class="row d-none" id="agent-kpi-cards"></div>
    @endif
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@yield('pageDT')
@stack('in_page_scripts')
<script>
    let filtersData = [];
    let total_target = {{ $total_target ?? 0 }};
    let total_achievement = {{ $total_achievement ?? 0 }};
    let total_achievement_percentage = (total_achievement / total_target) * 100;
    let total_achievement_color = total_achievement_percentage >= 75 ? 'success' : (total_achievement_percentage >= 50 ?
        'warning' : 'danger');
    $("#overall_progress_div").html(
        `<div class="progress-bar bg-${total_achievement_color}" role="progressbar" style="width: ${total_achievement_percentage}%"
                                 aria-valuenow="${total_achievement_percentage}" aria-valuemin="0" aria-valuemax="100"></div>`
        );
    $("#overallProgressNumber").text(
        `${numberFormat(total_achievement)} / ${numberFormat(total_target)} (${total_achievement_percentage.toFixed(2)}%)`
        )

    let this_month_total_deals = 0;
    let this_month_sales_deal_count = 0;
    let this_month_rental_deals_count = 0;
    let this_month_gross_revenue = 0;

    let previous_month_total_deals = 0;
    let previous_month_sales_deal_count = 0;
    let previous_month_rental_deals_count = 0;
    let previous_month_gross_revenue = 0;



    function convertMonthToDateFormat(dateString) {
        // Parse the input string in "F Y" format (e.g., "January 2025")
        const parts = dateString.split(' ');

        if (parts.length !== 2) {
            throw new Error("Invalid date format. Expected format: 'Month YYYY'");
        }

        const monthName = parts[0];
        const year = parts[1];

        // Map month names to their corresponding numbers
        const monthMap = {
            January: '01',
            February: '02',
            March: '03',
            April: '04',
            May: '05',
            June: '06',
            July: '07',
            August: '08',
            September: '09',
            October: '10',
            November: '11',
            December: '12',
        };

        const month = monthMap[monthName];

        if (!month || !/^\d{4}$/.test(year)) {
            throw new Error("Invalid date components. Ensure the format is 'Month YYYY' and the year is valid.");
        }

        return `${year}-${month}`;
    }

    $(document).ready(function() {
        $("#month").datepicker({
            format: {
                toDisplay: function(date, format, language) {
                    var d = new Date(date);
                    var options = {
                        year: 'numeric',
                        month: 'long'
                    };
                    return d.toLocaleDateString(language, options);
                },
                toValue: function(date, format, language) {
                    var d = new Date(date);
                    var year = d.getFullYear();
                    var month = (d.getMonth() + 1).toString().padStart(2, '0');
                    return `${year}-${month}`;
                }
            },
            startView: "months",
            minViewMode: "months",
            autoclose: true,
        });
    })


    $(document).on("change", "#month", function() {
        window.location = "?month=" + convertMonthToDateFormat($(this).val())
    })
    @if (count($reports) > 0)
        const toggleColumnCards = (key) => {
            $(".target-cols").addClass("d-none");
            $(`.${key}-cols`).removeClass("d-none");
            getBreakdown(key)
        }

        const renderDateWiseChart = (elementId, shouldHide = false) => {
            let myChart = echarts.init(document.getElementById(elementId));

            const dataset = $(`#${elementId}`).data("dataset")

            const categories = [];
            const data = [];

            Object.keys(dataset ?? []).map((key, index) => {
                categories.push(key)
                data.push(dataset[key])
            })

            let option = {
                xAxis: {
                    type: 'category',
                    data: categories
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: data,
                    type: 'line'
                }]
            };

            myChart.setOption(option);

            if (shouldHide) {
                $(`#${elementId}`).closest(".target-cols").addClass("d-none")
            }
        }

        $(document).ready(function() {
            $(".date-wise-chart").each(function(index) {
                renderDateWiseChart($(this).attr("id"), index !== 0)
            })
        })

        const renderPieChart = (data, elementId) => {
            let myChart = echarts.init(document.getElementById(elementId));

            let option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}: {c} ({d}%)'
                },
                series: [{
                    type: 'pie',
                    radius: '50%',
                    data: data,
                    label: {
                        show: true, // Enable labels
                        formatter: '{b}: {c} ({d}%)',
                        position: 'outside',
                        color: '#333',
                        fontSize: 12
                    },
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

        const getBreakdown = (kpi) => {
            //renderPieChart([])
            $("#status-table>tbody").html("")
            $.ajax({
                url: "{{ route('reports.team.kpi-v2.get.report.breakdown') }}",
                type: 'GET',
                data: {
                    group_id: "{{ $group->id }}",
                    month: convertMonthToDateFormat($("#month").val()),
                    kpi: kpi,
                },
                success: function(response) {
                    if (response.status == 1) {
                        let data = response.data;
                        let selectedKpi = data.kpi_title;
                        $(".selectedKpi").text(selectedKpi);
                        let dayWiseData = data.target?.result?.data ?? [];

                        // const chartData = [];

                        // let tableRows = ""

                        // data?.map(item => {
                        //     chartData.push({
                        //         name: item.title,
                        //         value: item.count
                        //     })

                        //     tableRows += `<tr>
                        //             <th>${item.title}</th>
                        //             <td>${item.count}</td>
                        //         </tr>`

                        // })
                        // renderPieChart(chartData)
                        // $("#status-table>tbody").html(tableRows)
                        dayWiseData = JSON.parse(dayWiseData);
                        let dates = Object.keys(dayWiseData);
                        let values = Object.values(dayWiseData);
                        dayWiseChart(dates, values);
                    }
                }
            })
        }

        const getTeamExtraData = (kpi) => {


            $.ajax({
                url: "{{ route('reports.team.kpi-v2.get.team.kpi.data') }}",
                type: 'GET',
                data: {
                    group_id: "{{ $group->id }}",
                    month: convertMonthToDateFormat($("#month").val()),
                    kpi: kpi,
                },
                success: function(response) {
                    if (response.status == 1) {
                        let data = response.data;
                        let selectedKpi = data.kpi;


                        let dayWiseData = data.data ?? [];

                        // const chartData = [];

                        // let tableRows = ""

                        // data?.map(item => {
                        //     chartData.push({
                        //         name: item.title,
                        //         value: item.count
                        //     })

                        //     tableRows += `<tr>
                        //             <th>${item.title}</th>
                        //             <td>${item.count}</td>
                        //         </tr>`

                        // })
                        // renderPieChart(chartData)
                        // $("#status-table>tbody").html(tableRows)
                        dayWiseData = JSON.parse(dayWiseData);
                        let dates = Object.keys(dayWiseData);
                        let values = Object.values(dayWiseData);
                        dayWiseChart(dates, values, kpi);
                    }
                }
            })
        }

        // getBreakdown("{{ $targets[0]->kpi ?? 'listings' }}")
        let labels = {
            total_deals: 'Total Deals',
            sales_deal_count: 'Sales Deals',
            rental_deals_count: 'Rental Deals',
            gross_revenue: 'Gross Revenue',
            avg_rental_revenue: 'Avg. Rental Revenue',
            avg_sales_revenue: 'Avg. Sales Revenue',
            avg_rental_commission: 'Avg. Rental Commission',
            avg_sales_commission: 'Avg. Sales Commission'
        };

        const dayWiseChart = (dates, values, label) => {
            $("#dayWiseReportCard").removeClass("d-none");
            if (label != '') {
                let labelText = labels[label] || label;

                $(".selectedKpi").text(labelText);

            }
            var options = {
                series: [{
                    name: 'Total',
                    data: values
                }],
                chart: {
                    height: 350,
                    type: 'area'
                },
                colors: ['#d57342'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                    categories: dates,
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                },
            };

            var chart = new ApexCharts(document.querySelector("#dayWiseChart"), options);
            chart.render();


        }
        const appendTrendFlag = (currentValue, previousValue, className) => {
            const container = document.querySelector(`.${className}`);
            if (!container) {
                console.error("Element with the provided class name not found.");
                return;
            }

            const difference = ((currentValue - previousValue) / previousValue) * 100;
            const formattedDifference = difference.toFixed(2); // Keep 2 decimal points
            let trendHTML;

            if (difference > 0) {
                // Up trend
                trendHTML = `
            <span class="badge bg-success-subtle text-success" title="Compared to the previous month">
                <i class="ri-arrow-up-s-fill align-middle me-1"></i>${formattedDifference}%
            </span>
        `;
            } else if (difference < 0) {
                // Down trend
                trendHTML = `
            <span class="badge bg-danger-subtle text-danger" title="Compared to the previous month">
                <i class="ri-arrow-down-s-fill align-middle me-1"></i>${Math.abs(formattedDifference)}%
            </span>
        `;
            } else {
                // No change
                trendHTML = `
            <span class="badge bg-secondary-subtle text-secondary" title="Compared to the previous month">
                <i class="ri-arrow-right-s-fill align-middle me-1"></i>0.00%
            </span>
        `;
            }

            container.innerHTML = trendHTML;
        }
        const compareBarChart = () => {
            var options = {
                series: [

                    {
                        name: 'Avg Rental Commission',
                        data: [300]
                    },
                    {
                        name: 'Avg Sales Commission',
                        data: [150]
                    },
                    {
                        name: 'Average Rental Revenue',
                        data: [2000]
                    },
                    {
                        name: 'Average Sales Revenue',
                        data: [25]
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 275
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: -6,
                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: ['Revenue Vs Commission'],
                },
            };

            var chart = new ApexCharts(document.querySelector("#compareBarChart"), options);
            chart.render();
        }
        compareBarChart();

        function getRandomInt(min, max) {
            min = Math.ceil(min); // Round up to the nearest integer
            max = Math.floor(max); // Round down to the nearest integer
            return Math.floor(Math.random() * (max - min + 1)) + min; // Generate random integer
        }


        const getAgentReports = () => {
            $.ajax({
                url: "{{ route('reports.team.kpi-v2.get.team.agent.report') }}",
                type: 'GET',
                data: {
                    group_id: "{{ $group->id }}",
                    month: convertMonthToDateFormat($("#month").val()),

                },
                success: function(response) {
                    if (response.status == 1) {
                        let kpi_report = response.data.kpi_report;
                        let deal_report = response.data.deal_data;
                        this_month_total_deals = deal_report.total_deals;
                        this_month_sales_deal_count = deal_report.sales_deal_count;
                        this_month_rental_deals_count = this_month_total_deals -
                            this_month_sales_deal_count == 0 ? 0 : this_month_total_deals -
                            this_month_sales_deal_count;
                        this_month_rental_deals_count = this_month_rental_deals_count < 0 ? 0 :
                            this_month_rental_deals_count;
                        this_month_gross_revenue = deal_report.gross_revenue;

                        let avg_rental_rev = (deal_report.avg_rental_revenue / deal_report
                            .avg_sales_revenue) * 100;
                        let avg_sales_rev = 100 - avg_rental_rev;
                        let pieData = [avg_rental_rev, avg_sales_rev];
                        comparePieChart(pieData,['Avg Rental Revenue','Avg Sales Revenue'], 'comparePieChart');
                        $("#totalDealCountCard").html(
                            `<span class="counter-value" data-target="${this_month_total_deals}">${this_month_total_deals}</span>`
                        );
                        $("#totalGrossRevenueCountCard").html(
                            `AED ${formatNumberMetricPrefixes(deal_report.gross_revenue)}`
                        );
                        $("#totalSalesDealCountCard").html(
                            `<span class="counter-value" data-target="${this_month_sales_deal_count}">${this_month_sales_deal_count}</span>`
                        );
                        $("#totalRentalDealCountCard").html(
                            `<span class="counter-value" data-target="${this_month_rental_deals_count}">${this_month_rental_deals_count}</span>`
                        );
                        $(".sales_deal_count").text(deal_report.sales_deal_count);
                        $(".rental_deal_count").text(this_month_rental_deals_count);
                        $(".avg_sales_commission").text(numberFormatAX(deal_report
                            .avg_sales_commission));
                        $(".avg_sales_revenue").text(numberFormatAX(deal_report.avg_sales_revenue));
                        $(".avg_rental_commission").text(numberFormatAX(deal_report
                            .avg_rental_commission));
                        $(".avg_rental_revenue").text(numberFormatAX(deal_report.avg_rental_revenue));

                        comparePieChart([deal_report
                        .avg_sales_commission,deal_report
                        .avg_rental_commission],['Avg Sales Commission','Avg Rental Commission'], 'commissionPieChart');
                        let html = "";
                        Object.entries(kpi_report).forEach(([key, item]) => {
                            html += makeAgentCard(item)
                        })
                        $("#agent-cards").html(html)

                    }
                }
            })
        }
        const subtractMonth = (monthStr) => {
            let [year, month] = monthStr.split('-').map(Number);

            let date = new Date(year, month - 1);

            date.setMonth(date.getMonth() - 1);

            let newYear = date.getFullYear();
            let newMonth = date.getMonth() + 1;

            newMonth = newMonth < 10 ? '0' + newMonth : newMonth;

            return `${newYear}-${newMonth}`;
        }
        const getAgentPreMonthReports = () => {


            $.ajax({
                url: "{{ route('reports.team.kpi-v2.get.team.agent.report') }}",
                type: 'GET',
                data: {
                    group_id: "{{ $group->id }}",
                    month: subtractMonth(convertMonthToDateFormat($("#month").val())),

                },
                success: function(response) {
                    if (response.status == 1) {
                        let kpi_report = response.data.kpi_report;
                        let deal_report = response.data.deal_data;
                        previous_month_total_deals = deal_report.total_deals;
                        previous_month_sales_deal_count = deal_report.sales_deal_count;
                        previous_month_rental_deals_count = this_month_total_deals -
                            this_month_sales_deal_count == 0 ? 0 : this_month_total_deals -
                            this_month_sales_deal_count;
                        previous_month_gross_revenue = deal_report.gross_revenue;

                        appendTrendFlag(this_month_total_deals, previous_month_total_deals,
                            "totalDealCountTrend");
                        appendTrendFlag(this_month_gross_revenue, previous_month_gross_revenue,
                            'totalGrossRevenueTrend');
                        appendTrendFlag(this_month_rental_deals_count,
                            previous_month_rental_deals_count, 'totalRentalDealCountTrend');
                        appendTrendFlag(this_month_sales_deal_count, previous_month_sales_deal_count,
                            'totalSalesDealCountTrend');

                    }
                }
            })
        }
        getAgentReports();
        getAgentPreMonthReports();

        const comparePieChart = (data,labels, elementId) => {

            var options = {
                series: data,
                chart: {
                    width: '287px',
                    height: '287px',
                    type: 'pie',
                },
                labels: labels,
                colors: ['#d57342', '#d3a188'],

                plotOptions: {
                    pie: {
                        dataLabels: {
                            offset: -5,
                        },
                    },
                },
                grid: {
                    padding: {
                        top: 0,
                        bottom: 0,
                        left: 0,
                        right: 0,
                    },
                },
                dataLabels: {
                    formatter(val, opts) {
                        const name = opts.w.globals.labels[opts.seriesIndex]
                        return [name, val.toFixed(1) + '%']
                    },
                },
                legend: {
                    show: false,
                },
            };

            var chart = new ApexCharts(document.querySelector("#" + elementId), options);
            chart.render();

        }

        // renderPieChart(['20','80'],'comparePieChart');
    @endif


    const getBgColor = (percentage) => {
        if (percentage >= 75) {
            return "success"
        } else if (percentage >= 50) {
            return "warning"
        } else {
            return "danger"
        }
    }

    const getIcon = (key) => {
        if (key == "listings") {
            return "ri-home-line"
        }
        if (key == "viewings") {
            return "ri-calendar-check-line"
        }
        if (key == "offers") {
            return "ri-bank-card-line"
        }
        if (key == "listing_leads") {
            return "ri-home-gear-line"
        }
        if (key == "calls") {
            return "ri-phone-line"
        }
        return "ri-earth-line"
    }

    const makeAgentCard = (item) => {
        let targets = "";
        item.reports?.map(report => {
            targets += `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="mdi mdi-handshake-outline ax-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(report.kpi)} ${numberFormat(report.achieved || 0, true)}</h6>
                                </div>
                            </div>
                        </li>`;

        })

        item.other_reports?.map(report => {
            targets += `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="mdi mdi-handshake-outline ax-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(report.kpi)}: ${numberFormat(report.achieved || 0, true)}</h6>
                                </div>
                            </div>
                        </li>`;

        })

        let progress = (item.agent.achieved / item.agent.target) * 100;
        let progressColor = progress >= 75 ? 'success' : (progress >= 50 ? 'warning' : 'danger');
        let progressWidth = progress > 100 ? 100 : progress;

        return `<div class="col-3"><div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered${item.agent.id}"
                            role="button" aria-expanded="false" aria-controls="leadDiscovered2344">
                            <div class="flex-shrink-0">
                                <img src="${item.agent.thumbnail}"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-12 mb-1">${item.agent.full_name}</h6>
                                <p class="text-muted fs-12 mb-0">${item.agent.email}</p>
                                <div class="progress animated-progress custom-progress mb-4">
                                <div class="progress-bar bg-${progressColor}" role="progressbar" style="width: ${progressWidth}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                            </div>
                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse" id="leadDiscovered${item.agent.id}" style="">
                        <div class="card-body">
                            <p class="text-muted text-truncate-two-lines">${item.agent?.bio || ''}</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">${targets}</ul>
                        </div>
                    </div>
                </div></div></div>`;

    }

    const makeAgentKpiReportCard = (item) => {
        let targets = "";
        let totalTarget = 0;
        let totalAchieved = 0;
        item.targets?.map(target => {
            totalTarget += parseFloat(target.target)
            totalAchieved += parseFloat(target.result?.achieved || 0)
            targets += `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="${getIcon(target.kpi)}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(target.kpi)} (${numberFormat(target.result?.achieved || 0, true)}/${numberFormat(target.target, true)})</h6>
                                    <div class="progress animated-progress custom-progress mb-1">
                                        <div class="progress-bar bg-${getBgColor(target.result?.achieved_percentage || 0)}" role="progressbar"
                                            style="width: ${target.result?.achieved_percentage || 0}%" aria-valuenow="${target.result?.achieved_percentage || 0}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </li>`;

        })

        item.other_reports?.map(report => {
            targets += `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="${getIcon(report.kpi)}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(report.kpi)} (${numberFormat(report.count || 0, true)})</h6>
                                </div>
                            </div>
                        </li>`;
        })

        const totalAchievedPercentage = calculatePercentage(totalTarget, totalAchieved);


        return `<div class="col-3"><div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#agent-kpi-report-card${item.agent.id}"
                            role="button" aria-expanded="false" aria-controls="agent-kpi-report-card${item.agent.id}">
                            <div class="flex-shrink-0">
                                <img src="${item.agent.thumbnail}"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-12 mb-1">${item.agent.full_name}</h6>
                                <p class="text-muted fs-12 mb-0">${item.agent.email}</p>
                                <div class="progress animated-progress custom-progress mb-4">
                                    <div class="progress-bar bg-${getBgColor(totalAchievedPercentage)}" role="progressbar" style="width: ${totalAchievedPercentage}%"
                                        aria-valuenow="${totalAchievedPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse" id="agent-kpi-report-card${item.agent.id}" style="">
                        <div class="card-body">
                            <p class="text-muted text-truncate-two-lines">${item.agent?.bio || ''}</p>
                            <ul class="list-unstyled vstack gap-2 mb-0">${targets}</ul>
                        </div>
                    </div>
                </div></div></div>`;
    }

    function toggleAgentReportView(viewType = 'revenue') {
        if (viewType === 'kpi') {
            $("#agent-kpi-cards").removeClass("d-none")
            $("#agent-cards").addClass("d-none")
        } else {
            $("#agent-kpi-cards").addClass("d-none")
            $("#agent-cards").removeClass("d-none")
        }
    }

    const getAgentKpiReport = () => {
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.get-report') }}",
            type: 'GET',
            data: {
                group_id: "{{ $group->id }}",
                month: convertMonthToDateFormat($("#month").val()),

            },
            success: function(response) {
                if (response.status == 1) {
                    let data = response.data;

                    let html = ""
                    data?.map((item => {
                        html += makeAgentKpiReportCard(item);
                    }))

                    $("#agent-kpi-cards").html(html)
                } else {
                    $("#agent-kpi-cards").html("")
                }
            }
        })
    }

    getAgentKpiReport();
    const smallChart = (elementId) => {

        let dates = ['2021-09-01', '2021-09-02', '2021-09-03', '2021-09-04', '2021-09-05', '2021-09-06', '2021-09-07', '2021-09-08', '2021-09-09', '2021-09-10', '2021-09-11', '2021-09-12', '2021-09-13', '2021-09-14', '2021-09-15', '2021-09-16', '2021-09-17', '2021-09-18', '2021-09-19', '2021-09-20', '2021-09-21', '2021-09-22', '2021-09-23', '2021-09-24', '2021-09-25', '2021-09-26', '2021-09-27', '2021-09-28', '2021-09-29', '2021-09-30'];
        let values = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 90, 80, 70, 60, 50, 40, 30, 20, 10, 0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        let label = 'total_deals';

        var options = {
        series: [{
            name: "Results",
            data: [0, 110, 95, 75, 120]
        }],
        chart: {
            width: 140,
            type: "area",
            sparkline: {
                enabled: true
            },
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: "smooth",
            width: 1.5
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [50, 100, 100, 100]
            }
        },
        colors: ["#d57342"],
    };

    var chart = new ApexCharts(document.querySelector("#"+elementId), options);
    chart.render();


        }
        smallChart('results_sparkline_charts3');
        smallChart('salesDealCardChart');
        smallChart('rentalDealCardChart');
    $("#sidebarReports").addClass('collapse show');
    $('#sidebarTeamKpiV2Report').addClass('active');
    $("#reportsActive").addClass('active');
</script>
@endsection
