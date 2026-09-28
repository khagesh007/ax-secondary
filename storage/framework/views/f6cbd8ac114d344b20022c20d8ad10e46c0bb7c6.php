<?php $__env->startSection('title'); ?>
    KPI
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
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
        .super-agent-badge{
            background: #3a307f;
            font-weight: 400;
            color: white !important;
            font-size: 11px !important;
            border-radius: 0.4rem !important;
            padding: .16em .45em !important;
        }
    </style>

    <style>
        .progress-ring-container {
            display: inline-block;
            position: relative;
        }

        .progress-ring {
            transform: rotate(-90deg); /* Rotate to make the progress start from the top */
        }

        .progress-ring circle {
            fill: none;
            stroke-width: 4; /* Adjust stroke width to suit the smaller size */
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
            font-size: 8px; /* Smaller text size */
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: middle;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Ensures text is centered */
        }
        .avatar-custom{
                height: 4.0rem !important;
                width: 4.1rem;

        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Team <?php echo e($group->name); ?> - <span class="text-muted fs-12">Key Performance Indicator Report</span></h4>
                        <ol class="breadcrumb m-0 text-muted">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kpi</a></li>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(route('reports.team.kpi-v2.team.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item">View</li>
                            <li class="breadcrumb-item active"><?php echo e($group->name); ?></li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex">
                                <input type="text" class="form-control" name="month"
                                    value="<?php echo e(request('month') ? date('F Y', strtotime(request('month'))) : date('F Y')); ?>"
                                    id="month" placeholder="Select Month">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($reports) == 0): ?>
        <div class="alert alert-warning" role="alert">
            No target set for this month
        </div>
    <?php endif; ?>
    <?php if(count($reports) > 0): ?>
        <div class="row">
            <div class="col-3 ">
                <div class="card">
                    <div class="card-body">
                        <a class="d-flex align-items-center" aria-expanded="false" aria-controls="teamTileCard4">
                            <div class="avatar-custom flex-shrink-0">
                                <span class="avatar-title bg-light rounded-circle fs-3 ax-text">
                                    <?php echo e($group?->name); ?>

                                </span>
                            </div>


                            <div class="flex-grow-1 ms-3" style="direction: rtl;
    margin-right: 5px;">
                                <h6 class="fs-14 mb-1"><span class="text-muted"><?php echo e($group?->user?->full_name); ?></span>
                                    <b></b>
                                </h6>


                                <p class="text-muted fs-10 mb-0"><?php echo e($group->user->position); ?></p>



                            </div>
                            <div class="flex-shrink-0">
                                <img src="<?php echo e($group?->user?->thumbnail); ?>" alt="" class="avatar-xs rounded">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-body d-none">
                        <b>Overall Progress
                            <span id="overallProgressNumber" class="text-muted fs-12"></span>
                        </b>
                        <div class="progress animated-progress custom-progress mb-1" id="overall_progress_div">

                        </div>
                    </div>

                    <div class="card-body">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <?php
                                $total_target = 0;
                                $total_achievement = 0;
                            ?>
                            <?php $__currentLoopData = $reports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i
                                                class="ri <?php echo e($target->kpi == 'listings' ? 'ri-question-answer-line' : ($target->kpi == 'viewings' ? 'ri-mac-line' : 'ri-earth-line')); ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?php echo e(getKpiFullText($target->kpi)); ?>

                                                (<?php echo e(formatNumberMetricPrefixes($target?->result?->achieved ?? 0)); ?>/<?php echo e(formatNumberMetricPrefixes($target?->target)); ?>)
                                                <?php
                                                    $total_target += $target?->target;
                                                    $total_achievement += $target?->result?->achieved;
                                                     $percentage = ($target?->target != 0) ? ($target?->result?->achieved / $target?->target) * 100 : 0;
                                                ?>
                                                <a href="javascript:void(0)"
                                                   onclick="toggleColumnCards('<?php echo e($target->kpi); ?>')" class="float-end">
                                                    <i class=" bx bx-info-circle"></i>
                                                </a>
                                            </h6>
                                            <div class="progress animated-progress custom-progress mb-1">
                                                <div class="progress-bar bg-<?php echo e(($percentage ?? 0) >= 75 ? 'success' : (($percentage ?? 0) >= 50 ? 'warning' : 'danger')); ?>"
                                                     role="progressbar"
                                                     style="width: <?php echo e($percentage ?? 0); ?>%"
                                                     aria-valuenow="<?php echo e($percentage ?? 0); ?>"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                                <span style="margin-top:-4px;"><?php echo e(number_format($percentage) ?? 0); ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                        <div class="row">
                            <div class="col-12 mt-2 text-muted">
                                <?php
                                    $achived_percentage = ($total_achievement / $total_target) * 100;

                                ?>
                                <?php echo e(round($achived_percentage,2)); ?>% Target Achived So Far (AED <?php echo e(number_format($total_achievement)); ?>)
                            </div>
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
                            <div class="col-12">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1"><span class=""><?php echo e(number_format($avg_gross_commission?->total,2)); ?>%</span>
                                        <span class="text-success ms-1 fs-12"><i
                                                class="ri-arrow-right-up-line ms-1 align-middle"></i></span>
                                    </h5>
                                    <p class="text-muted mb-0">Avg. Gross Commission</p>
                                </div>
                            </div>
                            <!--end col-->


                        </div>
                    </div>

                <div class="card-body">
                    <table
                    class="table table-bordered"
                    >
                        <tbody>
                        <?php $__currentLoopData = $kpi_without_target; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpiw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                if($kpiw->kpi == 'gross_revenue' || $kpiw->kpi == 'avg_sales_commission' || $kpiw->kpi == 'avg_rental_revenue' ||$kpiw->kpi == 'avg_sales_revenue' || $kpiw->kpi == 'gross_revenue' || $kpiw->kpi == 'gross_revenue' ||  $kpiw->kpi == 'rental_revenue' ||  $kpiw->kpi == 'sales_revenue' ||  $kpiw->kpi == 'rental_commission' || $kpiw->kpi == 'sales_commission'){
                                    $achived = formatNumberMetricPrefixes($kpiw?->total);
                                }else{
                                    $achived = $kpiw?->total;
                                }
                            ?>
                            <?php if($kpiw->kpi == 'gross_commission_amount'): ?>
                        <tr>
                            <td><?php echo e(getKpiFullText($kpiw->kpi)); ?></td>
                            <td><?php echo e(formatNumberMetricPrefixes($achived)); ?>

                                <div class="align-self-end float-end d-none <?php echo e($kpiw->kpi); ?>-flag" data-kpi="<?php echo e($kpiw->kpi); ?>" data-achived="<?php echo e($kpiw?->total); ?>">
                                <span class="badge bg-danger-subtle text-success" title="Compared to the previous month">
                                    <i class="ri-arrow-up-s-fill align-middle me-1"></i>100%
                                </span>
                                </div>
                            </td>
                        </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="row mt-5">

                    </div>
                </div>

                </div>


            </div>


            <div class="col-6">
                <div class="card d-none">

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
                                <li class="nav-item d-none" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#compare-commmission-tab"
                                        role="tab" aria-selected="false">
                                      Commission
                                    </a>
                                </li>
                                <li class="nav-item d-none" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#compare-chart-tab" role="tab"
                                        aria-selected="true">
                                        <i class="ri-numbers-line"></i>
                                    </a>
                                </li>
                                <li class="nav-item d-none" role="presentation">
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
            <div class="row d-none">
                <div class="col-4">
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

                <div class="col-4 col-md-6 d-none">
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

                <div class="col-4 d-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">

                                <div class="flex-grow-0">
                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1"> Gross Revenue
                                        <a href="javascript:void(0)" onclick="getTeamExtraData('gross_revenue')"
                                           class="float-end info-btn">
                                            <i class=" bx bx-info-circle"></i>
                                        </a>
                                    </p>
                                    <h5 class=" mb-3" id="totalGrossRevenueCountCard">AED <span
                                            class="counter-value" data-target="20000">0</span></h5>
                                </div>

                            </div>
                            <div class="align-self-end float-end trend-margin-top totalGrossRevenueTrend">

                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
                <div class="col-4">
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
                <div class="col-4">
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

            <?php $__currentLoopData = $targets ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 target-cols <?php echo e($target->kpi); ?>-cols <?php echo e($index != 0 ? 'd-none' : ''); ?>">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <div class="flex-grow-1 oveflow-hidden">
                                <p class="text-muted text-truncates mb-0"><?php echo e($target->kpi); ?> Details</p>
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
                                        <?php echo e($target->is_processed == 1 ? 'bg-primary' : ''); ?>

                                        <?php echo e($target->is_processed == 2 ? 'bg-info' : ''); ?>

                                        <?php echo e($target->is_processed == 0 ? 'bg-warning' : ''); ?>">
                                                <?php echo e($target->is_processed == 1 ? 'Processed' : ''); ?>

                                                <?php echo e($target->is_processed == 2 ? 'In Progress' : ''); ?>

                                                <?php echo e($target->is_processed == 0 ? 'Pending' : ''); ?>

                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Target</b></td>
                                        <td><?php echo e($target->target); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Daily Target Average</b> <span
                                                title="The agent must maintain this average to achieve the target."><i
                                                    class=" ri-information-line"></i></span></td>
                                        <td>
                                            <span
                                                title="<?php echo e(number_format($target->result->average_per_day ?? 0, 2)); ?>"><?php echo e(roundOff($target->result->average_per_day ?? 0)); ?></span>

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
                                            <?php $agentDailyAverage = ($target->result->achieved ?? 0) / $target->duration; ?>
                                            <span
                                                class="<?php echo e($agentDailyAverage > ($target->result->average_per_day ?? 0) ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($agentDailyAverage, 2)); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Achieved</b></td>
                                        <td><?php echo e($target->result->achieved ?? 0); ?>

                                            (<?php echo e(number_format($target->result->achieved_percentage ?? 0, 2)); ?>%)
                                            <?php echo ($target->result->achieved ?? 0) < $target->target
                                                ? ' <span class="text-danger">Below Target</span>'
                                                : ' <span class="text-success">Above Target</span>'; ?>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td><b>Duration</b></td>
                                        <td><?php echo e(date('d/m/y', strtotime($target->start_date))); ?> -
                                            <?php echo e(date('d/m/y', strtotime($target->end_date))); ?>

                                            (<?php echo e($target->duration); ?>)
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6 target-cols <?php echo e($target->kpi); ?>-cols">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <div class="flex-grow-1 oveflow-hidden">
                                <p class="text-muted text-truncates mb-0"><?php echo e($target->kpi); ?> Date Wise</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="<?php echo e($target->kpi); ?>-date-wise-chart" class="date-wise-chart"
                                data-dataset="<?php echo e(json_encode($target->result->data ?? [])); ?>" style="height: 300px">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('assets/libs/echarts/echarts.min.js')); ?>"></script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?php $__env->startSection('global_scripts'); ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.6.0/echarts.min.js"  referrerpolicy="no-referrer"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->yieldContent('pageDT'); ?>
<?php echo $__env->yieldPushContent('in_page_scripts'); ?>
<script>
    let filtersData = [];
    let total_target = <?php echo e($total_target ?? 0); ?>;
    let total_achievement = <?php echo e($total_achievement ?? 0); ?>;
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
    const this_month_kpi_without_target = [];

    let previous_month_total_deals = 0;
    let previous_month_sales_deal_count = 0;
    let previous_month_rental_deals_count = 0;
    let previous_month_gross_revenue = 0;
    const previous_month_kpi_without_target = [];

    let target_sales_value = 0;
    let achieved_sales_value = 0;
    <?php
    $achived = 0;
    ?>
    <?php $__currentLoopData = $reports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
    if(isset($kpiw)){
        if($kpiw->kpi == 'gross_revenue' || $kpiw->kpi == 'avg_sales_commission' || $kpiw->kpi == 'avg_rental_revenue' ||$kpiw->kpi == 'avg_sales_revenue' || $kpiw->kpi == 'gross_revenue' || $kpiw->kpi == 'gross_revenue' ||  $kpiw->kpi == 'rental_revenue' ||  $kpiw->kpi == 'sales_revenue' ||  $kpiw->kpi == 'rental_commission' || $kpiw->kpi == 'sales_commission'){
            $achived = formatNumberMetricPrefixes($kpiw?->total);
        }else{
            $achived = $kpiw?->total;
        }
    }
    ?>
    <?php if(isset($kpiw)): ?>
    <?php if($kpiw->kpi == 'avg_gross_commission'): ?>
    achieved_sales_value = "<?php echo e($achived); ?>"

    <?php endif; ?>
    <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


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
    let truBrokerBanner = ` <svg width="84" height="22" viewBox="0 0 84 22" fill="none" xmlns="http://www.w3.org/2000/svg" format="plural"><rect width="84" height="22" rx="5" fill="url(#paint0_linear_666_62002)"></rect><path d="M11.066 16V8.374H8.12V7.36H15.08V8.374H12.134V16H11.066ZM15.4377 16V9.52H16.3917V11.086L16.2357 10.882C16.3117 10.682 16.4097 10.498 16.5297 10.33C16.6537 10.162 16.7917 10.024 16.9437 9.916C17.1117 9.78 17.3017 9.676 17.5137 9.604C17.7257 9.532 17.9417 9.49 18.1617 9.478C18.3817 9.462 18.5877 9.476 18.7797 9.52V10.522C18.5557 10.462 18.3097 10.446 18.0417 10.474C17.7737 10.502 17.5257 10.6 17.2977 10.768C17.0897 10.916 16.9277 11.096 16.8117 11.308C16.6997 11.52 16.6217 11.75 16.5777 11.998C16.5337 12.242 16.5117 12.492 16.5117 12.748V16H15.4377ZM22.3742 16.168C21.9582 16.168 21.6002 16.102 21.3002 15.97C21.0042 15.838 20.7582 15.662 20.5622 15.442C20.3662 15.222 20.2122 14.978 20.1002 14.71C19.9882 14.442 19.9082 14.168 19.8602 13.888C19.8162 13.608 19.7942 13.346 19.7942 13.102V9.52H20.8742V12.766C20.8742 13.07 20.9022 13.364 20.9582 13.648C21.0142 13.932 21.1082 14.188 21.2402 14.416C21.3762 14.644 21.5562 14.824 21.7802 14.956C22.0082 15.088 22.2922 15.154 22.6322 15.154C22.9002 15.154 23.1442 15.108 23.3642 15.016C23.5842 14.924 23.7722 14.788 23.9282 14.608C24.0842 14.424 24.2042 14.192 24.2882 13.912C24.3722 13.632 24.4142 13.306 24.4142 12.934L25.1162 13.15C25.1162 13.774 25.0022 14.312 24.7742 14.764C24.5502 15.216 24.2322 15.564 23.8202 15.808C23.4122 16.048 22.9302 16.168 22.3742 16.168ZM24.5342 16V14.266H24.4142V9.52H25.4882V16H24.5342ZM27.1681 16V7.36H30.5941C31.1781 7.36 31.6561 7.478 32.0281 7.714C32.4041 7.946 32.6821 8.244 32.8621 8.608C33.0461 8.972 33.1381 9.348 33.1381 9.736C33.1381 10.212 33.0241 10.616 32.7961 10.948C32.5721 11.28 32.2661 11.504 31.8781 11.62V11.32C32.4221 11.444 32.8341 11.702 33.1141 12.094C33.3981 12.486 33.5401 12.932 33.5401 13.432C33.5401 13.944 33.4421 14.392 33.2461 14.776C33.0501 15.16 32.7581 15.46 32.3701 15.676C31.9861 15.892 31.5101 16 30.9421 16H27.1681ZM28.6321 14.638H30.7621C31.0141 14.638 31.2401 14.588 31.4401 14.488C31.6401 14.384 31.7961 14.24 31.9081 14.056C32.0241 13.868 32.0821 13.646 32.0821 13.39C32.0821 13.158 32.0321 12.95 31.9321 12.766C31.8321 12.582 31.6861 12.436 31.4941 12.328C31.3021 12.216 31.0721 12.16 30.8041 12.16H28.6321V14.638ZM28.6321 10.81H30.5761C30.7841 10.81 30.9701 10.77 31.1341 10.69C31.2981 10.61 31.4281 10.492 31.5241 10.336C31.6201 10.18 31.6681 9.988 31.6681 9.76C31.6681 9.46 31.5721 9.21 31.3801 9.01C31.1881 8.81 30.9201 8.71 30.5761 8.71H28.6321V10.81ZM34.7384 16V9.52H36.0164V11.098L35.8604 10.894C35.9404 10.678 36.0464 10.482 36.1784 10.306C36.3144 10.126 36.4764 9.978 36.6644 9.862C36.8244 9.754 37.0004 9.67 37.1924 9.61C37.3884 9.546 37.5884 9.508 37.7924 9.496C37.9964 9.48 38.1944 9.488 38.3864 9.52V10.87C38.1944 10.814 37.9724 10.796 37.7204 10.816C37.4724 10.836 37.2484 10.906 37.0484 11.026C36.8484 11.134 36.6844 11.272 36.5564 11.44C36.4324 11.608 36.3404 11.8 36.2804 12.016C36.2204 12.228 36.1904 12.458 36.1904 12.706V16H34.7384ZM42.2762 16.18C41.6282 16.18 41.0622 16.034 40.5782 15.742C40.0942 15.45 39.7182 15.048 39.4502 14.536C39.1862 14.02 39.0542 13.428 39.0542 12.76C39.0542 12.08 39.1902 11.484 39.4622 10.972C39.7342 10.46 40.1122 10.06 40.5962 9.772C41.0802 9.484 41.6402 9.34 42.2762 9.34C42.9282 9.34 43.4962 9.486 43.9802 9.778C44.4642 10.07 44.8402 10.474 45.1082 10.99C45.3762 11.502 45.5102 12.092 45.5102 12.76C45.5102 13.432 45.3742 14.026 45.1022 14.542C44.8342 15.054 44.4582 15.456 43.9742 15.748C43.4902 16.036 42.9242 16.18 42.2762 16.18ZM42.2762 14.824C42.8522 14.824 43.2802 14.632 43.5602 14.248C43.8402 13.864 43.9802 13.368 43.9802 12.76C43.9802 12.132 43.8382 11.632 43.5542 11.26C43.2702 10.884 42.8442 10.696 42.2762 10.696C41.8882 10.696 41.5682 10.784 41.3162 10.96C41.0682 11.132 40.8842 11.374 40.7642 11.686C40.6442 11.994 40.5842 12.352 40.5842 12.76C40.5842 13.388 40.7262 13.89 41.0102 14.266C41.2982 14.638 41.7202 14.824 42.2762 14.824ZM46.8202 16L46.8322 7.36H48.2962V12.64L50.6662 9.52H52.4722L49.9582 12.76L52.6882 16H50.7742L48.2962 12.88V16H46.8202ZM56.118 16.18C55.462 16.18 54.886 16.038 54.39 15.754C53.894 15.47 53.506 15.076 53.226 14.572C52.95 14.068 52.812 13.488 52.812 12.832C52.812 12.124 52.948 11.51 53.22 10.99C53.492 10.466 53.87 10.06 54.354 9.772C54.838 9.484 55.398 9.34 56.034 9.34C56.706 9.34 57.276 9.498 57.744 9.814C58.216 10.126 58.566 10.568 58.794 11.14C59.022 11.712 59.108 12.386 59.052 13.162H57.618V12.634C57.614 11.93 57.49 11.416 57.246 11.092C57.002 10.768 56.618 10.606 56.094 10.606C55.502 10.606 55.062 10.79 54.774 11.158C54.486 11.522 54.342 12.056 54.342 12.76C54.342 13.416 54.486 13.924 54.774 14.284C55.062 14.644 55.482 14.824 56.034 14.824C56.39 14.824 56.696 14.746 56.952 14.59C57.212 14.43 57.412 14.2 57.552 13.9L58.98 14.332C58.732 14.916 58.348 15.37 57.828 15.694C57.312 16.018 56.742 16.18 56.118 16.18ZM53.886 13.162V12.07H58.344V13.162H53.886ZM60.3908 16V9.52H61.6688V11.098L61.5128 10.894C61.5928 10.678 61.6988 10.482 61.8308 10.306C61.9668 10.126 62.1288 9.978 62.3168 9.862C62.4768 9.754 62.6528 9.67 62.8448 9.61C63.0408 9.546 63.2408 9.508 63.4448 9.496C63.6488 9.48 63.8468 9.488 64.0388 9.52V10.87C63.8468 10.814 63.6248 10.796 63.3728 10.816C63.1248 10.836 62.9008 10.906 62.7008 11.026C62.5008 11.134 62.3368 11.272 62.2088 11.44C62.0848 11.608 61.9928 11.8 61.9328 12.016C61.8728 12.228 61.8428 12.458 61.8428 12.706V16H60.3908ZM67.4514 16.18C66.6514 16.18 66.0014 16 65.5014 15.64C65.0014 15.28 64.6974 14.774 64.5894 14.122L66.0654 13.894C66.1414 14.214 66.3094 14.466 66.5694 14.65C66.8294 14.834 67.1574 14.926 67.5534 14.926C67.9014 14.926 68.1694 14.858 68.3574 14.722C68.5494 14.582 68.6454 14.392 68.6454 14.152C68.6454 14.004 68.6094 13.886 68.5374 13.798C68.4694 13.706 68.3174 13.618 68.0814 13.534C67.8454 13.45 67.4834 13.344 66.9954 13.216C66.4514 13.072 66.0194 12.918 65.6994 12.754C65.3794 12.586 65.1494 12.388 65.0094 12.16C64.8694 11.932 64.7994 11.656 64.7994 11.332C64.7994 10.928 64.9054 10.576 65.1174 10.276C65.3294 9.976 65.6254 9.746 66.0054 9.586C66.3854 9.422 66.8334 9.34 67.3494 9.34C67.8534 9.34 68.2994 9.418 68.6874 9.574C69.0794 9.73 69.3954 9.952 69.6354 10.24C69.8754 10.528 70.0234 10.866 70.0794 11.254L68.6034 11.518C68.5674 11.242 68.4414 11.024 68.2254 10.864C68.0134 10.704 67.7294 10.614 67.3734 10.594C67.0334 10.574 66.7594 10.626 66.5514 10.75C66.3434 10.87 66.2394 11.04 66.2394 11.26C66.2394 11.384 66.2814 11.49 66.3654 11.578C66.4494 11.666 66.6174 11.754 66.8694 11.842C67.1254 11.93 67.5054 12.038 68.0094 12.166C68.5254 12.298 68.9374 12.45 69.2454 12.622C69.5574 12.79 69.7814 12.992 69.9174 13.228C70.0574 13.464 70.1274 13.75 70.1274 14.086C70.1274 14.738 69.8894 15.25 69.4134 15.622C68.9414 15.994 68.2874 16.18 67.4514 16.18ZM76.0796 11.86V7.36H76.7516L78.3956 10.102L80.0396 7.36H80.7116L80.7176 11.86H79.9376V8.926L78.6476 11.122H78.1436L76.8596 8.926V11.86H76.0796ZM72.8696 11.86V8.116H71.3216V7.36H75.2276V8.116H73.6856V11.86H72.8696Z" fill="white"></path><defs><linearGradient id="paint0_linear_666_62002" x1="79.4063" y1="-5.17647" x2="6.40824" y2="36.4682" gradientUnits="userSpaceOnUse"><stop offset="0.196272" stop-color="#053940"></stop><stop offset="0.489583" stop-color="#00745F"></stop><stop offset="0.720273" stop-color="#033F46"></stop></linearGradient></defs></svg>
                               `;
    let truBrokerBannerPng = `<img src="<?php echo e(asset('assets/images/bayut/truBroker.png')); ?>" style="width:90px;">`;

    let isSuperAgentBanner = ` <span class="badge super-agent-badge"
                                        style="font-weight: 500;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            class="r-1xlzkty r-1472mwg r-lrsllp">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.9096 6.45575L12.4054 3.26454H12.407C12.2402 2.91182 11.7607 2.91182 11.5955 3.26454L10.0897 6.45575C10.024 6.59516 9.8973 6.69258 9.74977 6.71441L6.38697 7.22669C6.01654 7.28379 5.86901 7.7608 6.13681 8.03457L8.5695 10.5187C8.67534 10.6279 8.72505 10.7857 8.69939 10.9386L8.1253 14.4456C8.06115 14.8319 8.44923 15.1275 8.78118 14.9444L11.7896 13.2883C11.9211 13.2161 12.0782 13.2161 12.2097 13.2883L15.2181 14.9444C15.5501 15.1275 15.9381 14.8319 15.874 14.4456L15.2999 10.9386C15.2742 10.7841 15.3239 10.6279 15.4298 10.5187L17.8625 8.03457C18.1319 7.7608 17.9828 7.28379 17.6123 7.22669L14.2495 6.71441C14.102 6.69258 13.9753 6.59516 13.9096 6.45575ZM15.6043 18.9989V17.9316C15.6043 17.5384 15.2024 17.2206 14.7063 17.2206H13.3601V15.0859H10.6661V17.2206H9.31989C8.82375 17.2206 8.42188 17.5384 8.42188 17.9316V18.9989H15.6043ZM16.0909 19H7.90909C7.40683 19 7 19.2978 7 19.6661V21H17V19.6661C17 19.2978 16.5932 19 16.0909 19Z"
                                                fill="currentColor"></path>
                                        </svg>
                                        Super Agent
                                    </span>`;
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
    <?php if(count($reports) > 0): ?>
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
                url: "<?php echo e(route('reports.team.kpi-v2.get.report.breakdown')); ?>",
                type: 'GET',
                data: {
                    group_id: "<?php echo e($group->id); ?>",
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
                url: "<?php echo e(route('reports.team.kpi-v2.get.team.kpi.data')); ?>",
                type: 'GET',
                data: {
                    group_id: "<?php echo e($group->id); ?>",
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

        // getBreakdown("<?php echo e($targets[0]->kpi ?? 'listings'); ?>")
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
            return '';
            const container = document.querySelector(`.${className}`);
            if (!container) {
                console.log("Element with the provided class name not found.");
                return;
            }
            let formattedDifference = 0;
            let difference = 0;
            console.log('current value '+currentValue+' previousValue '+className+' '+previousValue);

           if(previousValue == 0 || previousValue == null || previousValue == undefined || previousValue == ''){
            if(currentValue > 0){
                formattedDifference = 100;
                difference = 100;
            }else{
                formattedDifference = 0;
                difference = 0;
            }
           }{
            let tempVarA = currentValue - previousValue;

            let tempVarB = tempVarA > 0 ? (tempVarA / previousValue) : 0;

             difference = tempVarB * 100;
            console.log('tempVarA '+tempVarA+' tempVarB '+tempVarB +' difference '+difference);

            formattedDifference = (isNaN(difference) || difference === Infinity) ? 0 : difference.toFixed(2); // Keep 2 decimal points
           }
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
                        data: [0]
                    },
                    {
                        name: 'Avg Sales Commission',
                        data: [0]
                    },
                    {
                        name: 'Average Rental Revenue',
                        data: [0]
                    },
                    {
                        name: 'Average Sales Revenue',
                        data: [0]
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
                url: "<?php echo e(route('reports.team.kpi-v2.get.team.agent.report')); ?>",
                type: 'GET',
                data: {
                    group_id: "<?php echo e($group->id); ?>",
                    month: convertMonthToDateFormat($("#month").val()),

                },
                success: function(response) {
                    if (response.status == 1) {
                        let kpi_report = response.data.kpi_report;
                        let deal_report = response.data.deal_data;
                        this_month_kpi_without_target.value = response.data.kpi_without_target;
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

                       // let pieData = [this_month_kpi_without_target.value.rental_revenue, this_month_kpi_without_target.value.sales_revenue];
                        let rental_revenue = deal_report.rental_revenue;
                        let sales_revenue = deal_report.sales_revenue;
                        let doughnutData = [rental_revenue,sales_revenue];
                        let labels = ['Rental Revenue','Sales Revenue']
                        doughnutChart(labels,doughnutData, 'comparePieChart');

                      //  $(".avg_sales_revenue_metrics").text(formatNumberMetricPrefixes(deal_report.avg_sales_revenue));
                        $(".avg_rental_revenue_metrics").text(formatNumberMetricPrefixes(deal_report.avg_rental_revenue));
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
                            console.log(item)
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
                url: "<?php echo e(route('reports.team.kpi-v2.get.team.agent.report')); ?>",
                type: 'GET',
                data: {
                    group_id: "<?php echo e($group->id); ?>",
                    month: subtractMonth(convertMonthToDateFormat($("#month").val())),

                },
                success: function(response) {
                    if (response.status == 1) {
                        let kpi_report = response.data.kpi_report;
                        let deal_report = response.data.deal_data;
                       let pre_month_kpi_without_target = response.data.kpi_without_target;

                       //loop through pre_month_kpi_without_target
                        Object.entries(pre_month_kpi_without_target).forEach(([key, item]) => {
                            let this_month_achivmnt = $("."+key+"-flag").data('achived')
                             this_month_achivmnt = this_month_achivmnt === undefined ? 0 : this_month_achivmnt;
                            appendTrendFlag(this_month_achivmnt, item,
                                key+"-flag");
                        })

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


    const doughnutChart = (labels, data, elementId) => {
        // Chart options
        var options = {
            series: data, // Use the dynamic data passed to the function
            chart: {
                type: 'donut',
            },
            labels: labels, // Use the dynamic labels passed to the function
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 140
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        // Render the chart
        var chart = new ApexCharts(document.querySelector("#" + elementId), options);
        chart.render();
    }
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


    <?php endif; ?>


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
        if (key == "live_listings" || key == "new_listings"){
            return "ri-home-line"
        }
        if (key == "sales_target"){
            return "ri-bank-card-line"
        }
        if (key == "offers"){
            return "ri-bank-card-line"
        }
        if (key == "calls"){
            return "ri-phone-line"
        }
        if (key == "leads"){
            return "ri-account-box-line"
        }
        if (key == "deals"){
            return "ri-bank-card-2-line"
        }
        if (key == "viewings"){
            return "ri-eye-line"
        }
        if (key == "meetings"){
            return "ri-group-line"
        }
        if (key == "follow-up"){
            return "ri-calendar-line"
        }

        return "ri-earth-line"
    }

    let viewAgentKpiURL = "<?php echo e(route('reports.group.kpi-v2.view', 1)); ?>";

    const makeAgentCard = (item) => {
        let target = "";
        if (item.target){
            //.replace(/[^\d]/g, '')
            const targetCount = removeDecimal(item.target?.target) || "0";

            const achievedCount = removeDecimal(item.target?.result?.achieved) || 0;
            target = `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="mdi mdi-handshake-outline ax-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(item.target?.kpi || "")} ${formatNumberMetricPrefixes(achievedCount)}/${formatNumberMetricPrefixes(targetCount)}</h6>
                                </div>
                            </div>
                        </li>`;
        }

        let reports = "";
        item.reports?.map(report => {
            reports += `<li>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-xxs text-muted">
                                    <i class="mdi mdi-handshake-outline ax-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${ucwords(report.kpi_title)}: ${ report.kpi == "avg_gross_commission" ? `${report.count || 0}%` : formatNumberMetricPrefixes(parseFloat((report.count || "0").replace(/[^\d]/g, '')))}</h6>
                                </div>
                            </div>
                        </li>`;

        })

        let progress = (item.agent.achieved / item.agent.target) * 100;
        let progressColor = progress >= 75 ? 'success' : (progress >= 50 ? 'warning' : 'danger');
        let progressWidth = (progress > 100 ? 100 : progress).toFixed();

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

                                <div class="d-flex justify-content-between gap-2">
                                    <div class="progress animated-progress custom-progress mb-4" style="width: 90%">
                                        <div class="progress-bar bg-${progressColor}" role="progressbar" style="width: ${progressWidth}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="badge bg-${progressColor}-subtle text-${progressColor}" title="Percentage of achieved">
                                        ${isNaN(progressWidth) ? "0" : progressWidth}%
                                    </span>
                                </div>

                            </div>
                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse" id="leadDiscovered${item.agent.id}" style="">
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 mb-0">
                                ${target}
                                ${reports}
                            </ul>
                        </div>
                        <div class="card-footer hstack gap-2">
                            <a href="${viewAgentKpiURL.slice(0, -1)}${item.agent.id}" target="_blank" class="btn btn-info btn-sm w-100"><i class="ri-eye-line align-bottom me-1"></i> Full Report</a>
                        </div>
                    </div>
                </div></div></div>`;

    }

    const makeAgentKpiReportCard = (item) => {
        let targets = "";
        let totalTarget = 0;
        let totalAchieved = 0;
        let achievedPercentages = [];
        item.targets?.map(target => {
            const targetCount = parseFloat((target?.target || "0").replace(/[^\d]/g, ''));
            const achievedCount = removeDecimal(target?.result?.achieved);
            totalTarget += targetCount
            totalAchieved += achievedCount
            //calculate achived percentage
           // let achived_percentage = calculatePercentage(targetCount, achievedCount);
            achievedPercentages.push(parseFloat(target.result?.achieved_percentage || 0))

            targets += `<div class="col">
                            <div class="py-2 px-1">
                                <h5 class="text-muted text-uppercase text-center fs-10">${ucwords(target.kpi)}</h5>
                                <div class="text-center">
                                    <div class="progress-ring-container">
                                        <svg width="30" height="30" class="progress-ring">
                                            <circle class="background" cx="15" cy="15" r="13"></circle>
                                            <circle class="progress-${getBgColor(target.result?.achieved_percentage || 0)}" cx="15" cy="15" r="13" stroke-dasharray="81.68" stroke-dashoffset="${81.68 * (1 - (Math.min(100, (target.result?.achieved_percentage || 0)) / 100))}"></circle>
                                        </svg>
                                        <text x="50%" y="50%" class="percentage-text">${target.result?.achieved_percentage || 0}%</text>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <h2 class="mb-0 fs-13"><span>${formatNumberMetricPrefixes(achievedCount)}/${formatNumberMetricPrefixes(targetCount)}</span></h2>
                                </div>
                            </div>
                        </div>`;

        })

        let otherReports = "";
        item.other_reports?.map(report => {
            let report_count = removeDecimal(report.count);
            otherReports += `<li>
                                <div class="d-flex justify-content-start gap-2">
                                    <div class="d-flex justify-content-center gap-1">
                                        <i class="${getIcon(report.kpi)} text-muted"></i>
                                        <div>${ucwords(report.kpi)}:</div>
                                    </div>
                                    <div>${formatNumberMetricPrefixes(parseFloat(report_count))}</div>
                                </div>
                        </li>`;
        })

        const totalAchievedPercentage = calculatePercentage(totalTarget, totalAchieved);

        const calculateAverage = () =>  {
            const total = achievedPercentages.reduce((sum, percentage) => sum + percentage, 0);
            if (total == 0){
                return 0
            }
            return (total / achievedPercentages.length).toFixed(2);
        }

        const agentAveragePercentage = calculateAverage();


        return `<div class="col-3"><div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#agentKpiCard${item.agent.id}"
                            role="button" aria-expanded="false" aria-controls="agentKpiCard${item.agent.id}">
                            <div class="flex-shrink-0">
                                <img src="${item.agent.thumbnail}"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-12 mb-1">${item.agent.full_name}</h6>
                                <div class="d-flex justify-content-between">

                                </div>
                                <div class="d-flex justify-content-between gap-2">
                                    <div class="progress animated-progress custom-progress" style="width: 90%">
                                        <div class="progress-bar bg-${getBgColor(agentAveragePercentage)}" role="progressbar" style="width: ${agentAveragePercentage}%"
                                            aria-valuenow="${agentAveragePercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="badge bg-${getBgColor(agentAveragePercentage)}-subtle text-${getBgColor(agentAveragePercentage)}" title="Average Percentage of achieved">
                                        ${agentAveragePercentage}%
                                    </span>
 </div>
<div  class="d-flex align-items-center"> ${item.is_truBroker > 0 ? truBrokerBanner : ''} ${item.is_super_agent > 0 ? isSuperAgentBanner : ''}</div>

                            </div>
                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse" id="agentKpiCard${item.agent.id}" style="">
                        <div class="card-body">
                            <div class="card crm-widget">
                                <div class="card-body p-0">
                                    <div class="row row-cols-xxl-3 row-cols-md-3 row-cols-1 g-0">
                                        ${targets}
                                    </div>
                                </div>
                              </div>
                            <ul class="list-unstyled vstack gap-2 mb-0 mt-2">${otherReports}</ul>
                        </div>
                        <div class="card-footer hstack gap-2">
                            <a href="${viewAgentKpiURL.slice(0, -1)}${item.agent.id}" target="_blank" class="btn btn-info btn-sm w-100"><i class="ri-eye-line align-bottom me-1"></i> Full Report</a>
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
            url: "<?php echo e(route('reports.group.kpi-v2.get-report')); ?>",
            type: 'GET',
            data: {
                group_id: "<?php echo e($group->id); ?>",
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

    const randomiseArrayData = (array) =>{
        return array.map(item => getRandomInt(0, 100))
    }

    const smallChart = (values,elementId) => {

        // let values = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 90, 80, 70, 60, 50, 40, 30, 20, 10, 0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        // values = randomiseArrayData(values);
        let label = 'total_deals';

        var options = {
            series: [{
                name: "Results",
                data: values
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
    // smallChart('results_sparkline_charts3');
    // smallChart('salesDealCardChart');
    // smallChart('rentalDealCardChart');
    $("#sidebarReports").addClass('collapse show');
    $('#sidebarTeamKpiV2Report').addClass('active');
    $("#reportsActive").addClass('active');
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/kpi_v2/team/teamReport.blade.php ENDPATH**/ ?>