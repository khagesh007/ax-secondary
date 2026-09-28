<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['url']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['url']); ?>
<?php foreach (array_filter((['url']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<tr>
<td class="header">
    <a href="#" style="display: inline-block;">
        <img src="<?php echo e(asset('assets/images/logo/logo.png')); ?>" class="logo" style="height: 50px;" alt="<?php echo e(getApp('name')); ?>"/>
    </a>
</td>
</tr>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/vendor/mail/html/header.blade.php ENDPATH**/ ?>