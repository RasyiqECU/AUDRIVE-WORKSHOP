
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'info', // success, warning, danger, info, neutral
    'dot' => true,
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
    'type' => 'info', // success, warning, danger, info, neutral
    'dot' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $types = [
        'success' => 'border-verde/40 text-verde bg-verde/5',
        'warning' => 'border-arancio/40 text-arancio bg-arancio/5',
        'danger'  => 'border-danger/40 text-danger bg-danger/5',
        'info'    => 'border-giallo/40 text-giallo bg-giallo/5',
        'neutral' => 'border-silver/20 text-silver-dim bg-steel/30',
    ];
    $dots = [
        'success' => 'bg-verde',
        'warning' => 'bg-arancio',
        'danger'  => 'bg-danger',
        'info'    => 'bg-giallo',
        'neutral' => 'bg-silver-dim',
    ];
    $style = $types[$type] ?? $types['info'];
    $dotColor = $dots[$type] ?? $dots['info'];
?>


<span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 border font-heading font-semibold text-[10px] uppercase tracking-wider {$style}"])); ?>

      style="clip-path: polygon(0 0, calc(100% - 5px) 0, 100% 5px, 100% 100%, 5px 100%, 0 calc(100% - 5px));">
    <?php if($dot): ?>
        <span class="w-1.5 h-1.5 rounded-full <?php echo e($dotColor); ?>"></span>
    <?php endif; ?>
    <?php echo e($slot); ?>

</span>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/ui/badge.blade.php ENDPATH**/ ?>