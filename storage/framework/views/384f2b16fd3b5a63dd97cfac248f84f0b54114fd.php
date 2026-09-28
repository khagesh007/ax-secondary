<?php $__env->startSection('title'); ?>
    Quiz
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    
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
        .dt-buttons{
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
            <li class="breadcrumb-item active" aria-current="page">Report Dashboard</li>
        </ol>
    </nav>

    <div class="row card-body row-cols-md-3 row-cols-1">


        <div class="col col-lg bg-white border-end">
            <div class="py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Total Quizzes <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-space-ship-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value"
                                               data-target="<?php echo e($statics['total_quiz']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Total Attempts <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="<?php echo e($statics['total_attempt']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                   COMPLETED <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-pulse-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="<?php echo e($statics['total_completed_attempt']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    PASSED <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-trophy-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="<?php echo e($statics['total_passed_attempt']); ?>">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    FAILED <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-service-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="0">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>







    <div class="row mt-2">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title>">Total Completed/Incomplete Attempts</h5>
                </div>
                <div class="card-body p-0" style="min-height: 375px;">
                    <div>
                        <div  id="lead-status-tab" role="tabpanel">
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
                    <h5 class="card-title>">Total Fail/Passed</h5>
                </div>
                <div class="card-body p-0" style="min-height: 375px;">
                    <div  id="pass-fail-pie" role="tabpanel"
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
                             data-colors='[ "#00b19d",  "#ff4560",  "--vz-warning", "--vz-primary", "--vz-info"]'
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
                <div class="card-body">

                    <ul class="list-group mt-4" id="factList">

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Most Attempted</h4>
                </div>
                <div class="card-body">

                  <table class="table table-stripped">
                    <tr>
                        <th>Quiz</th>
                        <th>Attempts</th>
                    </tr>
                    <tbody id="mostAttemptedQuiz">

                    </tbody>

                  </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title>">Recent Attempts</h4>
                </div>
                <div class="card-body p-0" style="min-height: 375px;">
                    <div>

                            
                            <div id="recentAttempts"
                                 data-colors='["--vz-primary","#a1a1a1",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                 class="e-charts"></div>

                        <!-- end tab pane -->
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end card -->
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
    </div>
    </div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('assets/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>

    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>

        let leadObj = {};
        const maximum_marks = '0';
        const rating = '0';
        let viewAttemptRoute = "<?php echo e(route('admin.quiz.user.attempt.view',':id')); ?>";


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
                            return `<p>${data?.quiz?.name}</p>`;
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

        function getChartColorsArray(chartId) {
            if (document.getElementById(chartId) !== null) {
                var colors = document.getElementById(chartId).getAttribute("data-colors");
                colors = JSON.parse(colors);
                return colors.map(function (value) {
                    var newValue = value.replace(" ", "");
                    if (newValue.indexOf(",") === -1) {
                        var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                        if (color) return color;
                        else return newValue;
                        ;
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
        function drawPieChart(elementId,data) {

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
                $("#loader_"+elementId).addClass("d-none");
            }
        }

        function getQuizReportData1() {
            $.ajax({
                url: route('admin.quiz.report.data1'),
                type: "GET",
                success: function (result) {
                    if (result.status == 'success') {
                        let data = {
                            "Completed" : result.data.completed,
                            "Incomplete" : result.data.unfinished
                        }

                        drawPieChart('completePiechart',data);
                        let data1 = {
                            "Pass" : result.data.passed,
                            "Fail" : result.data.failed
                        }
                        drawPieChart('passFailPieChart',data1);

                    }
                }
            })
        }
        function getQuizReportData2() {
            $.ajax({
                url: route('admin.quiz.report.data2'),
                type: "GET",
                success: function (result) {
                    if (result.status == 'success') {
                        drawFacts(result.data);

                    }
                }
            })
        }
        function getQuizReportData3() {
            $.ajax({
                url: route('admin.quiz.report.data3'),
                type: "GET",
                success: function (result) {
                    if (result.status == 'success') {

                        let html = '';
                        for (const [key, value] of Object.entries(result.data)) {
                            html += `<tr>
                                <td>${value.name}</td>
                                <td>${value.attempt_count}</td>
                            </tr>`;
                        }
                        $("#mostAttemptedQuiz").append(html);

                    }
                }
            })
        }
        getQuizReportData3()
        const drawFacts = (data) => {
            console.log('completed users '+data.quiz_completed_users+' passed '+ data.passed)
            let completed_percentage = (data.total_attempt > 0 && data.completed > 0)  ? (data.completed / data.total_attempt) * 100 : '0';
            let multiple_attempt_percentage = (data.quiz_completed_users > 0 && data.multi_attempt_completed > 0)  ? (data.multi_attempt_completed / data.quiz_completed_users) * 100 : '0';
            let percentage_passed = (data.quiz_completed_users > 0 && data.passed > 0)  ? (data.quiz_completed_users / data.passed) * 100 : '0';
            let not_attempted = data.system_user_count - data.attempted_user_count;
            let html = '';
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${data.attempted_user_count}/${data.system_user_count}</b> Employees has been attempted one or more quiz.</li>`;
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${completed_percentage.toFixed(2)}%</b> of quiz attempt is completed.</li>`;
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${multiple_attempt_percentage}%</b> Users needed to take the quiz more than once.</li>`;
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${percentage_passed.toFixed(2)}%</b> Users passed the quiz.</li>`;
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${data.hundred_percent_marks}</b> Users have got 100% marks.</li>`;
            html+=`<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${not_attempted}</b> Users haven't attempted for any quiz yet.</li>`;

            // for (const [key, value] of Object.entries(data)) {
            //     html += `<li class="list-group-item"><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> <b>${value}</b> ${key}</li>`;
            // }

            $("#factList").append(html);
        }



        const drawRecentAttemptStackBar = (data) => {

            let attempts = [];
            let passed = [];
            let failed = [];
            let incomplete = [];
            let completed = [];
            for (const date in data.attempts) {
               // rawData.push(data.attempts[date]);
                //attempts.push(data.attempts[date][0]);
                passed.push(data.attempts[date][0]);
                failed.push(data.attempts[date][1]);
                incomplete.push(data.attempts[date][2]);
                completed.push(data.attempts[date][3]);
            }

            let options1 = {
                series: [
                //     {
                //     name: 'Attempt',
                //     data: attempts
                // },
                    {
                        color: '#00b19d',
                    name: 'Passed',
                    data: passed
                }, {
                    color: '#ff4560',
                    name: 'Failed',
                    data: failed
                }, {
                    color: '#f1b44c',
                    name: 'Incomplete',
                    data: incomplete
                },{
                    color: '#00e396',
                    name: 'Completed',
                    data: completed
                }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 10,
                        borderRadiusApplication: 'end', // 'around', 'end'
                        borderRadiusWhenStacked: 'last', // 'all', 'last'
                        dataLabels: {
                            total: {
                                enabled: true,
                                style: {
                                    fontSize: '13px',
                                    fontWeight: 900
                                }
                            }
                        }
                    },
                },
                xaxis: {
                    type: 'datetime',
                    categories: data.dates,
                },
                legend: {
                    position: 'right',
                    offsetY: 40
                },
                fill: {
                    opacity: 1
                }
            };

            var chart = new ApexCharts(document.querySelector("#recentAttempts"), options1);
            chart.render();
        }
        function getLast30daysData() {
            $.ajax({
                url: "<?php echo e(route('admin.quiz.report.last30.days')); ?>",
                type: "GET",
                success: function (result) {
                    if (result.status == 'success') {
                        drawRecentAttemptStackBar(result.data);

                    }
                }
            })
        }
        getLast30daysData();

        getQuizReportData1();
        getQuizReportData2();
            quizUserHistory();
        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarDashboard").addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/quiz/dashboard.blade.php ENDPATH**/ ?>