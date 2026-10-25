
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => '📦',
    'title' => 'Tidak ada data',
    'description' => null,
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
    'icon' => '📦',
    'title' => 'Tidak ada data',
    'description' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div <?php echo e($attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4 text-center'])); ?>>
    <span class="text-5xl mb-4 opacity-40"><?php echo e($icon); ?></span>
    <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2"><?php echo e($title); ?></h3>
    <?php if($description): ?>
        <p class="font-body text-xs text-silver-dim max-w-md"><?php echo e($description); ?></p>
    <?php endif; ?>
    <?php if(isset($action)): ?>
        <div class="mt-4"><?php echo e($action); ?></div>
    <?php endif; ?>
</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/ui/empty-state.blade.php ENDPATH**/ ?>