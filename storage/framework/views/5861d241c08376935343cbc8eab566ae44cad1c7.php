<?php $__env->startSection('title'); ?>
    Quiz Result View
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
<style>
     .dt-buttons{
            display: none !important;
        }
</style>
    <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="ri-home-5-fill"></i></a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.quiz.list')); ?>">Quiz</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(route('admin.quiz.view',['id'=>$quiz->id])); ?>"><?php echo e($quiz->name); ?></a></li>
    </ol>
</nav>
    <div class="row">
        <div class="col-xl-6">
            <div class="card overflow-hidden" style="height: 183px;">
                <div class="card-body bg-marketplace d-flex">


                    <div class="flex-grow-0"> <img src="<?php echo e($user->thumb_url); ?>"
                                                   alt="" style="height: 150px;" class="img-fluid">
                    </div>

                    <div class="flex-grow-1" style="margin-left: 30px;">
                        <div class="d-flex">


                            <div class="flex-grow-1">
                                <h4 class="fs-18 lh-base mb-0 agent-name"><?php echo e($user->name); ?></h4>
                                <p class="mb-0 pt-1 text-muted"><?php echo e($user->role_list[0]); ?></p>
                            </div>

                            <div class="flex-grow-0" style="margin-left: 10px;">

                            </div>
                        </div>

                        <div class="row mt-2 text-muted">
                            <div class="col-6">Total Quiz Completed: <?php echo e($completed_quiz ?? 0); ?></div>
                            <div class="col-6 d-none">Total Score: <?php echo e($total_score ?? 0); ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <h5 class="text-success">Scored Marks: <?php echo e($attempt->total_marks); ?>/<?php echo e($quiz_total_marks); ?></h5>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">
                Questions Attempted
                                </div>
                                <div>
                                    <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($attempt->answers_count); ?>">0</span></h4>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">
                                    Correct Answers

                                </div>
                                <div>
                                    <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($attempt->correct_answers); ?>">0</span></h4>

                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">
                                  Incorrect Answers

                                </div>
                                <div>
                                    <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($attempt->wrong_answers); ?>">0</span></h4>

                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <div class="col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">
                                   Skipped Answers

                                </div>
                                <div>
                                    <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($attempt->skipped_answers); ?>">0</span></h4>

                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->



            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1 oveflow-hidden">
                        <h5 class="text-muted text-truncates mb-0">Quiz: <?php echo e($quiz->name); ?>.</h5>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#home2" role="tab" aria-selected="true">
                                    Current Quiz
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link"
                                onclick="quizUserHistory()"
                                data-bs-toggle="tab" href="#profile2" role="tab" aria-selected="false" tabindex="-1">
                                    More from this user
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="card-body">



<div class="row">
    <div class="tab-content text-muted">
        <div class="tab-pane active show" id="home2" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <p class="text-muted">
                        Description: <?php echo e($quiz->description); ?>

                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 scrollable-div" style="min-height: 580px;">

                    <div class="accordion" id="default-accordion-example">


                        <?php $__currentLoopData = $questions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qstn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header d-flex justify-content-between align-items-center"
                                    id="questionTabHeading<?php echo e($qstn->id); ?>">
                                    <button
                                        class="accordion-button <?php if($loop->index != 0): ?> collapsed <?php endif; ?>"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#questionTabCollapse<?php echo e($qstn->id); ?>"
                                        <?php if($loop->index == 0): ?> aria-expanded="true"
                                        <?php else: ?> aria-expanded="false"
                                        <?php endif; ?> aria-controls="questionTabCollapse<?php echo e($qstn->id); ?>">
                                        Q.<?php echo e($loop->index+1); ?> <?php echo e($qstn?->question); ?> &nbsp;
                                        <?php
                                            $status = checkQuestionAttemptStatus($qstn,$attempt_answers);

                                            ?>

                                        <?php if('skipped' == $status): ?>
                                        <span class="badge border border-warning text-warning">Skipped</span>

                                        <?php elseif('correct' == $status): ?>
                                        <span class="badge border border-success text-success">Correct</span>
                                        <?php elseif('incorrect' == $status): ?>
                                        <span class="badge border border-danger text-danger">Incorrect</span>
                                        <?php endif; ?>
                                        <span class="float-end">

                                                </span>
                                    </button>
                                </h2>
                                <div id="questionTabCollapse<?php echo e($qstn->id); ?>"
                                     class="accordion-collapse collapse <?php if($loop->index == 0): ?> show <?php endif; ?>"
                                     aria-labelledby="questionTabHeading<?php echo e($qstn->id); ?>"
                                     data-bs-parent="#default-accordion-example">
                                    <div class="accordion-body">

                                        <div class="row">
                                            <?php if($qstn->answers->count() > 0): ?>
                                                <?php $qn = 1; ?>
                                                <?php $__currentLoopData = $qstn->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                $class = checkIfItsCorrect($answers,$attempt_answers,$qstn->id);

                                                ?>
                                                    <div class="col-6 mb-3">
                                                        <?php

