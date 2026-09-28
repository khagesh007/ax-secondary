<aside class="uk-width-1-6@l uk-margin-bottom">
    <div class="uk-text-center">
        <img src="<?php echo e(asset('vendor/totem/img/mask.svg')); ?>" class="uk-svg">
        <div class="uk-text-large">Totem</div>
    </div>
    <hr>
    <ul class="uk-nav uk-nav-default">
        <li class="<?php echo e(Str::contains(url()->current(), 'tasks') ? 'uk-active' : ''); ?>">
            <a href="<?php echo e(route('totem.tasks.all')); ?>" class="uk-flex uk-flex-middle">
                <span uk-icon="icon: clock; ratio: 1" class="uk-visible@m uk-margin-small-right"></span>
                <span class="uk-vertical-align-middle">Tasks</span>
            </a>
        </li>
    </ul>
    <hr>
</aside>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/vendor/totem/partials/sidebar.blade.php ENDPATH**/ ?>