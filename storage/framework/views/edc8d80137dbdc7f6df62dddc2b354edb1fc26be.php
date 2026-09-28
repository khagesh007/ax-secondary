<?php $__env->startSection('page-title'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('page-title'); ?>
    - Task
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <h5 class="uk-card-title uk-margin-remove">Task Details</h5>
        <status-button :data-task="<?php echo e($task); ?>" :data-exists="<?php echo e($task->exists ? 'true' : 'false'); ?>" activate-url="<?php echo e(route('totem.task.activate')); ?>" deactivate-url="<?php echo e(route('totem.task.deactivate', $task)); ?>"></status-button>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-panel-content'); ?>
    <ul class="uk-list uk-list-striped">
        <li>
            <span class="uk-text-muted uk-float-right">Description</span>
            <span class="uk-float-left"><?php echo e(Str::limit($task->description, 80)); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Command</span>
            <span class="uk-float-left"><?php echo e($task->command); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Parameters</span>
            <span class="uk-float-left"><?php echo e($task->parameters ?? "N/A"); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Cron Expression</span>
            <span class="uk-float-left">
                <span><?php echo e($task->getCronExpression()); ?></span>
            </span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Timezone</span>
            <span class="uk-float-left"><?php echo e($task->timezone); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Created At</span>
            <span class="uk-float-left"><?php echo e($task->created_at->toDateTimeString()); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Updated At</span>
            <span class="uk-float-left"><?php echo e($task->updated_at->toDateTimeString()); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Email Notification</span>
            <span class="uk-float-left"><?php echo e($task->notification_email_address ?? 'N/A'); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">SMS Notification</span>
            <span class="uk-float-left"><?php echo e($task->notification_phone_number ?? 'N/A'); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Slack Notification</span>
            <span class="uk-float-left"><?php echo e($task->notification_slack_webhook ?? 'N/A'); ?></span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Average Run Time</span>
            <span class="uk-float-left"><?php echo e($task->results()->count() > 0 ? number_format(  $task->results()->sum('duration') / (1000 * $task->results()->count()) , 2) : '0'); ?> seconds</span>
        </li>
        <li>
            <span class="uk-text-muted uk-float-right">Next Run Schedule</span>
            <span class="uk-float-left"><?php echo e($task->upcoming); ?></span>
        </li>
        <?php if($task->dont_overlap): ?>
            <li>
                <span class="uk-float-left">Doesn't Overlap with another instance of this task</span>
            </li>
        <?php endif; ?>
        <?php if($task->run_in_maintenance): ?>
            <li>
                <span class="uk-float-left">Runs in maintenance mode</span>
            </li>
        <?php endif; ?>
        <?php if($task->run_on_one_server): ?>
            <li>
                <span class="uk-float-left">Runs on a single server</span>
            </li>
        <?php endif; ?>
        <?php if($task->run_in_background): ?>
            <li>
                <span class="uk-float-left">Runs in the background</span>
            </li>
        <?php endif; ?>
    </ul>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-panel-footer'); ?>
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <span>
            <a href="<?php echo e(route('totem.task.edit', $task)); ?>" class="uk-button uk-button-primary uk-button-small">Edit</a>
            <form class="uk-display-inline" action="<?php echo e(route('totem.task.delete', $task)); ?>" method="post">
                <?php echo e(csrf_field()); ?>

                <?php echo e(method_field('delete')); ?>

                <button type="submit" class="uk-button uk-button-danger uk-button-small">Delete</button>
            </form>
            <a href="<?php echo e(route('totem.tasks.all')); ?>" class="uk-button uk-button-secondary uk-button-small">Cancel</a>
        </span>
        <execute-button :data-task="<?php echo e($task); ?>" url="<?php echo e(route('totem.task.execute', $task)); ?>" button-class="uk-button-small uk-button-primary"></execute-button>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('additional-panels'); ?>
    <div class="uk-card uk-card-default uk-margin-top">
        <div class="uk-card-header">
            <h5 class="uk-card-title uk-margin-remove">Execution Results</h5>
        </div>
        <div class="uk-card-body uk-padding-remove-top">
            <table class="uk-table uk-table-striped">
                <thead>
                    <tr>
                        <th>Executed At</th>
                        <th>Duration</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $results = $task->results()->orderByDesc('created_at')->paginate(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($result->ran_at->toDateTimeString()); ?></td>
                        <td><?php echo e(number_format($result->duration / 1000 , 2)); ?> seconds</td>
                        <td>
                            <task-output output="<?php echo e(nl2br($result->result)); ?>"></task-output>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="uk-text-center" colspan="5">
                            <p>Not executed yet.</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="uk-card-footer">
            <?php echo e($results->links('totem::partials.pagination')); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('totem::layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/vendor/totem/tasks/view.blade.php ENDPATH**/ ?>