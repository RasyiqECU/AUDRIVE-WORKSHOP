
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',    // primary, secondary, ghost, danger, success
    'size' => 'md',            // sm, md, lg
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'icon' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'primary',    // primary, secondary, ghost, danger, success
    'size' => 'md',            // sm, md, lg
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'icon' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-heading font-bold uppercase tracking-wider transition-premium focus:outline-none focus:ring-2 focus:ring-giallo/30 disabled:opacity-50 disabled:cursor-not-allowed';


    $variants = [
        'primary'   => 'bg-giallo text-nero hover:bg-arancio hover:text-nero',
        'secondary' => 'border border-giallo text-giallo hover:bg-giallo hover:text-nero',
        'ghost'     => 'border border-gunmetal text-silver hover:border-silver-dim hover:text-silver-bright bg-transparent',
        'danger'    => 'bg-danger/10 border border-danger text-danger hover:bg-danger hover:text-nero',
        'success'   => 'bg-verde/10 border border-verde text-verde hover:bg-verde hover:text-nero',
    ];


    $sizes = [
        'sm' => 'text-[10px] px-3 py-1.5',
        'md' => 'text-xs px-5 py-2.5',
        'lg' => 'text-sm px-7 py-3',
    ];


    $clipClass = 'clip-angular-button';
    $widthClass = $fullWidth ? 'w-full' : '';


    $variantStyle = $variants[$variant] ?? $variants['primary'];
    $sizeStyle = $sizes[$size] ?? $sizes['md'];
    $classes = "{$baseClasses} {$variantStyle} {$sizeStyle} {$clipClass} {$widthClass}";
?>


<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if($icon): ?><span><?php echo e($icon); ?></span><?php endif; ?>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" <?php echo e($disabled ? 'disabled' : ''); ?> <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php if($loading): ?>
            <span class="inline-block w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
        <?php elseif($icon): ?>
            <span><?php echo e($icon); ?></span>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/ui/button.blade.php ENDPATH**/ ?>