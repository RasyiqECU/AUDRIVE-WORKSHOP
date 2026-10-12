
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => '',
    'value' => '',
    'trend' => null,
    'icon' => null,
    'color' => 'giallo', // giallo, arancio, verde, silver
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
    'label' => '',
    'value' => '',
    'trend' => null,
    'icon' => null,
    'color' => 'giallo', // giallo, arancio, verde, silver
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $colors = [
        'giallo' => 'text-giallo',
        'arancio' => 'text-arancio',
        'verde' => 'text-verde',
        'silver' => 'text-silver-bright',
    ];
    $colorClass = $colors[$color] ?? $colors['giallo'];
?>


<div <?php echo e($attributes->merge(['class' => 'bg-steel/30 border border-gunmetal/50 p-4 dashboard-card'])); ?>>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="font-display text-2xl md:text-3xl font-bold <?php echo e($colorClass); ?>"><?php echo e($value); ?></p>
            <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mt-1"><?php echo e($label); ?></p>
            <?php if($trend): ?>
                <p class="font-mono text-[10px] text-verde mt-1"><?php echo e($trend); ?></p>
            <?php endif; ?>
        </div>
        <?php if($icon): ?>
            <span class="text-2xl opacity-40"><?php echo e($icon); ?></span>
        <?php endif; ?>
    </div>
</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/ui/stat-card.blade.php ENDPATH**/ ?>