//{{$answers->is_correct.'---'.$attempt_answers[$qstn->id]->quiz_q_answer_id.'---'.$answers->id}}
?>
                                                        <div

                                                            class="alert alert-light alert-dismissible
                                                            <?php echo e($class); ?>

                                                            text-body alert-label-icon fade show"
                                                            role="alert">
                                                            <i class="ri-edit-circle-line label-icon"></i>
                                                            <?php echo e($answers->answer); ?>

                                                        </div>
                                                    </div>
                                                    <?php $qn++; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php

                                                    $media_url = $qstn?->image_url != null ? $qstn->image_url : ($qstn->video_url != null ? $qstn->video_url : $qstn->youtube_url);
                                                ?>

                                            <?php endif; ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                </div>
        </div>
        </div>
        <div class="tab-pane" id="profile2" role="tabpanel">
            <div class="table-responsive table-card">
                <table class="table table-borderless table-nowrap align-middle mb-0"
                id="quizHistoryTable"
                >
                    <thead class="table-light text-muted">
                    <tr>
                        <th scope="col">Updated At</th>
                        <th scope="col">Quiz</th>
                        <th scope="col">Time Taken</th>
                        <th scope="col">Details</th>
                        <th scope="col">Total Marks</th>
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

            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
      let viewAttemptRoute = "<?php echo e(route('admin.quiz.user.attempt.view',':id')); ?>";
const quizUserHistory = () =>{
    if ($.fn.DataTable.isDataTable('#quizHistoryTable')) {
        $('#quizHistoryTable').DataTable().destroy();
    }
    let dataTable = $('#quizHistoryTable').DataTable({
        searching: false,
        responsive: true,
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
            url: "<?php echo e(route('admin.quiz.user.attempt.list')); ?>",
            type: "post",
            data: function (d) {

                d.quiz_id = "<?php echo e($quiz->id); ?>";
                d.quiz_user_id = "<?php echo e($user->id); ?>";
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
                    data: 'id',
                    name: 'id',
                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null


                }, {
                    data: null

                }, {
                    data: null
                }, {
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
                    orderable: true,
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
                        console.log(data.created_at+' '+data.completed_at+' '+data.updated_at)
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
                        if (data.status === 'completed') {
                        return `<span class="badge bg-success">Completed</span>`;
                    } else {
                        return `<span class="badge bg-warning">In Progress</span>`;
                    }
                    }

                },
                 {
                    "targets": 6,
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
                    targets: 7,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function (type, row, data, meta) {
                        let viewAttRoute = viewAttemptRoute.replace(':id', data.id);
                    return `<a href="${viewAttRoute}" title="View Details" class="btn action-edit edit-link" ><i class="ri-eye-line"></i></a>`;
                    }
                },

            ]

    }); // end datatable init

}
      $("#sideBarQuiz").addClass('collapse show');
      $("#sidebarQuizActive").addClass('active');
      $("#sidebarDashboard").addClass('active');
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/quiz/resultView.blade.php ENDPATH**/ ?>