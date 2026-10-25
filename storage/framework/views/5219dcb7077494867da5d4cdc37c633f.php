
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'subtitle' => null,
    'hover' => false,
    'padding' => 'md', // sm, md, lg, none
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
    'title' => null,
    'subtitle' => null,
    'hover' => false,
    'padding' => 'md', // sm, md, lg, none
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $paddings = [
        'sm' => 'p-3',
        'md' => 'p-5',
        'lg' => 'p-7',
        'none' => 'p-0',
    ];
    // Perbaikan di baris bawah ini: menambahkan $ pada $paddings dan $padding
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
    $hoverClass = $hover ? 'dashboard-card hover:border-giallo/30 hover:-translate-y-0.5' : '';
?>


<div <?php echo e($attributes->merge(['class' => "bg-carbon border border-gunmetal {$paddingClass} {$hoverClass} transition-premium"])); ?>>
    <?php if($title): ?>
        <div class="mb-4">
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider"><?php echo e($title); ?></h3>
            <?php if($subtitle): ?>
                <p class="font-body text-xs text-silver-dim mt-1"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php echo e($slot); ?>

</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/ui/card.blade.php ENDPATH**/ ?>