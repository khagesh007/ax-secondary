<?php if(!checkOAuthTokenExists(auth()->id())): ?>
<div class="row" >
    <div class="col-12">
        <div class="alert alert-danger alert-dismissible alert-label-icon rounded-label shadow fade show"
             role="alert">
            <i class="ri-airplay-line label-icon"></i><strong>Hi <?php echo e(auth()?->user()?->name); ?></strong> - Your CRM Calendar is not synchronized with your Google Calendar. To initiate the synchronization, please click here.
            <a href="<?php echo e(route('oauth.authenticate')); ?>" class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Authorize Now</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/partials/_calender_notification.blade.php ENDPATH**/ ?>