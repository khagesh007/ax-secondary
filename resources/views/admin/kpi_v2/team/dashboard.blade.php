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
        .ax-dark-text {
            color: #b77e62 !Important;
        }

        .horizontal-scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
        }

        #tileOfCards {
            display: flex;
            flex-wrap: nowrap;
            /* Prevent wrapping */
            gap: 10px;
            /* Optional: Adjust spacing between tiles */
        }

        #tileOfCardsHead {
            display: flex;
            flex-wrap: nowrap;
            /* Prevent wrapping */
            gap: 10px;
            /* Optional: Adjust spacing between tiles */
        }

        .progress-ring-container {
    display: inline-block;
    position: relative;
    width: 50px; /* Increased */
    height: 50px; /* Increased */
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring circle {
    fill: none;
    stroke-width: 4;
    stroke-linecap: round;
}

.progress-ring .background {
    stroke: #ddd;
}

.progress-ring .progress-success {
    stroke: #45cb85;
    transition: stroke-dasharray 1s ease;
}

.progress-ring .progress-warning {
    stroke: #ffbe0b;
    transition: stroke-dasharray 1s ease;
}

.progress-ring .progress-danger {
    stroke: #f06548;
    transition: stroke-dasharray 1s ease;
}

.percentage-text {
    font-size: 9px; /* Increased text size */
    font-weight: 500;
    text-anchor: middle;
    dominant-baseline: middle;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}


        .truBroker-badge {
            border-radius: 0.4rem !important;
            padding: .16em .45em !important;
            margin-right: 2px;
            height: 20px;
        }

        .super-agent-badge {
            background: #3a307f;
            font-weight: 400;
            color: white !important;
            font-size: 11px !important;
            border-radius: 0.4rem !important;
            padding: .16em .45em !important;
            height: 20px;
        }

        .badge-gradient-primary {
            background: linear-gradient(277deg, #032e0a, #45cb85);
        }

        .badge-cart {
            opacity: 0.5;
        }

        .opacity06 {
            opacity: 0.6;
        }

        .btn-outline-info {
            border-color: #d3a188 !important;
            color: #151515 !important;
        }
        .prev-month-badges span{
            opacity: 0.6;
        }
        hr {
            color: #b3b1b1 !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kpi</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex">
                                <input type="text" class="form-control" name="month"
                                    value="{{ request('month') ? date('F Y', strtotime(request('month'))) : date('F Y') }}"
                                    id="month" placeholder="Select Month">
                            </div>
                            <div>
                                @if($isSuperAdmin)
                                <a href="{{ route('reports.team.kpi-v2.team.setup') }}" class="btn btn-primary"
                                    id="save-button">Setup KPI</a>
                                @endif
                                <button type="button" class="btn btn-primary" id="refreshData" title="Refresh Data"><i
                                        class="bx bx-refresh"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-10">
            <ul class="nav nav-tabs mb-3 " role="tablist" style="background:white;padding: 4px;">

                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" onclick="setTeamFilter('sales_target')" href="#product1"
                        role="tab" aria-selected="false" tabindex="-1">
                        Sales Target
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" onclick="setTeamFilter('live_listings')" href="#messages"
                        role="tab" aria-selected="false" tabindex="-1">
                        Live Listings
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" onclick="setTeamFilter('new_listings')" href="#settings"
                        role="tab" aria-selected="false" tabindex="-1">
                        New Listings
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" onclick="setTeamFilter('truBroker')" href="#settings"
                        role="tab" aria-selected="false" tabindex="-1">
                        Active TruBroker
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" onclick="setTeamFilter('superAgent')" href="#settings"
                        role="tab" aria-selected="false" tabindex="-1">
                        Active Super Agent
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-2">
            <a href="javascript:void(0)" onclick="scrollToAgentInsight()" type="button"
               style="width: 100%;"
                class="btn btn-outline-info waves-effect waves-light">
                Agent Insight
                <i class=" ri-arrow-down-line"></i>

            </a>


        </div>
    </div>
    <div id="totalProgressSection">
    <div class="row team-total-progress-row">
        @php
            $targetTotal = 0;
            $achievedTotal = 0;
            $percentages = [];

            foreach ($groups ?? [] as $group) {
                foreach ($reports[$group->id] ?? [] as $report) {
                    $target = floatval($report->target ?? 0);
                    $achieved = floatval($report?->result?->achieved ?? 0);
                    $targetTotal += $target;
                    $achievedTotal += $achieved;
                    $percentage = $target != 0 ? ($achieved / $target) * 100 : 0;
                    $percentages[] = $percentage;
                }
            }

            $averagePercentage = calculateValuesAverages($percentages);
        @endphp


        <div class="col-3 d-none">
            <div class="card">
                <div class="card-body py-3">
                    <div class="text-center">
                        <h5>{{ formatNumberMetricPrefixes($achievedTotal) }}/{{ formatNumberMetricPrefixes($targetTotal) }}
                        </h5>
                        <p class="text-muted fs-12 mb-0">Overall Sales</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-9 d-none">
            <div class="card">
                <div class="card-body">
                    <div class="align-items-center mt-1">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="progress animated-progress custom-progress" style="width: 100%">
                                <div class="progress-bar bg-{{ ($percentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}"
                                     role="progressbar" style="width: {{ $averagePercentage }}%"
                                     aria-valuenow="{{ $averagePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span
                                class="badge bg-{{ ($averagePercentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}-subtle text-{{ ($averagePercentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}"
                                title="Overall achieved">
                                {{ $averagePercentage }}%
                            </span>
                        </div>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Percentage of Overall Sales Achieved</p>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="tab-content">
        <div class="row row-cols-xxl-3 row-cols-lg-3 row-cols-md-2 row-cols-1 mt-2" id="tileOfCardss">
            @foreach ($groups ?? [] as $group)
                @if($isSuperAdmin == false && $group->user->id != auth()->user()->id)
                    @continue
                @endif
                <div class="col">
                    <div class="card mb-1">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <!-- Left Content -->
                            <a class="d-flex align-items-center flex-grow-1" data-bs-toggle="collapse"
                                href="#teamTileCard{{ $group->id }}" role="button" aria-expanded="false"
                                aria-controls="teamTileCard{{ $group->id }}">

                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-light rounded-circle fs-3 text-white"
                                        style="background-color: #e5c7b8 !important;">
                                        {{ $group?->name }}
                                    </span>
                                </div>

                                <div class="ms-3">
                                    <h6 class="fs-12 mb-1">
                                        <span class="text-muted fs-14 team-manager-name{{ $group?->id }}">
                                            {{ $group?->user?->full_name }}
                                        </span>
                                    </h6>
                                </div>
                            </a>

                            <!-- Right Content (Dropdown) -->
                            <div class="dropdown">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="text-muted fs-16"><i
                                            class="mdi mdi-dots-vertical align-middle"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if($isSuperAdmin)
                                    <a class="dropdown-item" href="#" onclick="editTarget('{{ $group->id }}')"
                                        {{ (request('month') ?? date('Y-m')) < date('Y-m') ? 'disabled' : '' }}>
                                        <i class="ri-pencil-fill align-bottom me-1"></i> Edit Targets
                                    </a>
                                    @endif
                                    <a href="{{ route('reports.team.kpi-v2.get.report.page', ['id' => $group->id]) }}?month={{request('month') ?? date('Y-m')}}"
                                        target="_blank" class="dropdown-item">
                                        <i class="ri-eye-line align-bottom me-1"></i> Full Report
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="border-top border-top-dashed collapse show" id="teamTileCard{{ $group->id }}"
                            style="">
                            <div class="card-body">
                                <p class="text-muted text-truncate-two-lines"></p>


                                @foreach ($reports[$group->id] ?? [] as $report)
                                    @if ($report->kpi == 'sales_value')
                                        @php
                                            $sales_value_target = formatNumberMetricPrefixes($report->target);
                                            $sales_value_achieved = formatNumberMetricPrefixes(
                                                $report?->result?->achieved,
                                            );
                                        @endphp
                                    @else
                                        @php
                                            $sales_value_target = 0;
                                            $sales_value_achieved = 0;
                                        @endphp
                                    @endif
                                @endforeach
                                <div class="team-sales_target sales_target-progressbar  team-target-progressbar">
                                    <div class="">
                                        <div class="d-flex">

                                            <div class="flex-grow-1">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1 fs-14 text-muted">Target By HOD</h5>
                                                    </div>

                                                    <div class="flex-shrink-0 ms-2">

                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="border p-2 rounded border-dashed"
                                                            style="background: #fff4ee;">
                                                            <p class="text-muted text-truncate mb-2">Target</p>
                                                            <h5 class="mb-0">{{ $sales_value_target }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="border p-2 rounded border-dashed"
                                                            style="    background: #eafced;">
                                                            <p class="text-muted text-truncate mb-2">Achieved</p>
                                                            <h5 class="mb-0">{{ $sales_value_achieved }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <ul class="list-unstyled vstack gap-2 mb-0">
                                                            @php
                                                                $total_target = 0;
                                                                $total_achieved = 0;
                                                            @endphp
                                                            @foreach ($reports[$group->id] ?? [] as $report)
                                                                <li class="team-sales_value">
                                                                    <div class="d-flex">

                                                                        <div class="flex-grow-1">
                                                                            {{--                                                                        <h6 class="mb-0">{{getKpiFullText($report->kpi)}} --}}
                                                                            {{--                                                                            ({{formatNumberMetricPrefixes($report?->result?->achieved)}}/{{formatNumberMetricPrefixes($report->target)}})</h6> --}}
                                                                            @php

                                                                                $total_target += $report->target ?? 0;
                                                                                $total_achieved +=
                                                                                    $report?->result?->achieved ?? 0;
                                                                                $percentage =
                                                                                    $report->target != 0
                                                                                        ? ($report?->result?->achieved /
                                                                                                $report->target) *
                                                                                            100
                                                                                        : 0;
                                                                            @endphp

                                                                            <div
                                                                                class="progress animated-progress progress-lg mb-1">
                                                                                <div class="progress-bar bg-{{ getProgressbarBgColor($percentage) }}"
                                                                                    role="progressbar"
                                                                                    style="width: {{ $percentage > 100 ? '100' : $percentage }}%"
                                                                                    aria-valuenow="{{ $percentage }}"
                                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                                </div>
                                                                                <span
                                                                                    style="margin-top:0px;">{{ number_format($percentage, 2) }}%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-unstyled vstack gap-2 mb-0 mt-2 team-ul-{{ $group->id }}">

                                    @foreach ($team_data[$group->id] ?? [] as $report)
                                        @if ($report->kpi == 'avg_gross_commission' || $report->kpi == 'gross_commission_amount')
                                            <li class="team-sales_target sales_target-progressbar team-target-progressbar">
                                                <div class="d-flex">


                                                    @php
                                                        if (
                                                            $report->kpi == 'gross_revenue' ||
                                                            $report->kpi == 'avg_sales_commission' ||
                                                            $report->kpi == 'avg_rental_revenue' ||
                                                            $report->kpi == 'avg_sales_revenue' ||
                                                            $report->kpi == 'gross_revenue' ||
                                                            $report->kpi == 'gross_revenue' ||
                                                            $report->kpi == 'rental_revenue' ||
                                                            $report->kpi == 'sales_revenue' ||
                                                            $report->kpi == 'rental_commission' ||
                                                            $report->kpi == 'sales_commission' ||
                                                            $report->kpi == 'avg_gross_commission_amount' ||
                                                            $report->kpi == 'gross_commission_amount'
                                                        ) {
                                                            $achived = formatNumberMetricPrefixes($report?->total);
                                                        } else {
                                                            $achived = $report?->total;
                                                        }
                                                    @endphp
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ getKpiFullText($report->kpi) }}:
                                                            @if ($report->kpi == 'avg_gross_commission' || $report->kpi == 'avg_company_commission')
                                                                {{ number_format($achived, 2) }}%
                                                            @else
                                                                {{ $achived }}
                                                            @endif
                                                        </h6>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="teamTotalDiv" data-id="{{ $group->id }}" data-target="{{ $total_target }}"
                                data-achived="{{ $total_achieved }}">


                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row" id="agentInsight">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex">
                    <div class="flex-grow-1">
                        <h3>Agent Insight</h3>
                    </div>
                    <div class="flex-grow-2">
                        <a href="{{ route('reports.group.kpi-v2.setup') }}" class="btn btn-primary" id="save-button">Setup KPI</a>
                        <button type="button" class="btn btn-primary" id="refreshAgentData"
                            title="Refresh Agent Data"><i class=" ri-refresh-line"></i></button>
                        <button type="button" class="btn btn-primary" id="filter_btn" title="Filters"><i
                                class="bx bx-filter"></i></button>
                    </div>
                </div>
                <div class="card-body d-none" id="filterSection">
                    <div class="col-12">

                        <div class="row">

                            <div class="col-3">
                                <label>Sales values achieved</label>
                                <select class="form-control select2 filter-input" id="sales_values_filter"
                                    data-filter="sales">
                                    <option value="">Don't Filter</option>
                                    <option value="10">Upto 10%</option>
                                    <option value="20">Upto 20%</option>
                                    <option value="30">Upto 30%</option>
                                    <option value="40">Upto 40%</option>
                                    <option value="50">Upto 50%</option>
                                    <option value="60">Upto 60%</option>
                                    <option value="70">Upto 70%</option>
                                    <option value="80">Upto 80%</option>
                                    <option value="90">Upto 90%</option>
                                    <option value="100">Upto 100%</option>
                                    <option value="101">Over 100%</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label>Live listing achieved</label>
                                <select class="form-control select2 filter-input" id="live_listing_achieved_filter"
                                    data-filter="live">
                                    <option value="">Don't Filter</option>
                                    <option value="10">Upto 10%</option>
                                    <option value="20">Upto 20%</option>
                                    <option value="30">Upto 30%</option>
                                    <option value="40">Upto 40%</option>
                                    <option value="50">Upto 50%</option>
                                    <option value="60">Upto 60%</option>
                                    <option value="70">Upto 70%</option>
                                    <option value="80">Upto 80%</option>
                                    <option value="90">Upto 90%</option>
                                    <option value="100">Upto 100%</option>
                                    <option value="101">Over 100%</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label>New Listing achieved</label>
                                <select class="form-control select2 filter-input" id="new_listing_achieved_filter"
                                    data-filter="new">
                                    <option value="">Don't Filter</option>
                                    <option value="10">Upto 10%</option>
                                    <option value="20">Upto 20%</option>
                                    <option value="30">Upto 30%</option>
                                    <option value="40">Upto 40%</option>
                                    <option value="50">Upto 50%</option>
                                    <option value="60">Upto 60%</option>
                                    <option value="70">Upto 70%</option>
                                    <option value="80">Upto 80%</option>
                                    <option value="90">Upto 90%</option>
                                    <option value="100">Upto 100%</option>
                                    <option value="101">Over 100%</option>
                                </select>
                            </div>

                            <div class="col-3 ">
                                {{--                            <label>Agent Badge</label> --}}
                                {{--                            <div class="form-check mb-2"> --}}
                                {{--                                <input class="form-check-input" type="checkbox" id="isTruBrokerFilter"> --}}
                                {{--                                <label class="form-check-label" for="superAgent"> --}}
                                {{--                                   TruBroker --}}
                                {{--                                </label> --}}
                                {{--                            </div> --}}
                                {{--                            <div class="form-check mb-2"> --}}
                                {{--                                <input class="form-check-input" type="checkbox" id="isSuperAgentFilter"> --}}
                                {{--                                <label class="form-check-label" for="isSuperAgentFilter"> --}}
                                {{--                                    Super Agent --}}
                                {{--                                </label> --}}
                                {{--                            </div> --}}

                            </div>
                            <div class="col-3 mt-2">

                                <button type="button" class="btn btn-primary" id="resetFilter">
                                    Reset
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

        <div class="horizontal-scroll-container">
            @if($selectedGroup)
                <div class="row" id="tileOfCardsHead">
                    <div class="col">
                        <div class="card">
                            <a class="card-body bg-danger-subtle" data-bs-toggle="collapse"
                               href="#group-{{ $selectedGroup->id }}-tile-list" role="button" aria-expanded="false"
                               aria-controls="group-{{ $selectedGroup->id }}-tile-list">
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-sm flex-shrink-0 avatar-xs rounded-circle"
                                         style="width: 2rem; height: 2rem; border-width: 2px; border-style: solid; border-color: #e5c7b8;">
                                    <span class="avatar-title bg-light rounded-circle  text-white"
                                          style="background-color: #e5c7b8 !important;">
                                        {{ $selectedGroup?->name }}
                                    </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fs-12 mb-0"><span
                                                class="text-muted">{{ $selectedGroup?->user?->full_name }}</span></h6>
                                        <p class="text-muted fs-12 mb-0 mb-1"></p>
                                        <div class="d-flex justify-content-between" id="group-{{ $selectedGroup->id }}-progress">

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="collapse show group-tile-list group-list" id="group-{{ $selectedGroup->id }}-tile-list"
                             data-groupid="{{ $selectedGroup->id }}" data-managerid="{{ $selectedGroup->user->id }}">

                        </div>
                    </div>
                </div>
            @else
                <div class="row row-cols-xxl-3 row-cols-lg-3 row-cols-md-2 row-cols-1" id="tileOfCardsHead">
                    @foreach ($groups ?? [] as $group)
                        @if($isSuperAdmin == false && $group->user->id != auth()->user()->id)
                            @continue
                        @endif
                        <div class="col">
                            <div class="card">
                                <a class="card-body bg-danger-subtle" data-bs-toggle="collapse"
                                   href="#group-{{ $group->id }}-tile-list" role="button" aria-expanded="false"
                                   aria-controls="group-{{ $group->id }}-tile-list">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar-sm flex-shrink-0 avatar-xs rounded-circle"
                                             style="width: 2rem; height: 2rem; border-width: 2px; border-style: solid; border-color: #e5c7b8;">
                                        <span class="avatar-title bg-light rounded-circle  text-white"
                                              style="background-color: #e5c7b8 !important;">
                                            {{ $group?->name }}
                                        </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-12 mb-0"><span
                                                    class="text-muted">{{ $group?->user?->full_name }}</span></h6>
                                            <p class="text-muted fs-12 mb-0 mb-1"></p>
                                            <div class="d-flex justify-content-between" id="group-{{ $group->id }}-progress">

                                            </div>

                                        </div>

                                    </div>

                                </a>

                            </div>


                            <div class="collapse show group-tile-list group-list" id="group-{{ $group->id }}-tile-list"
                                 data-groupid="{{ $group->id }}" data-managerid="{{ $group->user->id }}">

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @includeIf('admin.kpi_v2.includes.modals')
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
@endsection
@yield('pageDT')
@stack('in_page_scripts')
<script>
    let groups = @json($groups);
    let isSuperAdmin = Boolean("{{ $isSuperAdmin }}");
    $(document).on("change", "#month", function() {
        window.location = "{{ route('reports.team.kpi-v2.team.dashboard') }}?month=" +
            convertMonthToDateFormat($(this).val())
    })
    //let truBrokerBanner = ` <svg width="84" height="22" viewBox="0 0 84 22" fill="none" xmlns="http://www.w3.org/2000/svg" format="plural"><rect width="84" height="22" rx="5" fill="url(#paint0_linear_666_62002)"></rect><path d="M11.066 16V8.374H8.12V7.36H15.08V8.374H12.134V16H11.066ZM15.4377 16V9.52H16.3917V11.086L16.2357 10.882C16.3117 10.682 16.4097 10.498 16.5297 10.33C16.6537 10.162 16.7917 10.024 16.9437 9.916C17.1117 9.78 17.3017 9.676 17.5137 9.604C17.7257 9.532 17.9417 9.49 18.1617 9.478C18.3817 9.462 18.5877 9.476 18.7797 9.52V10.522C18.5557 10.462 18.3097 10.446 18.0417 10.474C17.7737 10.502 17.5257 10.6 17.2977 10.768C17.0897 10.916 16.9277 11.096 16.8117 11.308C16.6997 11.52 16.6217 11.75 16.5777 11.998C16.5337 12.242 16.5117 12.492 16.5117 12.748V16H15.4377ZM22.3742 16.168C21.9582 16.168 21.6002 16.102 21.3002 15.97C21.0042 15.838 20.7582 15.662 20.5622 15.442C20.3662 15.222 20.2122 14.978 20.1002 14.71C19.9882 14.442 19.9082 14.168 19.8602 13.888C19.8162 13.608 19.7942 13.346 19.7942 13.102V9.52H20.8742V12.766C20.8742 13.07 20.9022 13.364 20.9582 13.648C21.0142 13.932 21.1082 14.188 21.2402 14.416C21.3762 14.644 21.5562 14.824 21.7802 14.956C22.0082 15.088 22.2922 15.154 22.6322 15.154C22.9002 15.154 23.1442 15.108 23.3642 15.016C23.5842 14.924 23.7722 14.788 23.9282 14.608C24.0842 14.424 24.2042 14.192 24.2882 13.912C24.3722 13.632 24.4142 13.306 24.4142 12.934L25.1162 13.15C25.1162 13.774 25.0022 14.312 24.7742 14.764C24.5502 15.216 24.2322 15.564 23.8202 15.808C23.4122 16.048 22.9302 16.168 22.3742 16.168ZM24.5342 16V14.266H24.4142V9.52H25.4882V16H24.5342ZM27.1681 16V7.36H30.5941C31.1781 7.36 31.6561 7.478 32.0281 7.714C32.4041 7.946 32.6821 8.244 32.8621 8.608C33.0461 8.972 33.1381 9.348 33.1381 9.736C33.1381 10.212 33.0241 10.616 32.7961 10.948C32.5721 11.28 32.2661 11.504 31.8781 11.62V11.32C32.4221 11.444 32.8341 11.702 33.1141 12.094C33.3981 12.486 33.5401 12.932 33.5401 13.432C33.5401 13.944 33.4421 14.392 33.2461 14.776C33.0501 15.16 32.7581 15.46 32.3701 15.676C31.9861 15.892 31.5101 16 30.9421 16H27.1681ZM28.6321 14.638H30.7621C31.0141 14.638 31.2401 14.588 31.4401 14.488C31.6401 14.384 31.7961 14.24 31.9081 14.056C32.0241 13.868 32.0821 13.646 32.0821 13.39C32.0821 13.158 32.0321 12.95 31.9321 12.766C31.8321 12.582 31.6861 12.436 31.4941 12.328C31.3021 12.216 31.0721 12.16 30.8041 12.16H28.6321V14.638ZM28.6321 10.81H30.5761C30.7841 10.81 30.9701 10.77 31.1341 10.69C31.2981 10.61 31.4281 10.492 31.5241 10.336C31.6201 10.18 31.6681 9.988 31.6681 9.76C31.6681 9.46 31.5721 9.21 31.3801 9.01C31.1881 8.81 30.9201 8.71 30.5761 8.71H28.6321V10.81ZM34.7384 16V9.52H36.0164V11.098L35.8604 10.894C35.9404 10.678 36.0464 10.482 36.1784 10.306C36.3144 10.126 36.4764 9.978 36.6644 9.862C36.8244 9.754 37.0004 9.67 37.1924 9.61C37.3884 9.546 37.5884 9.508 37.7924 9.496C37.9964 9.48 38.1944 9.488 38.3864 9.52V10.87C38.1944 10.814 37.9724 10.796 37.7204 10.816C37.4724 10.836 37.2484 10.906 37.0484 11.026C36.8484 11.134 36.6844 11.272 36.5564 11.44C36.4324 11.608 36.3404 11.8 36.2804 12.016C36.2204 12.228 36.1904 12.458 36.1904 12.706V16H34.7384ZM42.2762 16.18C41.6282 16.18 41.0622 16.034 40.5782 15.742C40.0942 15.45 39.7182 15.048 39.4502 14.536C39.1862 14.02 39.0542 13.428 39.0542 12.76C39.0542 12.08 39.1902 11.484 39.4622 10.972C39.7342 10.46 40.1122 10.06 40.5962 9.772C41.0802 9.484 41.6402 9.34 42.2762 9.34C42.9282 9.34 43.4962 9.486 43.9802 9.778C44.4642 10.07 44.8402 10.474 45.1082 10.99C45.3762 11.502 45.5102 12.092 45.5102 12.76C45.5102 13.432 45.3742 14.026 45.1022 14.542C44.8342 15.054 44.4582 15.456 43.9742 15.748C43.4902 16.036 42.9242 16.18 42.2762 16.18ZM42.2762 14.824C42.8522 14.824 43.2802 14.632 43.5602 14.248C43.8402 13.864 43.9802 13.368 43.9802 12.76C43.9802 12.132 43.8382 11.632 43.5542 11.26C43.2702 10.884 42.8442 10.696 42.2762 10.696C41.8882 10.696 41.5682 10.784 41.3162 10.96C41.0682 11.132 40.8842 11.374 40.7642 11.686C40.6442 11.994 40.5842 12.352 40.5842 12.76C40.5842 13.388 40.7262 13.89 41.0102 14.266C41.2982 14.638 41.7202 14.824 42.2762 14.824ZM46.8202 16L46.8322 7.36H48.2962V12.64L50.6662 9.52H52.4722L49.9582 12.76L52.6882 16H50.7742L48.2962 12.88V16H46.8202ZM56.118 16.18C55.462 16.18 54.886 16.038 54.39 15.754C53.894 15.47 53.506 15.076 53.226 14.572C52.95 14.068 52.812 13.488 52.812 12.832C52.812 12.124 52.948 11.51 53.22 10.99C53.492 10.466 53.87 10.06 54.354 9.772C54.838 9.484 55.398 9.34 56.034 9.34C56.706 9.34 57.276 9.498 57.744 9.814C58.216 10.126 58.566 10.568 58.794 11.14C59.022 11.712 59.108 12.386 59.052 13.162H57.618V12.634C57.614 11.93 57.49 11.416 57.246 11.092C57.002 10.768 56.618 10.606 56.094 10.606C55.502 10.606 55.062 10.79 54.774 11.158C54.486 11.522 54.342 12.056 54.342 12.76C54.342 13.416 54.486 13.924 54.774 14.284C55.062 14.644 55.482 14.824 56.034 14.824C56.39 14.824 56.696 14.746 56.952 14.59C57.212 14.43 57.412 14.2 57.552 13.9L58.98 14.332C58.732 14.916 58.348 15.37 57.828 15.694C57.312 16.018 56.742 16.18 56.118 16.18ZM53.886 13.162V12.07H58.344V13.162H53.886ZM60.3908 16V9.52H61.6688V11.098L61.5128 10.894C61.5928 10.678 61.6988 10.482 61.8308 10.306C61.9668 10.126 62.1288 9.978 62.3168 9.862C62.4768 9.754 62.6528 9.67 62.8448 9.61C63.0408 9.546 63.2408 9.508 63.4448 9.496C63.6488 9.48 63.8468 9.488 64.0388 9.52V10.87C63.8468 10.814 63.6248 10.796 63.3728 10.816C63.1248 10.836 62.9008 10.906 62.7008 11.026C62.5008 11.134 62.3368 11.272 62.2088 11.44C62.0848 11.608 61.9928 11.8 61.9328 12.016C61.8728 12.228 61.8428 12.458 61.8428 12.706V16H60.3908ZM67.4514 16.18C66.6514 16.18 66.0014 16 65.5014 15.64C65.0014 15.28 64.6974 14.774 64.5894 14.122L66.0654 13.894C66.1414 14.214 66.3094 14.466 66.5694 14.65C66.8294 14.834 67.1574 14.926 67.5534 14.926C67.9014 14.926 68.1694 14.858 68.3574 14.722C68.5494 14.582 68.6454 14.392 68.6454 14.152C68.6454 14.004 68.6094 13.886 68.5374 13.798C68.4694 13.706 68.3174 13.618 68.0814 13.534C67.8454 13.45 67.4834 13.344 66.9954 13.216C66.4514 13.072 66.0194 12.918 65.6994 12.754C65.3794 12.586 65.1494 12.388 65.0094 12.16C64.8694 11.932 64.7994 11.656 64.7994 11.332C64.7994 10.928 64.9054 10.576 65.1174 10.276C65.3294 9.976 65.6254 9.746 66.0054 9.586C66.3854 9.422 66.8334 9.34 67.3494 9.34C67.8534 9.34 68.2994 9.418 68.6874 9.574C69.0794 9.73 69.3954 9.952 69.6354 10.24C69.8754 10.528 70.0234 10.866 70.0794 11.254L68.6034 11.518C68.5674 11.242 68.4414 11.024 68.2254 10.864C68.0134 10.704 67.7294 10.614 67.3734 10.594C67.0334 10.574 66.7594 10.626 66.5514 10.75C66.3434 10.87 66.2394 11.04 66.2394 11.26C66.2394 11.384 66.2814 11.49 66.3654 11.578C66.4494 11.666 66.6174 11.754 66.8694 11.842C67.1254 11.93 67.5054 12.038 68.0094 12.166C68.5254 12.298 68.9374 12.45 69.2454 12.622C69.5574 12.79 69.7814 12.992 69.9174 13.228C70.0574 13.464 70.1274 13.75 70.1274 14.086C70.1274 14.738 69.8894 15.25 69.4134 15.622C68.9414 15.994 68.2874 16.18 67.4514 16.18ZM76.0796 11.86V7.36H76.7516L78.3956 10.102L80.0396 7.36H80.7116L80.7176 11.86H79.9376V8.926L78.6476 11.122H78.1436L76.8596 8.926V11.86H76.0796ZM72.8696 11.86V8.116H71.3216V7.36H75.2276V8.116H73.6856V11.86H72.8696Z" fill="white"></path><defs><linearGradient id="paint0_linear_666_62002" x1="79.4063" y1="-5.17647" x2="6.40824" y2="36.4682" gradientUnits="userSpaceOnUse"><stop offset="0.196272" stop-color="#053940"></stop><stop offset="0.489583" stop-color="#00745F"></stop><stop offset="0.720273" stop-color="#033F46"></stop></linearGradient></defs></svg>
    let truBrokerBanner =
        `<span title="TruBroker" class="badge badge-gradient-primary truBroker-badge"><i class="  ri-check-line fs-18 text-white"></i></span>`;
    //let truBrokerBannerPng = `<img src="{{ asset('assets/images/bayut/truBroker.png') }}" style="width:90px;">`;
    let truBrokerBannerPng = `<i class=" ri-checkbox-fill fs-18 text-success"></i>`;

    let isSuperAgentBanner = ` <span class="badge super-agent-badge" title="Super Agent"
                                        style="font-weight: 500;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            class="r-1xlzkty r-1472mwg r-lrsllp">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.9096 6.45575L12.4054 3.26454H12.407C12.2402 2.91182 11.7607 2.91182 11.5955 3.26454L10.0897 6.45575C10.024 6.59516 9.8973 6.69258 9.74977 6.71441L6.38697 7.22669C6.01654 7.28379 5.86901 7.7608 6.13681 8.03457L8.5695 10.5187C8.67534 10.6279 8.72505 10.7857 8.69939 10.9386L8.1253 14.4456C8.06115 14.8319 8.44923 15.1275 8.78118 14.9444L11.7896 13.2883C11.9211 13.2161 12.0782 13.2161 12.2097 13.2883L15.2181 14.9444C15.5501 15.1275 15.9381 14.8319 15.874 14.4456L15.2999 10.9386C15.2742 10.7841 15.3239 10.6279 15.4298 10.5187L17.8625 8.03457C18.1319 7.7608 17.9828 7.28379 17.6123 7.22669L14.2495 6.71441C14.102 6.69258 13.9753 6.59516 13.9096 6.45575ZM15.6043 18.9989V17.9316C15.6043 17.5384 15.2024 17.2206 14.7063 17.2206H13.3601V15.0859H10.6661V17.2206H9.31989C8.82375 17.2206 8.42188 17.5384 8.42188 17.9316V18.9989H15.6043ZM16.0909 19H7.90909C7.40683 19 7 19.2978 7 19.6661V21H17V19.6661C17 19.2978 16.5932 19 16.0909 19Z"
                                                fill="currentColor"></path>
                                        </svg>

                                    </span>`;
    let viewURL = "{{ route('reports.group.kpi-v2.view', 1) }}";

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
    $(document).on("submit", "#edit-target-form", function(e) {
        e.preventDefault();
        let formData = new FormData($(this)[0]);
        formData.append("month", convertMonthToDateFormat($("#month").val()))
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.save-agent-target') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                showToastify(result.msg, "success")
                $('#edit-kpi-modal').modal('hide');
                getReports();
            },
            error: function(jqXhr, json, errorThrown) {
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);

                showToastify('Something went wrong...', "error")
            },
        });

    })
    $(document).on("click", ".edit-item-btn", function() {
        const agentId = $(this).data("id")
        const managerId = $(this).closest(".group-list").data("managerid")

        $("#edit-target-form").trigger("reset")
        $("#edit-kpi-modal-month-title").html($("#month").val())

        $("#edit-target-settings-agent_id").val(agentId)
        $("#edit-target-settings-manager_id").val(managerId)

        $.ajax({
            url: "{{ route('reports.group.kpi-v2.kpi.agent.target') }}",
            type: 'GET',
            data: {
                agent_id: agentId,
                month: convertMonthToDateFormat($("#month").val()),
            },
            success: function(response) {
                if (response.status == 1) {
                    let data = response.data;
                    data.targets.map(target => {
                        $(`#edit-target-settings-${target.kpi}`).val(target.target)
                    })
                    new Cleave('.cleave', {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand'
                    });

                    $("#edit-kpi-modal").modal("show")
                } else {
                    $("#edit-kpi").html("")
                }
            }
        })

    })

    $(".filter-input").change(function() {

        applyFilters();
    });
    $("#resetFilter").click(function() {
        $("#sales_values_filter").val(null).trigger("change");
        $("#new_listing_achieved_filter").val(null).trigger("change");
        $("#live_listing_achieved_filter").val(null).trigger("change");
        $("#agent_badge").val(null).trigger("change");
        $(".agentCard").removeClass('d-none')
    });
    const badgeFilter = (badge) => {
        $(".agentCard").each(function() {
            let card = $(this);
            let showCard = false;
            let showBadge = badge;
            let showTruBroker = badge === 'truBroker' ? true : false;
            let showSuperAgent = badge === 'superAgent' ? true : false;

            if (showBadge !== "") {
                let isThisTruBroker = card.hasClass("isTruBroker");
                let isThisSuperAgent = card.hasClass("isSuperAgent");

                if (isThisTruBroker === true && showTruBroker === true) {
                    showCard = true;
                }
                if (isThisSuperAgent === true && showSuperAgent === true) {
                    showCard = true;
                }

            }
            if (showCard) {
                card.removeClass("d-none");
            } else {
                card.addClass("d-none");
            }
        });
    }

    const applyFilters = () => {
        $(".agentCard").each(function() {
            let card = $(this);
            let showCard = true;
            // let showTruBroker = false;
            // let showSuperAgent = false;

            $(".filter-input").each(function() {
                let filterValue = $(this).val();
                let filterKey = $(this).data("filter"); // Get the corresponding data attribute key

                if (filterValue !== "") {
                    let dataValue = parseFloat(card.attr("data-" +
                    filterKey)); // Get data from the card
                    let filterNumeric = parseFloat(filterValue);
                    // let isThisTruBroker = card.hasClass("isTruBroker");
                    // let isThisSuperAgent = card.hasClass("isSuperAgent");

                    if (filterNumeric === 101) {
                        // Special case for "Over 100%"
                        if (dataValue <= 100) {
                            showCard = false;
                        }
                    } else {
                        if (dataValue > filterNumeric) {
                            showCard = false;
                        }
                    }
                }
            });

            if (showCard) {
                card.removeClass("d-none");
            } else {
                card.addClass("d-none");
            }
        });
    }

    setTimeout(() => {
        $(".teamTotalDiv").each(function() {
            let total = $(this).data("target");
            let achieved = $(this).data("achived");
            let groupId = $(this).data("id");
            drawTeamAvg(total, achieved, `#group-${groupId}-progress`)
        })
    }, 1000);

    const setTeamFilter = (filter_target) => {
        $(".agent-target-progressbar").addClass('d-none')
        $(".team-target-progressbar").addClass('d-none')

        if (filter_target == 'truBroker' || filter_target == 'superAgent') {
            let truBroker = filter_target === 'truBroker' ? true : false;
            let super_agent = filter_target === 'super_agent' ? true : false;
            $("." + filter_target + "-progressbar").removeClass("d-none");
            badgeFilter(filter_target)
        } else {
            $("." + filter_target + "-progressbar").removeClass("d-none");
        }
    }
    $(document).on("click", ".teamTotalDiv", function() {
        let total = $(this).data("target");
        let achieved = $(this).data("achived");
        let groupId = $(this).data("id");
        drawTeamAvg(total, achieved, `#group-${groupId}-progress`)
    })

    const getBgColor = (percentage) => {
        if (percentage >= 75) {
            return "success"
        } else if (percentage >= 50) {
            return "warning"
        } else {
            return "danger"
        }
    }
    const scrollToAgentInsight = () => {


        const targetElement = $("#agentInsight");

        if (targetElement.length) { // Check if the element exists
            $('html, body').animate({
                scrollTop: targetElement.offset().top // ✅ Correct usage
            }, 700);
        } else {
            console.error("Element #agentInsight not found!");
        }
    };


    function drawTeamAvg(total, achieved, elementId) {
        if (total === 0) {
            return "0";
        }
        let avg = (achieved / total) * 100;
        let max = 100;
        avg = avg > 100 ? '100' : avg;

        $(elementId).html(
            `<div class="progress-bar bg-${getBgColor(avg)}" role="progressbar" style="width: ${avg}%" aria-valuenow="${avg}" aria-valuemin="0" aria-valuemax="100"></div>`
            )
    }

    const editTarget = (groupId) => {
        $("#edit-group-kpi-modal-month-title").html($("#month").val())
        $("#edit-group-target-form").trigger("reset")
        $.ajax({
            url: "{{ route('reports.team.kpi-v2.get.group.targets') }}",
            type: 'GET',
            data: {
                group_id: groupId,
                month: convertMonthToDateFormat($("#month").val()),
            },
            success: function(response) {
                if (response.status == 1) {
                    let data = response.data;

                    $("#edit-group-target-settings-group_id").val(data.group.id)
                    let targetx = 0;
                    data.targets.map(target => {
                        if (target.target != 0) {
                            targetx = target.target;
                        } else {
                            targetx = 0;
                        }
                        console.log(targetx)
                        $(`#edit-group-target-settings-${target.kpi}`).val(targetx)
                    })
                    new Cleave('#edit-group-target-settings-sales_value', {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand'
                    });

                    $("#edit-group-kpi-modal").modal("show")
                }
            }
        })
    }



    $(document).on("submit", "#edit-group-target-form", function(e) {
        e.preventDefault();
        let formData = new FormData($(this)[0]);
        formData.append("month", convertMonthToDateFormat($("#month").val()))
        $.ajax({
            url: "{{ route('reports.team.kpi-v2.save-group-target') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                showToastify(result.msg, "success")
                $('#edit-kpi-modal').modal('hide');
                window.location.reload()
            },
            error: function(jqXhr, json, errorThrown) {
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);

                showToastify('Something went wrong...', "error")
            },
        });

    })
    $("#filter_btn").click(function() {
        $("#filterSection").toggleClass("d-none");
    });
    const getIcon = (key) => {
        if (key == "live_listings" || key == "new_listings") {
            return "ri-home-line"
        }
        if (key == "sales_target") {
            return "ri-bank-card-line"
        }
        if (key == "offers") {
            return "ri-bank-card-line"
        }
        if (key == "calls") {
            return "ri-phone-line"
        }
        if (key == "leads") {
            return "ri-account-box-line"
        }
        if (key == "deals") {
            return "ri-bank-card-2-line"
        }
        if (key == "viewings") {
            return "ri-eye-line"
        }
        if (key == "meetings") {
            return "ri-group-line"
        }
        if (key == "follow-up") {
            return "ri-calendar-line"
        }

        return "ri-earth-line"
    }
    const makeReportTileCard = (item, groupId) => {
        let selectedMonth = $("#month").val();
    selectedMonth = convertMonthToDateFormat(selectedMonth);
        let targets = "";
        let totalTarget = 0;
        let totalAchieved = 0;
        let achievedPercentages = [];
        let sales_target_achieved = 0;
        let live_listing_achieved = 0;
        let new_listing_achieved = 0;
        let targets_achieved_values = [];
        let progressbarList = ''
        let extra_achievements = '';
        item.targets?.map(target => {

            const targetCount = parseFloat(target.target.replace(/[^\d]/g, ''));
            const achievedCount = removeDecimal(target.result?.achieved || "0");
            totalTarget += targetCount
            totalAchieved += achievedCount
            let achievedPercentage = calculatePercentage(targetCount, achievedCount);

            achievedPercentages.push(parseFloat(achievedPercentage || 0))

            progressbarList += `<div class="d-flex justify-content-between gap-2 ${target.kpi === 'sales_target' ? '' : 'd-none'} target-progressbar${groupId} agent-target-progressbar ${target.kpi}-progressbar  ${target.kpi}${groupId}">
<div class="progress  animated-progress progress-lg " style="width: 90%">
                                        <div class="progress-bar bg-${getBgColor(achievedPercentage)}" role="progressbar" style="width: ${achievedPercentage}%"
                                            aria-valuenow="${achievedPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="badge bg-${getBgColor(achievedPercentage)}-subtle text-${getBgColor(achievedPercentage)}" title="Average Percentage of achieved">
                                        ${achievedPercentage || 0}%
                                    </span></div>`;


            targets += `<div class="col">
                            <div class="py-2 px-1">
                                <h5 class="text-muted text-uppercase text-center fs-10">${ucwords(target.kpi)}</h5>
                                <div class="text-center">
                                    <div class="progress-ring-container">
                                        <svg width="50" height="50" class="progress-ring">
                                            <circle class="background" cx="25" cy="25" r="20"></circle>
                                            <circle class="progress-${getBgColor(achievedPercentage || 0)}" cx="25" cy="25" r="20" stroke-dasharray="125.66" stroke-dashoffset="${125.66 * (1 - (Math.min(100, (achievedPercentage || 0)) / 100))}"></circle>
                                        </svg>
                                        <text x="50%" y="50%" class="percentage-text">${achievedPercentage || 0}%</text>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <h2 class="mb-0 fs-10"><span>${formatNumberMetricPrefixes(achievedCount)}/${formatNumberMetricPrefixes(targetCount)}</span></h2>
                                </div>
                            </div>
                        </div>`

            if (target.kpi == "sales_target") {
                sales_target_achieved = removeDecimal(achievedPercentage || "0");
                extra_achievements = target.result?.data;

            }
            if (target.kpi == "live_listings") {
                live_listing_achieved = removeDecimal(achievedPercentage || "0");
            }
            if (target.kpi == "new_listings") {
                new_listing_achieved = removeDecimal(achievedPercentage || "0");
            }
            targets_achieved_values[target.kpi] ??= {};
            targets_achieved_values[target.kpi].target = targetCount;
            targets_achieved_values[target.kpi].achieved_percentage = achievedPercentage;
            targets_achieved_values[target.kpi].achieved_value = achievedCount;

        })

        let otherReports = "";
        item.other_reports?.map(report => {
            let report_countx = removeDecimal(report.count);
            if (report.kpi !== "follow-up" && report.kpi !== "meetings") {
                otherReports += `<div class="col-6">
                                <div class="d-flex justify-content-start gap-2">
                                    <div class="d-flex justify-content-center gap-1">
                                        <i class="${getIcon(report.kpi)} text-muted"></i>
                                        <div>${ucwords(report.kpi)}:</div>
                                    </div>
                                    <div>${formatNumberMetricPrefixes(report_countx)}</div>
                                </div>
                        </div>`;
            }
        })



        const totalAchievedPercentage = calculatePercentage(totalTarget, totalAchieved);

        const disableEdit = convertMonthToDateFormat($("#month").val()) < "{{ date('Y-m') }}";

        const calculateAverage = () => {
            const total = achievedPercentages.reduce((sum, percentage) => sum + percentage, 0);
            if (total == 0) {
                return 0
            }
            return (total / achievedPercentages.length).toFixed(2);
        }

        const agentAveragePercentage = calculateAverage();
        //map through extra_achievements
        let otherAchievement = '';

        if (extra_achievements) {
            let total_gross_commission = 0;
            //find title by value "Total Gross Revenue"
            extra_achievements.map((achievement) => {
                if(achievement.title === 'Total Gross Commission') {
                    total_gross_commission = formatNumberMetricPrefixes(achievement.count);
                }
            });
            extra_achievements.map((achievement) => {

                if(achievement.title == 'Average Gross Commission Percentage') {
                    otherAchievement += `<div class="row g-0"><hr>

                    <div class="col-12 mb-2" style="
                margin-left: 8px;
                ">
                <div class="d-flex justify-content-start gap-2">
                    <div class="d-flex justify-content-center gap-1">
                    <i class="ri-bank-card-2-line text-muted"></i>
                <div>Avg Commission:</div>
            </div>
                <div>${achievement.count}% (${total_gross_commission})</div>
            </div>
            </div></div>`;
                }

            });
        }else{
            otherAchievement = `<div class="row g-0">

                    <div class="col-12 mb-2" style="
                margin-left: 8px;
                ">
                <div class="d-flex justify-content-start gap-2">
                    <div class="d-flex justify-content-center gap-1">
                    <i class="ri-bank-card-2-line text-muted"></i>
                <div>Avg Commission:</div>
            </div>
                <div>0%</div>
            </div>
            </div></div>`;
        }

        let html = `<div class="${isSuperAdmin ? '' : 'col-4'}">
                        <div data-overall="${agentAveragePercentage}"
                        data-sales="${sales_target_achieved}"
                        data-live="${live_listing_achieved}"
                        data-new="${new_listing_achieved}"
                        data-name="${item.agent.full_name}"
                        class="card mb-1 agentCard ${item.is_truBroker > 0 ? 'isTruBroker' : ''} ${item.is_super_agent > 0 ? 'isSuperAgent' : ''} groupAgentCard${groupId}">
                                            <div class="card-body d-flex">
                                                <a class="d-flex align-items-center flex-grow-1 collapsed" data-bs-toggle="collapse" href="#leadDiscovered${item.agent.id}"
                                                    role="button" aria-expanded="false" aria-controls="leadDiscovered${item.agent.id}">
                                                    <div class="flex-shrink-0">
                                                        <img src="${item.agent.thumbnail ?? item.agent.thumb_url}"
                                                            alt="" class="avatar-xs rounded-circle">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-12 mb-1">${item.agent.full_name}</h6>
                                                        <div class="d-flex justify-content-between">
                                                            <p class="text-muted fs-12 mb-0"></p>
                                                        </div>
                        ${progressbarList}
                                                        <div class="d-flex d-none justify-content-between gap-2 agent-target-progressbar target-progressbar${groupId}">

                                                            <div class="progress progress-lg animated-progress" style="width: 90%">
                                                                <div class="progress-bar bg-${getBgColor(agentAveragePercentage)}" role="progressbar" style="width: ${agentAveragePercentage}%"
                                                                    aria-valuenow="${agentAveragePercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <span class="badge bg-${getBgColor(agentAveragePercentage)}-subtle text-${getBgColor(agentAveragePercentage)}" title="Average Percentage of achieved">
                                                                ${agentAveragePercentage || 0}%
                                                            </span>
                                                        </div>
                        <div  class="d-flex align-items-center badge-cart mt-1"> ${item.is_truBroker > 0 ? truBrokerBanner : ''} ${item.is_super_agent > 0 ? isSuperAgentBanner : ''}


                        </div>
                                                    </div>

                                                </a>
                                                <div class="dropdown"><a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered190" role="button" aria-expanded="false" aria-controls="leadDiscovered190">
                                                    </a><a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="text-muted fs-16"><i class="mdi mdi-dots-vertical align-middle"></i></span>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(0px, 24px, 0px);">
                                                        <a class="dropdown-item ${disableEdit ? '' : 'edit-item-btn'}"

                                            ${disableEdit ? 'disabled' : 'edit-item-btn'} href="javascript:void(0)"  data-id="${item.agent.id}">
                                                            <i class="ri-pencil-fill align-bottom me-1"></i> Edit Targets
                                                        </a>
                                                        <a href="${viewURL.slice(0, -1)}${item.agent.id}?month=${selectedMonth}" target="_blank" class="dropdown-item">
                                                            <i class="ri-eye-line align-bottom me-1"></i> Full Report
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border-top border-top-dashed collapse" id="leadDiscovered${item.agent.id}" style="">
                                                <div class="card-body" style="background: #dcdcdc;">
                                                    <div class="card crm-widget">
                                                        <div class="card-body p-0">
                                                            <div class="row row-cols-xxl-3 row-cols-md-3 row-cols-1 g-0">
                                                                ${targets}
                                                            </div>
                                                            <div class="mt-2"></div>
                                                                ${otherAchievement}
                                            <div class="row mb-2 ${(item.prev_truBroker === 1 || item.prev_super_agent === 1 ) ? '' : 'd-none'}" >
                        <div class="col-12">
                        <span id="old" class="d-flex align-items-center prev-month-badges" style="margin-left: 8px;">
                            <i class="ri-award-fill"></i> &nbsp; Previous month: &nbsp;
                           ${item.prev_truBroker ? truBrokerBanner : ''} ${item.prev_super_agent ? isSuperAgentBanner : ''}

                        </span>
                        </div>
                        </div>
                                                        </div>
                                                      </div>
                                                    <div class="card ${otherReports !=='' ? '' : 'd-none'}"><div class="card-body">
                        <div class="row mb-0 mt-2">${otherReports}</div></div></div>
                                                </div>

                                            </div>
                                        </div></div>`;

        return {
            total: totalTarget,
            achieved: totalAchieved,
            targets_achievements: targets_achieved_values,
            html
        }
    }


    const makeReportTableCard = (item, groupId) => {
        let targets = "";
        let totalTarget = 0;
        let totalAchieved = 0;
        let sales_target_achieved = 0;
        let live_listing_achieved = 0;
        let new_listing_achieved = 0;
        item.targets?.map((target, index) => {
            const targetCount = removeDecimal(target.target);
            const achievedCount = removeDecimal(target.result?.achieved);
            totalTarget += isNaN(targetCount) ? 0 : parseFloat(targetCount)
            totalAchieved += achievedCount
            let achievedPercentage = isNaN(target.result?.achieved_percentage) ? 0 : parseFloat(target
                .result?.achieved_percentage);
            targets += `<div class="col-4 text-center">
                            <div>
                                ${formatNumberMetricPrefixes(achievedCount)}/${formatNumberMetricPrefixes(targetCount)}
                                <span class="float-end badge bg-${getBgColor(target.result?.achieved_percentage || 0)}-subtle text-${getBgColor(target.result?.achieved_percentage || 0)}" title="Percentage of achieved">
                                    ${achievedPercentage || 0}%
                                </span>
                            </div>
                            <div class="progress animated-progress custom-progress customized-progress">
                                <div class="progress-bar bg-${getBgColor(target.result?.achieved_percentage || 0)}" role="progressbar" style="width: ${target.result?.achieved_percentage || 0}%"
                                        aria-valuenow="${target.result?.achieved_percentage || 0}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="d-flex justify-content-center gap-1">
                                <i class="${getIcon(target.kpi)} text-muted"></i>
                                <div>${ucwords(target.kpi)}</div>
                            </div>
                        </div>`;

            if (target.kpi == "sales_target") {
                sales_target_achieved = removeDecimal(achievedPercentage || "0");
            }
            if (target.kpi == "live_listings") {
                live_listing_achieved = removeDecimal(achievedPercentage || "0");
            }
            if (target.kpi == "new_listings") {
                new_listing_achieved = removeDecimal(achievedPercentage || "0");
            }

        })

        let otherReports = "";
        item.other_reports?.map(report => {
            let report_counts = removeDecimal(report.count)
            otherReports += `<div class="col-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <div class="d-flex justify-content-center gap-1">
                                        <i class="${getIcon(report.kpi)} text-muted"></i>
                                        <div>${ucwords(report.kpi)}:</div>
                                    </div>
                                    <div>${formatNumberMetricPrefixes(parseFloat(report_counts))}</div>
                                </div>
                         </div>`;
        })

        const totalAchievedPercentage = calculatePercentage(totalTarget, totalAchieved);
        totalAchievedPercentage.value = isNaN(totalAchievedPercentage) ? 0 : parseFloat(totalAchievedPercentage);
        const disableEdit = convertMonthToDateFormat($("#month").val()) < "{{ date('Y-m') }}"


        let html = `<div class="card agentCard"
data-sales="${sales_target_achieved}"
data-live="${live_listing_achieved}"
data-new="${new_listing_achieved}"
>
                        <div class="card-body">
                            <div>
                                <div class="dropdown d-inline-block float-end">
                                    <button class="btn btn-soft-dark btn-rounded btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-2-fill align-middle"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="">
                                        <li>
                                            <a href="${viewURL.slice(0, -1)}${item.agent.id}" target="_blank" class="dropdown-item"><i class="ri-eye-line align-bottom me-2 text-muted"></i> View Report</a>
                                        </li>
                                        <li>
                                            <a data-id="${item.agent.id}" class="dropdown-item ${disableEdit ? '' : 'edit-item-btn'} edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit Target</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="row">
                                    <div class="col-1">
                                        <img src="${item.agent.thumbnail}" class="flex-shrink-0 me-3 avatar-md rounded" alt="...">
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mt-0 mb-0">${item.agent.full_name}</h5>


                                        <div class="d-flex justify-content-between gap-2">
                                            <div class="progress animated-progress custom-progress" style="width: 90%">
                                                <div class="progress-bar bg-${getBgColor(totalAchievedPercentage)}" role="progressbar" style="width: ${totalAchievedPercentage}%"
                                                    aria-valuenow="${totalAchievedPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="badge bg-${getBgColor(totalAchievedPercentage)}-subtle text-${getBgColor(totalAchievedPercentage)}" title="Percentage of achieved">
                                                ${totalAchievedPercentage || 0}%
                                            </span>
                                        </div>
  ${item.is_truBroker > 0 ? truBrokerBannerPng : ''} ${item.is_super_agent > 0 ? isSuperAgentBanner : ''}
                                    </div>
                                    <div class="col-6">
                                        <div class="row mb-2">${targets}</div>
                                        <div class="row ">${otherReports}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

        return {
            total: totalTarget,
            achieved: totalAchieved,
            html
        }
    }

    const createTargetTotals = (data) => {
        const keys = Object.keys(data[0]);

        const totalData = keys.reduce((acc, key) => {
            acc[key] = {
                target: data.reduce((sum, item) => sum + (item[key]?.target || 0), 0),
                achieved_percentage: data.reduce((sum, item) => sum + parseFloat(item[key]
                    ?.achieved_percentage || 0), 0).toFixed(2),
                achieved_value: data.reduce((sum, item) => sum + parseFloat(item[key]?.achieved_value ||
                    0), 0).toFixed(2)
            };
            return acc;
        }, {});
        return totalData;
    }
    const teamProgressCard = (data) =>{

        return `<li class="team-${data.key} ${data.key}-progressbar ${data.key == 'sales_target' ? '' : 'd-none'} team-target-progressbar"><div class="">
                              <div class="">
                                    <div class="d-flex">

                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fs-14 text-muted">${data.title} - For ${data.manager_name}</h6>
                                                </div>

                                                <div class="flex-shrink-0 ms-2">

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="border p-2 rounded border-dashed" style="background: #fff4ee;">
                                                        <p class="text-muted text-truncate mb-2">Total</p>
                                                        <h5 class="mb-0">${data.total}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="border p-2 rounded border-dashed" style="    background: #eafced;">
                                                        <p class="text-muted text-truncate mb-2">Achieved
<span
title="Previous month achievement"
id="prev_${data.key}${data.group_id}"
style="
    font-size: 12px;
    float: inline-end;
    color: #929292;
">0</span></p>
                                                        <h5 class="mb-0">${data.achieved}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                                        <li class="team-sales_value">
                                                            <div class="d-flex">

                                                                <div class="flex-grow-1">


                                                                    <div class="progress animated-progress progress-lg mb-1">
                                                                        <div class="progress-bar bg-${getBgColor(data.percentage)}" role="progressbar" style="width: ${data.percentage}%" aria-valuenow="${data.percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        <span style="margin-top:0px;">${data.percentage || 0}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></li>`;
    }
    const getReport = (groupId) => {
        if (groupId) {
            $.ajax({
                url: "{{ route('reports.group.kpi-v2.get-report') }}",
                type: 'GET',
                data: {
                    group_id: groupId,
                    month: convertMonthToDateFormat($("#month").val())
                },
                success: function(response) {
                    if (response.status == 1) {
                        let data = response.data;

                        let tileHtml = "";
                        let tableHtml = "";

                        let targetTotal = 0;
                        let targetAchieved = 0;
                        let targets_achievements = [];
                        let total_truBroker = 0;
                        let total_superAgent = 0;
                        let total_agents = 0;
                        data?.map((item => {
                            const tileCard = makeReportTileCard(item, groupId);
                            const tableCard = makeReportTableCard(item, groupId);
                            tileHtml += tileCard.html;
                            tableHtml += tableCard.html;
                            targetTotal += tileCard.total;
                            targetAchieved += tileCard.achieved;
                            targets_achievements.push(tileCard.targets_achievements)

                            total_truBroker += item.is_truBroker > 0 ? 1 : 0;
                            total_superAgent += item.is_super_agent > 0 ? 1 : 0;
                            total_agents += 1;

                        }))
                        let truBroker_report_card_data = {
                            'key': 'truBroker',
                            'group_id': groupId,
                            'title': 'TruBroker',
                            'total': total_agents,
                            'achieved': total_truBroker,
                            'percentage': calculatePercentage(total_agents, total_truBroker),
                            'manager_name':   $(".team-manager-name" + groupId).text() ?? 'Manager',
                        }
                        let super_agent_report_card_data = {
                            'key': 'superAgent',
                            'group_id': groupId,
                            'title': 'Super Agent',
                            'total': total_agents,
                            'achieved': total_superAgent,
                            'percentage': calculatePercentage(total_agents, total_superAgent),
                            'manager_name':   $(".team-manager-name" + groupId).text() ?? 'Manager',
                        }
                        let truBrokerReportCard = teamProgressCard(truBroker_report_card_data)
                        let superAgentReportCard = teamProgressCard(super_agent_report_card_data)
                        let teamTotalTargetAchievements = createTargetTotals(targets_achievements)


                        teamTotalTargetAchievements && Object.keys(teamTotalTargetAchievements).map((
                            key) => {

                            const targetTotal = teamTotalTargetAchievements[key].target;
                            const targetAchieved = teamTotalTargetAchievements[key]
                                .achieved_value;
                            const achievedPercentage = calculatePercentage(targetTotal,
                                targetAchieved);



                            let target_total_label = 0
                            let achieved_total_label = 0
                            if (key === 'sales_target') {
                                target_total_label = formatNumberMetricPrefixes(targetTotal)
                                achieved_total_label = formatNumberMetricPrefixes(targetAchieved)
                            } else {
                                target_total_label = targetTotal
                                achieved_total_label = parseInt(targetAchieved)
                            }
                            //replace _ with space and make first letter capital key
                            let target_title_text = ucwords(key.replace(/_/g, ' '));
                            let manager_name = $(".team-manager-name" + groupId).text()
                            let target_result_card = `<li class="team-${key} ${key}-progressbar d-none team-target-progressbar"><div class="">
                              <div class="">
                                    <div class="d-flex">

                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fs-14 text-muted">${target_title_text} - Target By ${manager_name}</h6>
                                                </div>

                                                <div class="flex-shrink-0 ms-2">

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="border p-2 rounded border-dashed" style="background: #fff4ee;">
                                                        <p class="text-muted text-truncate mb-2">Target</p>
                                                        <h5 class="mb-0">${target_total_label}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="border p-2 rounded border-dashed" style="    background: #eafced;">
                                                        <p class="text-muted text-truncate mb-2">Achieved</p>
                                                        <h5 class="mb-0">${achieved_total_label}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                                        <li class="team-sales_value">
                                                            <div class="d-flex">

                                                                <div class="flex-grow-1">


                                                                    <div class="progress animated-progress progress-lg mb-1">
                                                                        <div class="progress-bar bg-${getBgColor(achievedPercentage)}" role="progressbar" style="width: ${achievedPercentage}%" aria-valuenow="${achievedPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        <span style="margin-top:0px;">${achievedPercentage || 0}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></li>`;

                            //check if .team-ul-${groupId} has any child html node with .team-truBroker class


                            // $(`.team-ul-${groupId}`).prepend(truBrokerReportCard)
                            $(`.team-ul-${groupId}`).prepend(target_result_card)
                            $(`#group-${groupId}-progress`).prepend(`<li class="team-${key} ${key}-progressbar team-target-progressbar">
                                        <div class="d-flex">

                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">${target_title_text}
                                                    (${achieved_total_label}/${target_total_label})</h6>

                                                <div class="progress animated-progress custom-progress mb-1">
                                                    <div class="progress-bar bg-${getBgColor(achievedPercentage)}" role="progressbar" style="width: ${achievedPercentage}%" aria-valuenow="${getBgColor(achievedPercentage)}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <span style="margin-top:-4px;"> ${achievedPercentage || 0}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>`);
                        })



                        if ($(`.team-ul-${groupId}`).find(`.team-truBroker`).length == 0) {
                                $(`.team-ul-${groupId}`).prepend(truBrokerReportCard)
                            }else{

                            }
                            if ($(`.team-ul-${groupId}`).find(`.team-superAgent`).length == 0) {
                                $(`.team-ul-${groupId}`).prepend(superAgentReportCard)
                            }else{

                            }

                            if (!isSuperAdmin){
                                $(`#group-${groupId}-tile-list`).html(`<div class="row">${tileHtml}</div>`);
                            }else{
                                $(`#group-${groupId}-tile-list`).html(tileHtml);
                            }

                        $(`#group-${groupId}-card-list`).html(tableHtml);

                        const totalAchievedPercentage = calculatePercentage(targetTotal, targetAchieved)
                        $(`#group-${groupId}-progress`).html(`<div class="progress animated-progress progress-lg mb-2" style="width: 230px" ><div class="progress-bar bg-${getBgColor(totalAchievedPercentage)}" role="progressbar" style="width: ${totalAchievedPercentage}%" aria-valuenow="${totalAchievedPercentage}"
                            aria-valuemin="0" aria-valuemax="100"></div></div> <span class="badge bg-${getBgColor(totalAchievedPercentage)}-subtle text-${getBgColor(totalAchievedPercentage)}" title="Total Percentage of achieved">
                                        ${totalAchievedPercentage || 0}%
                                    </span>`)
                        // $(`.team-ul-${groupId}`).prepend(`<div class="progress animated-progress custom-progress mb-2" style="width: 230px" ><div class="progress-bar bg-${getBgColor(totalAchievedPercentage)}" role="progressbar" style="width: ${totalAchievedPercentage}%" aria-valuenow="${totalAchievedPercentage}"
                        // aria-valuemin="0" aria-valuemax="100"></div></div> <span class="badge bg-${getBgColor(totalAchievedPercentage)}-subtle text-${getBgColor(totalAchievedPercentage)}" title="Total Percentage of achieved">
                        //             ${totalAchievedPercentage || 0}%
                        //         </span>`);
                    } else {

                    }
                }
            })
        }
    }
    // getReport(10)
    const getReports = () => {
        if (isSuperAdmin) {

            groups.map(group => {
                getReport(group.id)
            })
        } else {
            getReport("{{ auth()->user()->group_id }}")
        }
    }

    getReports();
    const subtractMonth = (monthStr) => {
        let [year, month] = monthStr.split('-').map(Number);

        let date = new Date(year, month - 1);

        date.setMonth(date.getMonth() - 1);

        let newYear = date.getFullYear();
        let newMonth = date.getMonth() + 1;

        newMonth = newMonth < 10 ? '0' + newMonth : newMonth;

        return `${newYear}-${newMonth}`;
    }
    const getPreviousMonthTeamReport = () => {
            $.ajax({
                url: "{{ route('reports.team.kpi-v2.get.teams.overview') }}",
                type: 'GET',
                data: {
                    month: subtractMonth(convertMonthToDateFormat($("#month").val()))
                },
                success: function (response) {
                    if(response.status == '1'){
                        let data = Object.values(response.data)

                        //loop through data
                        data.map((item) => {
                            let groupId = item.group.id;
                            $("#prev_truBroker"+groupId).text(item.tru_brokers)
                            $("#prev_superAgent"+groupId).text(item.super_agent)


                        })
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);
                },
            })

    }
    getPreviousMonthTeamReport();

    const getTeamTotals = () => {
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.team.get.totals') }}",
            type: 'GET',
            data: {
                month: convertMonthToDateFormat($("#month").val())
            },
            success: function (response) {
                if(response.status == '1'){
                    let data = Object.values(response.data)

                 let html = '';
                    data.map((item) => {

                let target_value = 0;
                let achieved_value = 0;
                        // $("#prev_truBroker"+groupId).text(item.tru_brokers)
                        // $("#prev_superAgent"+groupId).text(item.super_agent)
                if(item.kpi === 'sales_value'){
                    achieved_value  = formatNumberMetricPrefixes(item.achieved);
                    target_value = formatNumberMetricPrefixes(item.target);
                    item.kpi = 'sales_target';
                }else{
                    achieved_value = item.achieved;
                    target_value = item.target;
                }

                        html+=`<div class="row  ${item.kpi}-progressbar team-target-progressbar ${item.kpi === 'sales_target' ? '' : 'd-none'} ">


        <div class="col-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="text-center">
                        <h5>${achieved_value}/${target_value}
                        </h5>
                        <p class="text-muted fs-12 mb-0">${item.kpi_title}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body">
                    <div class="align-items-center mt-1">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="progress animated-progress custom-progress" style="width: 100%">
                                <div class="progress-bar bg-${getBgColor(item.percentage)}" role="progressbar" style="width:  ${item.percentage}%" aria-valuenow=" ${item.percentage}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="badge bg-danger-subtle text-${getBgColor(item.percentage)}" title="Overall achieved">
                               ${item.percentage}%
                            </span>
                        </div>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Percentage of Overall ${item.kpi_title} Achieved</p>
                </div>
            </div>
        </div>
    </div>`;
                    })

                    $("#totalProgressSection").append(html)
                }
            },
            error: function (jqXhr, json, errorThrown) {
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);
            },
        })

    }
    getTeamTotals();

    $("#refreshData").on("click", function() {
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.kpi.team.refresh.report') }}" + '?month=' +
                convertMonthToDateFormat($("#month").val()),
            type: 'GET',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                if (result.status == 1) {
                    showToastify(result.msg, "success")
                    location.reload()
                } else {
                    showToastify(result.msg, "error")
                }

            },
            error: function(jqXhr, json, errorThrown) {
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);

                showToastify('Something went wrong...', "error")
            },
        });
    })
    $("#refreshAgentData").on("click", function() {
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.kpi.agent.refresh.report') }}" +
                `?month=${convertMonthToDateFormat($("#month").val())}`,
            type: 'GET',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                showToastify(result.msg, "success")
                $('#edit-kpi-modal').modal('hide');
                location.reload();
               // getReports();
            },
            error: function(jqXhr, json, errorThrown) {
                console.log(jqXhr);
                console.log(json);
                console.log(errorThrown);

                showToastify('Something went wrong...', "error")
            },
        });
    })
    // $("#sidebarReports").addClass('collapse show');
    $('#kpiReportSidebar').addClass('active');
    // $("#reportsActive").addClass('active');
</script>
@endsection
