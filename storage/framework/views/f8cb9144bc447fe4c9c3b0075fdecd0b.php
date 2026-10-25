
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'amount' => 0,
    'size' => 'md', // sm, md, lg
    'currency' => 'Rp',
    'strikethrough' => false,
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
    'amount' => 0,
    'size' => 'md', // sm, md, lg
    'currency' => 'Rp',
    'strikethrough' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $sizes = [
        'sm' => 'font-mono text-xs',
        'md' => 'font-mono text-sm',
        'lg' => 'font-display text-xl md:text-2xl font-bold',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $formatted = $currency . ' ' . number_format($amount, 0, ',', '.');
?>


<span <?php echo e($attributes->merge(['class' => "{$sizeClass} text-giallo" . ($strikethrough ? ' line-through text-silver-dim' : '')])); ?>>
    <?php echo e($formatted); ?>

</span>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/ui/price.blade.php ENDPATH**/ ?>