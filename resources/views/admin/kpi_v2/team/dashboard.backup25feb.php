@extends('admin.layouts.master')
@section('title')
    KPI
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .ax-dark-text{
        color: #b77e62 !Important;
    }
    .horizontal-scroll-container {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
    }
    #tileOfCards {
        display: flex;
        flex-wrap: nowrap; /* Prevent wrapping */
        gap: 10px; /* Optional: Adjust spacing between tiles */
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
                                <input type="text" class="form-control" name="month" value="{{ request("month") ? date("F Y", strtotime(request("month"))) : date('F Y') }}"
                                       id="month" placeholder="Select Month">
                            </div>
                            <div>
                                <a href="{{ route('reports.team.kpi-v2.team.setup') }}" class="btn btn-primary"
                                   id="save-button">Setup KPI</a>
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
        @php
            $targetTotal = 0;
            $achievedTotal = 0;
            $percentages = [];

            foreach ($groups ?? [] as $group){
                foreach ($reports[$group->id] ??[] as $report){
                    $target = floatval($report->target ?? 0);
                    $achieved = floatval($report?->result?->achieved ?? 0);
                    $targetTotal += $target;
                    $achievedTotal += $achieved;
                    $percentage = ($target != 0) ? ($achieved / $target) * 100 : 0;
                    $percentages[] = $percentage;
                }
            }

            function calculateAverage($achievedPercentages) {
                $total = array_sum($achievedPercentages);

                if ($total == 0) {
                    return 0;
                }
                return number_format($total / count($achievedPercentages), 2, '.', '');
            }

            $averagePercentage = calculateAverage($percentages);
        @endphp


        <div class="col-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="text-center">
                        <h5>{{ formatNumberMetricPrefixes($achievedTotal) }}/{{ formatNumberMetricPrefixes($targetTotal) }}</h5>
                        <p class="text-muted fs-12 mb-0">Overall Sales</p>
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
                                <div class="progress-bar bg-{{ ($percentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $averagePercentage }}%"
                                     aria-valuenow="{{ $averagePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="badge bg-{{ ($averagePercentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}-subtle text-{{ ($averagePercentage ?? 0) >= 75 ? 'success' : (($averagePercentage ?? 0) >= 50 ? 'warning' : 'danger') }}" title="Overall achieved">
                                        {{ $averagePercentage }}%
                                    </span>
                        </div>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Percentage of Overall Sales Achieved</p>
                </div>
            </div>
        </div>
    </div>
    <div class="horizontal-scroll-container">
    <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 mt-2" id="tileOfCardss">
        @foreach($groups ?? [] as $group)
            <div class="col">
                <div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse"
                           href="#teamTileCard{{$group->id}}" role="button" aria-expanded="false"
                           aria-controls="teamTileCard{{$group->id}}">

                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-light rounded-circle fs-3 text-white"
                                          style="background-color: {{ $group->color }} !important;"
                                    >
                                       {{ $group?->name }}
                                    </span>
                                    </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-12 mb-1"><span class="text-muted">{{$group?->user?->full_name}}</span></h6>
                                @if($group?->user)
                                    <p class="text-muted fs-12 mb-0">{{ $group->user->position }}</p>
                                @endif
                                <div id="group-{{$group?->id}}-progress">

                                </div>

                            </div>
                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse show" id="teamTileCard{{$group->id}}" style="">
                        <div class="card-body">
                            <p class="text-muted text-truncate-two-lines"></p>
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                @php
                                    $total_target = 0;
                                    $total_achieved = 0;
                                @endphp
                                @foreach($reports[$group->id] ??[] as $report)
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-scan-2-fill ax-text"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{getKpiFullText($report->kpi)}}
                                                    ({{formatNumberMetricPrefixes($report?->result?->achieved)}}/{{formatNumberMetricPrefixes($report->target)}})</h6>
                                                @php

                                                    $total_target += $report->target ?? 0;
                                                    $total_achieved += $report?->result?->achieved ?? 0;
                                                    $percentage = ($report->target != 0) ? ($report?->result?->achieved / $report->target) * 100 : 0;
                                                @endphp

                                                <div class="progress animated-progress custom-progress mb-1">
                                                    <div class="progress-bar bg-{{getProgressbarBgColor($percentage)}}"
                                                         role="progressbar"
                                                         style="width: {{$percentage > 100 ? '100' : $percentage}}%"
                                                         aria-valuenow="{{$percentage}}"
                                                         aria-valuemin="0" aria-valuemax="100"></div>
                                                    <span style="margin-top:-4px;">{{number_format($percentage,2)}}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                @foreach($team_data[$group->id] ??[] as $report)
                                    @if($report->kpi == 'avg_gross_commission')
                                    <li class="">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                                    <i class="ri-bill-line ax-text"></i>
                                                </div>

                                                @php
                                                if($report->kpi == 'gross_revenue' || $report->kpi == 'avg_sales_commission' || $report->kpi == 'avg_rental_revenue' || $report->kpi == 'avg_sales_revenue' || $report->kpi == 'gross_revenue' || $report->kpi == 'gross_revenue' ||  $report->kpi == 'rental_revenue' ||  $report->kpi == 'sales_revenue' ||  $report->kpi == 'rental_commission' || $report->kpi == 'sales_commission'){
                                                    $achived = formatNumberMetricPrefixes($report?->total);
                                                }else{
                                                    $achived = $report?->total;
                                                }
                                                @endphp
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{getKpiFullText($report->kpi)}}:  @if($report->kpi == 'avg_gross_commission' || $report->kpi == 'avg_company_commission'){{number_format($achived,2)}}% @else {{$achived}}@endif</h6>
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer hstack gap-2 teamTotalDiv" data-id="{{$group->id}}"
                             data-target="{{$total_target}}" data-achived="{{$total_achieved}}">

                            <button class="btn btn-warning btn-sm w-100" onclick="editTarget('{{$group->id}}')" {{ (request("month") ?? date("Y-m")) < date("Y-m") ? "disabled" : "" }}><i
                                    class="ri-pencil-fill align-bottom me-1"></i>Edit Targets
                            </button>
                            <a href="{{route('reports.team.kpi-v2.get.report.page',['id'=>$group->id])}}"
                               target="_blank" class="btn btn-info btn-sm w-100"><i
                                    class="ri-eye-line align-bottom me-1"></i> Full Report</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    </div>
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                Notes:
                <ul>
                    <li>
                    Gross Revenue = Buyer Commission + Seller Commission + Vat Amount
                    </li>
                    <li>
                        Sales Revenue = Buyer Commission + Seller Commission
                    </li>
                    <li>
                        Rental Revenue = Tenant Commission + Landlord Commission
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>
    @includeIf("admin.kpi_v2.includes.modals")
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @section('global_scripts')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{asset('assets/libs/cleave.js/cleave.js.min.js')}}"></script>
    @endsection
    @yield('pageDT')
    @stack('in_page_scripts')
    <script>


        $(document).on("change", "#month", function (){
            window.location = "{{ route('reports.team.kpi-v2.team.dashboard') }}?month=" + convertMonthToDateFormat($(this).val())
        })

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

        $(document).ready(function () {
            $("#month").datepicker({
                format: {
                    toDisplay: function (date, format, language) {
                        var d = new Date(date);
                        var options = { year: 'numeric', month: 'long' };
                        return d.toLocaleDateString(language, options);
                    },
                    toValue: function (date, format, language) {
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

        $(document).ready(function () {
            setTimeout(() => {
                $(".teamTotalDiv").each(function () {
                    let total = $(this).data("target");
                    let achieved = $(this).data("achived");
                    let groupId = $(this).data("id");
                    drawTeamAvg(total, achieved, `#group-${groupId}-progress`)
                })
            }, 1000);
        })

        $(document).on("click", ".teamTotalDiv", function () {
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

        function drawTeamAvg(total, achieved, elementId) {
            if (total === 0) {
                return "0";
            }
            let avg = (achieved / total) * 100;
            let max = 100;
            avg = avg > 100 ? '100' : avg;

            $(elementId).html(`<div class="progress-bar bg-${getBgColor(avg)}" role="progressbar" style="width: ${avg}%" aria-valuenow="${avg}" aria-valuemin="0" aria-valuemax="100"></div>`)
        }

        const editTarget = (groupId) => {
            $("#edit-group-kpi-modal-month-title").html($("#month").val())
            $("#edit-group-target-form").trigger("reset")
            $.ajax({
                url: "{{route('reports.team.kpi-v2.get.group.targets')}}",
                type: 'GET',
                data: {
                    group_id: groupId,
                    month: convertMonthToDateFormat($("#month").val()),
                },
                success: function (response) {
                    if (response.status == 1) {
                        let data = response.data;

                        $("#edit-group-target-settings-group_id").val(data.group.id)
                        let targetx = 0;
                        data.targets.map(target => {
                            if(target.target != 0) {
                                targetx = target.target;
                            }else{
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

        $(document).on("submit", "#edit-group-target-form", function (e) {
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
                success: function (result) {
                    showToastify(result.msg, "success")
                    $('#edit-kpi-modal').modal('hide');
                    window.location.reload()
                },
                error: function (jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);

                    showToastify('Something went wrong...', "error")
                },
            });

        })
        $("#refreshData").on("click", function (){
        $.ajax({
            url: "{{ route('reports.group.kpi-v2.kpi.team.refresh.report') }}"+'?month='+convertMonthToDateFormat($("#month").val()),
            type: 'GET',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                if(result.status == 1){
                    showToastify(result.msg, "success")
                    location.reload()
                }else{
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
       // $("#sidebarReports").addClass('collapse show');
        $('#kpiReportSidebar').addClass('active');
       // $("#reportsActive").addClass('active');

    </script>
@endsection
