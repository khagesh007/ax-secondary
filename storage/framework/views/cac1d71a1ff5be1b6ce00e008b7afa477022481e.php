<?php $__env->startSection('title'); ?>
    Quiz
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .img-link .active {
            border: 3px solid #d3a188 !important;
        }

        .btn {
            box-shadow: 0px 0px 0px rgb(255 255 255 / 0%) !important;
        }

        .removeOptionSpan {
            position: absolute;
            right: 0;
            margin-top: -33px;
        }

        .optionInput {
            width: 90% !important;
        }

        .dt-buttons {
            display: none !important;
        }
        .passed{
            background-color: #00b19d;
        }
        .border-passed{
            border-color: #00b19d !important;
        }
        .text-passed{
            color: #00b19d !important;

        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="ri-home-5-fill"></i></a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.quiz.list')); ?>">Quiz</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($quiz->name); ?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>Title: <?php echo e($quiz->name); ?></h4>
                    <div class="gap-2 align-items-center mx-auto my-3 my-lg-0">

                        <div class="text-muted"><span class="badge bg-success text-bg-success"><i
                                    class="mdi mdi-star me-1"></i><?php echo e(number_format($quiz->rating)); ?></span>
                            <?php echo e($quiz->rated_only_count); ?> Ratings</div>
                    </div>
                    <p class="text-muted"> <?php echo e($quiz->description); ?></p>
                    <p class="float-end">

                        <span class="text-muted">Created By: <?php echo e($quiz->created_by?->name); ?>

                            <?php echo e(date('d/m/Y h:i A', strtotime($quiz->created_at))); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row card-body row-cols-md-3 row-cols-1">


        <div class="col col-lg bg-white border-end">
            <div class="py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Questions <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-space-ship-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value"
                                data-target="<?php echo e($statics['total_questions']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Total Marks <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><?php echo e($total_marks); ?></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Total Attempts <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-pulse-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="<?php echo e($attempt_counts ?? 0); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Quiz Users <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-trophy-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><?php echo e($attempted_user_counts); ?></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Quiz Completed <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-service-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value"
                                data-target="<?php echo e($statics['completed_quiz']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>

    <div class="col-12">
        <div class="collapse slideIn" id="collapseContactForm">
            <div class="">
                <div class=" row">
                    <div>


                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header align-items-center d-flex">

                    <h4 class="card-title mb-0 flex-grow-1">Top Scorers</h4>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('admin.quiz.view', ['id' => $quiz->id])); ?>" target="_blank">View More</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Score</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $__currentLoopData = $top_scores_5; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topper): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="d-flex">
                                            <img src="<?php echo e($topper->user->thumb_url); ?>" alt="<?php echo e($topper->user->name); ?>"
                                                class="avatar-xs rounded-3 me-2">
                                            <div>
                                                <h5 class="fs-13 mb-0"><?php echo e($topper->user->name); ?></h5>
                                                <p class="fs-12 mb-0 text-muted"><?php echo e($topper->user?->role_list[0]); ?></p>
                                            </div>
                                        </td>

                                        <td>
                                            <?php echo e($topper->total_marks); ?>

                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Lowest Scorers</h4>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('admin.quiz.view', ['id' => $quiz->id])); ?>" target="_blank">View More</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $top_loser_5; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="d-flex">
                                            <img src="<?php echo e($loser->user->thumb_url); ?>" alt="<?php echo e($loser->user->name); ?>"
                                                class="avatar-xs rounded-3 me-2">
                                            <div>
                                                <h5 class="fs-13 mb-0"><?php echo e($loser->user->name); ?></h5>
                                                <p class="fs-12 mb-0 text-muted"><?php echo e($loser->user?->role_list[0]); ?></p>
                                            </div>
                                        </td>

                                        <td>
                                            <?php echo e($loser->total_marks); ?>

                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Recent Attempts</h4>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('admin.quiz.view', ['id' => $quiz->id])); ?>" target="_blank">View More</a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recent_attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="d-flex">
                                            <img src="<?php echo e($attempt->user->thumb_url); ?>" alt="<?php echo e($attempt->user->name); ?>"
                                                class="avatar-xs rounded-3 me-2">
                                            <div>
                                                <h5 class="fs-13 mb-0"><?php echo e($attempt->user->name); ?></h5>
                                                <p class="fs-12 mb-0 text-muted"><?php echo e($attempt->user?->role_list[0]); ?></p>
                                            </div>
                                        </td>

                                        <td>
                                            <?php echo e($attempt->total_marks); ?>

                                        </td>
                                        <td style="width:5%;">
                                            <?php echo e($attempt->status); ?> </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-2">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Total Completed/Incomplete Attempts</h4>
                </div>
                <div class="card-body p-0" style="min-height: 375px;">
                    <div>
                        <div id="lead-status-tab" role="tabpanel">
                            <div class="text-center uk-vertical-align-middle" id="loader_completePiechart">
                                <div class="spinner-border text-success mt-5 mb-5" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div id="completePiechart"
                                data-colors='["#00e396","#f1b44c",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                class="e-charts"></div>
                        </div><!-- end tabpane -->
                        <!-- end tab pane -->
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end card -->
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Total Fail/Passed</h4>
                </div>
                <div class="card-body p-0" style="min-height: 375px;">
                    <div id="pass-fail-pie" role="tabpanel"
                        style="
                            overflow-y: scroll;
                            height: 370px;
                        ">
                        <div class="text-center uk-vertical-align-middle" id="loader_passFailPieChart">
                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div id="passFailPieChart"
                            data-colors='[ "#00b19d",  "#ff4560", "--vz-warning", "--vz-danger", "--vz-info"]'
                            class="e-charts"></div>
                    </div><!-- end tab pane -->
                    <!-- end tab pane -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end card -->


        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Some Facts</h4>
                </div>
                <div class="card-body" style="min-height: 370px;">

                    <ul class="list-group mt-4" id="factList">

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Overall Results</h4>
                </div>
                <div class="card-body p-0" style="max-height: 370px;">
                    <div>

                        
                        <div id="allAttempts"
                             data-colors='["--vz-primary","#a1a1a1",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                             class="e-charts"></div>

                    <!-- end tab pane -->
                </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0"
                               id="quizHistoryTable">
                            <thead class="table-light text-muted">
                            <tr>
                                <th scope="col">Updated At</th>
                                <th scope="col">Quiz</th>
                                <th scope="col">Time Taken</th>
                                <th scope="col">Details</th>
                                <th scope="col">Total Marks</th>
                                <th scope="col">Result</th>
                                <th scope="col">Status</th>
                                <th scope="col">Attempted At</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->
    </div>
    </div>

    <?php echo $__env->make('admin.quiz.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('assets/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        let leadObj = {};
        const maximum_marks = '<?php echo e($total_marks); ?>';
        const rating = '<?php echo e(number_format($quiz->rating, 2)); ?>';
        let viewAttemptRoute = "<?php echo e(route('admin.quiz.user.attempt.view',':id')); ?>";
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

        function drawPieChart(elementId, data) {

            let chartData = Object.entries(data).map(([name, value]) => ({
                value,
                name
            }));

            var chartPieColors = getChartColorsArray(elementId);
            if (chartPieColors) {
                var chartDom = document.getElementById(elementId);
                var myChart = echarts.init(chartDom);
                var option;

                option = {
                    tooltip: {
                        trigger: 'item'
                    },
                    label: {
                        alignTo: 'edge',
                        formatter: '{name|{b}}\n{time|{c} total}',
                        minMargin: 5,
                        edgeDistance: 10,
                        lineHeight: 15,
                        rich: {
                            time: {
                                fontSize: 10,
                                color: '#999'
                            }
                        }
                    },
                    labelLine: {
                        length: 15,
                        length2: 0,
                        maxSurfaceAngle: 80
                    },

                    color: chartPieColors,
                    series: [{
                        name: 'Total Leads',
                        type: 'pie',
                        radius: '50%',
                        data: chartData,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }],
                    textStyle: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                };

                option && myChart.setOption(option);
                $("#loader_" + elementId).addClass("d-none");
            }
        }

        function getQuizReportData1() {
            $.ajax({
                url: route('admin.quiz.report.data1') + '?quiz_id=<?php echo e($quiz->id); ?>',
                type: "GET",
                success: function(result) {
                    if (result.status == 'success') {
                        let data = {
                            "Completed": result.data.completed,
                            "Incomplete": result.data.unfinished
                        }

                        drawPieChart('completePiechart', data);
                        let data1 = {
                            "Pass": result.data.passed,
                            "Fail": result.data.failed
                        }
                        drawPieChart('passFailPieChart', data1);
                        drawOverallChart(result.data.passed, result.data.failed, result.data.completed, result.data
                            .unfinished);
                    }
                }
            })
        }

        const drawFacts = (data) => {
            let completed_percentage = (data.total_attempt > 0 && data.completed > 0) ? (data.completed / data
                .total_attempt) * 100 : '0';
            let multiple_attempt_percentage = (data.quiz_completed_users > 0 && data.multi_attempt_completed > 0) ? (
                data.multi_attempt_completed / data.quiz_completed_users) * 100 : '0';
            let percentage_passed = (data.quiz_completed_users > 0 && data.passed > 0) ? (data.passed / data
                .quiz_completed_users) * 100 : '0';
            let html = '';
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${data.attempted_user_count}/${data.system_user_count}</b> Employees has been attempted this quiz.</li>`;
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${completed_percentage}%</b> of quiz attempt is completed.</li>`;
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${multiple_attempt_percentage}%</b> Users needed to take the quiz more than once.</li>`;
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${percentage_passed}%</b> Users passed the quiz.</li>`;
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${data.hundred_percent_marks}</b> Users have got 100% marks.</li>`;
            html +=
                `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i>  This quiz has been rated <b>${rating}</b> star.</li>`;
            // for (const [key, value] of Object.entries(data)) {
            //     html += `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${value}</b> ${key}</li>`;
            // }

            $("#factList").append(html);
        }

        function getQuizReportData2() {
            $.ajax({
                url: route('admin.quiz.report.data2') + '?quiz_id=<?php echo e($quiz->id); ?>',
                type: "GET",
                success: function(result) {
                    if (result.status == 'success') {


                        drawFacts(result.data);

                    }
                }
            })
        }

        function drawOverallChart(passed, failed, completed, incompleted) {
            var options = {
          series: [{
          name: 'Passed',
          data: [passed]
        }, {
          name: 'Failed',
          data: [failed]
        }, {
          name: 'Completed',
          data: [completed]
        },
        {

          name: 'Incompleted',
          data: [incompleted]
        }
    ],
    colors: ['#00b19d', '#ff4560','#00e396', '#f1b44c',],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: ['Overall Data'],
        },
        yaxis: {
          title: {
            text: 'Attempts'
          }
        },
       fill: {
    opacity: 1
  },
        tooltip: {
          y: {
            formatter: function (val) {
              return "$ " + val + " thousands"
            }
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#allAttempts"), options);
        chart.render();
        }
        const quizUserHistory = () =>{

            if ($.fn.DataTable.isDataTable('#quizHistoryTable')) {
                $('#quizHistoryTable').DataTable().destroy();
            }
            let dataTable = $('#quizHistoryTable').DataTable({
                responsive: true,
                searching: false,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                order: [
                    [0, "desc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
                <'row'<'col-sm-12'tr>>
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                ajax: {
                    url: "<?php echo e(route('admin.quiz.report.user.attempts')); ?>",
                    type: "post",
                    data: function (d) {
                        d.quiz_id = '<?php echo e($quiz->id); ?>';
                    },
                    error: function () {
                        $(".users-error").html("");
                        $("#dataTable").append(
                            '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                        );
                        $("#dataTable_processing").css("display", "none");
                    }
                },
                "aoColumns": [
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: true

                    },
                    {
                        data: 'id',
                        name: 'id',
                    }, {
                        data: 'time_taken',
                        name: 'time_taken',
                    }, {
                        data: null,
                        orderable: false,
                    }, {
                        data: null,

                    }, {
                        data: null,
                        orderable: false,
                    }, {
                        data: null,
                        orderable: false,
                    }, {
                        data: null,
                        orderable: false,
                    },{
                        data: null,
                        orderable: false,
                    }
                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        orderable: true,
                        "visible": false,
                        "data": "updated_at",
                        render: function (type, row, data, meta) {
                            return data.updated_at;
                        }
                    },
                    {
                        "targets": 1,
                        orderable: false,
                        "data": "id",
                        render: function (type, row, data, meta) {
                            return `<div  class="d-flex">
                                    <img src="${data.user.thumb_url}" alt="" class="avatar-xs rounded-3 me-2">
                            <div>
                                <h5 class="fs-13 mb-0">${data.user.name}</h5>
                                <p class="fs-12 mb-0 text-muted">${data?.user?.role_list[0]}</p>
                            </div></div>`;
                        }
                    },
                    {
                        "targets": 2,
                        orderable: false,
                        "data": "id",
                        render: function (type, row, data, meta) {

                            let start_time = moment.utc(data.created_at);
                            let end_time = data.completed_at !=null ?  moment.utc(data.completed_at) : moment.utc(data.updated_at);
                            let duration = moment.duration(end_time.diff(start_time));
                            return duration.hours() + 'h ' + duration.minutes() + 'm ' + duration.seconds() + 's';
                        }
                    },
                    {
                        "targets": 3,
                        orderable: true,
                        "data": "id",
                        render: function (type, row, data, meta) {
                            return`
                    <div class="row">
                        <div class="col-6">Q. Attempted: <b>${data.answers_count}</b></div>
                        <div class="col-6">Correct Ans: <b>${data.correct_answers}</b></div>
                        <div class="col-6 mt-1">Incorrect Ans: <b>${data.wrong_answers}</b></div>
                        <div class="col-6 mt-1">Skipped: <b>${data.skipped_answers}</b></div>
                        </div>
                    `;
                        }
                    },
                    {
                        "targets": 4,
                        orderable: true,
                        "data": "created_at",
                        render: function (type, row, data, meta) {
                            return data.total_marks;
                        }
                    },
                    {
                        "targets": 5,
                        orderable: true,
                        "data": "id",
                        render: function (type, row, data, meta) {
                            if (data.result === 'pass') {
                                return `<span class="badge border border-passed text-passed " style="border-color: #00b19d !important;">Passed</span>`;
                            } else {
                                return `<span class="badge border border-danger text-danger">Failed</span>`;
                            }
                        }

                    }, {
                        "targets": 6,
                        orderable: true,
                        "data": "id",
                        render: function (type, row, data, meta) {
                            if (data.status === 'completed') {
                                return `<span class="badge bg-success">Completed</span>`;
                            } else {
                                return `<span class="badge bg-warning">In Progress</span>`;
                            }
                        }

                    },
                    {
                        "targets": 7,
                        orderable: true,
                        "data": "id",
                        render: function (type, row, data, meta) {
                            if (data.created_at == null) {
                                return '-';
                            } else {
                                return moment(data.created_at).format('MM/DD/YYYY hh:mm:ss');
                            }
                        }

                    },
                    {
                        targets: 8,
                        orderable: false,
                        visible: true,
                        /*This will hide particular column*/
                        render: function (type, row, data, meta) {
                            let viewAttRoute = viewAttemptRoute.replace(':id', data.id);
                            return `<a href="${viewAttRoute}" title="View Details" class="btn action-edit edit-link" ><i class="ri-eye-line"></i></a>`;
                        }
                    },

                ]

            });
            }
        getQuizReportData1();
        getQuizReportData2();
        quizUserHistory()
        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/quiz/reportView.blade.php ENDPATH**/ ?>