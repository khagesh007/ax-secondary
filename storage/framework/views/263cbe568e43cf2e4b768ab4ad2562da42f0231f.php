<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let laravelAssetUrl = "<?php echo e(asset('')); ?>";
</script>

<script src="<?php echo e(URL::asset('assets/libs/bootstrap/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/moment/moment.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>


<script src="<?php echo e(URL::asset('assets/libs/simplebar/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/node-waves/node-waves.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/feather-icons/feather-icons.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/pages/plugins/lord-icon-2.1.0.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/js/plugins.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>?newFile"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-maxlength/1.10.0/bootstrap-maxlength.min.js"
    integrity="sha512-04L+TAgzlDAaUpaEGriEBg/qEryUjw4GNL/FkxA3h621EFPycccO2Y8vNhvid9UhgGC/9+MHLAFwGythpvOAAQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?php echo e(asset('assets/libs/intl-tel-input/js/intlTelInput.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('assets/libs/prismjs/prismjs.min.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="<?php echo e(URL::asset('assets/js/pace.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('/assets/js/routeList.js')); ?>?v=<?php echo e(time()); ?>"></script>
<script src="<?php echo e(asset('assets/js/helpers.js')); ?>?v=<?php echo e(time()); ?>"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.8/push.min.js" crossorigin="anonymous"
    referrerpolicy="no-referrer"></script>
<script>
    const sessionCheckUrl = "<?php echo e(route('check.session')); ?>";
    const ajaxLoginUrl = "<?php echo e(route('ajax.login')); ?>";
    const iconPath = '<?php echo e(asset('/assets/images/logo/favicon-32x32.png')); ?>' //used
    const refreshNotificationUrl = "<?php echo e(route('admin.dashboard.notifications')); ?>";
    const refreshNotificationReadUrl = "<?php echo e(route('admin.message.read.notifications')); ?>";
    const notificationSoundFile = "<?php echo e(asset('/assets/audio/notification-sound.wav')); ?>";
    const globalUser = {
        'id': '<?php echo e(auth()->user()->id); ?>',
        'name': '<?php echo e(auth()->user()->name); ?>',
        'first_name': '<?php echo e(auth()->user()->first_name); ?>',
        'last_name': '<?php echo e(auth()->user()->last_name); ?>'
    };
    const pusherKey = "<?php echo e(config('broadcasting.connections.pusher.key')); ?>";
    const pusherWorker = "<?php echo e(asset('assets/js/pusher/shared_worker.js')); ?>";
    const commonNoDataIcon = "<?php echo e(asset('assets/json/icons/common-event.json')); ?>";
</script>
<script src="<?php echo e(URL::asset('/assets/js/sessionChecker.js')); ?>"></script>

<script src="<?php echo e(URL::asset('/assets/js/plugins/notifications.js')); ?>?v=2"></script>


<script>






    $('.select2').select2();
    $('.select2[multiple=""]').select2({
        closeOnSelect: false
    });
    getNotifications(); //calling notification for all pages on page load

    function checkUnique(value, column, model, id = "null") {
        $(`#${column}`).removeClass("is-invalid")
        $(`#${column}`).next(".invalid-feedback").text(`enter a valid ${column}.`);
        if (value.length > 0) {
            $.ajax({
                url: "<?php echo e(route('ajax.is-value-unique')); ?>",
                type: "get",
                data: {
                    value,
                    column,
                    model,
                    id
                },
                success: function(result) {
                    if (!result.data) {
                        $(`#${column}`).addClass("is-invalid")
                        $(`#${column}`).next(".invalid-feedback").text(
                            `The ${column} has already been taken.`)
                        showToastify(`The ${column} has already been taken.`, "error")
                    }
                }
            });
        }

    }






</script>
<?php echo $__env->yieldContent('script'); ?>
<?php echo $__env->yieldPushContent('partial-scripts'); ?>
<?php echo $__env->yieldContent('script-bottom'); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/layouts/vendor-scripts.blade.php ENDPATH**/ ?>