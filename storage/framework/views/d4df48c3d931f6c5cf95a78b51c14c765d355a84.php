<?php $__env->startSection('title'); ?>
    <?php echo e($quiz->name); ?> | Rules
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
<style>
    .list-group-item {
    margin-bottom: 0;
    background: #d3a18882 !important;
    width: 100%;
    font-size: 16px;
    font-weight: 400;
    padding: .7rem 0.5rem !important;
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
    <?php
        $time = $time != 0 ? $time/60 : 0;
        $time = round($time, 2);
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <?php echo e($quiz->name); ?>

                    <br>
                    <p class="text-muted">
                        <?php echo e($quiz->description); ?>

                        <span class="float-end">Passing Marks <span class="badge rounded-pill border border-success text-success "> <?php echo e($quiz_meta?->passing_mark ?? ''); ?></span>
                    </span>
                    </p>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-10 offset-2 scrollable-div" style="min-height: 565px;">
                            <p>Rules:</p>
                            <p><?php echo $quiz->rules; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-group list-group-horizontal-md justify-content-center">
                                <li class="list-group-item">
                                    <i class=" ri-question-line"></i>
                                    Total Question : <b><?php echo e($total_questions); ?></b></li>
                                <li class="list-group-item">
                                    <i class=" ri-time-line"></i>
                                    Time : <b><?php echo e($time); ?>min</b></li>
                                <li class="list-group-item">
                                    <i class=" ri-check-double-line"></i>
                                    Correct Answer marks: <b><?php echo e($quiz_meta->question_mark); ?></b></li>
                                <li class="list-group-item">
                                    <i class="ri-close-line"></i>
                                    Incorrect Answer marks: <b>-<?php echo e($quiz_meta->negative_marks); ?></b></li>
                                <li class="list-group-item">
                                    <i class="ri-spam-line"></i>
                                    Unanswered marks: <b><?php echo e($quiz_meta->skip_mark); ?></b></li>

                            </ul>

                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <p>
                                Note: By clicking on start you have agreed that you have read and accepted the rules of the quiz.
                            </p>
                        </div>
                        <div class="col-12">
                            <a href="<?php echo e(route('admin.quiz.index')); ?>" class="btn btn-primary">Back</a>
                            <button type="submit" onclick="startQuiz()" name="submit" class="btn btn-primary float-end">Start The Quiz</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>


    


    <script>

        let time = "<?php echo e($time ?? 0); ?>";
        let quiz_id = "<?php echo e($quiz->id); ?>";
        <?php if($quiz->trial_quiz_id != null): ?>
            <?php if($attempt == 0 || $attempt == '0'): ?>
            quiz_id = "<?php echo e($quiz->trial_quiz_id); ?>";
                <?php else: ?>
                quiz_id = "<?php echo e($quiz->id); ?>";
                <?php endif; ?>
                    <?php else: ?>
                    quiz_id = "<?php echo e($quiz->id); ?>";
                <?php endif; ?>

        function startQuiz() {

            Swal.fire({

                html: '<div class="mt-3"><img src="<?php echo e(asset('images/animated/question.gif')); ?>" style="width:100px; height:100px;"> <div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to start the quiz this will take '+time+'min ?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, Start!",
                cancelButtonClass: "btn btn-light w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0


            })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        $.ajax({
                            url: "<?php echo e(route('admin.quiz.start')); ?>",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                quiz_id : quiz_id,
                            },
                            success: function(result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Quiz has been started", "success")
                                 let url = result.url;
                                    //redirect to next page
                                   window.location.replace(url);
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
                    }
                })

        }

        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/quiz/rules.blade.php ENDPATH**/ ?>