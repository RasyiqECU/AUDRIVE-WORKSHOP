
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['car']));

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

foreach (array_filter((['car']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div <?php echo e($attributes->merge(['class' => 'grid grid-cols-2 md:grid-cols-4 gap-3'])); ?>>
    <?php $__currentLoopData = [
        ['Merek', $car->brand],
        ['Model', $car->model],
        ['Tahun', $car->year],
        ['Transmisi', ucfirst($car->transmission)],
        ['Warna', $car->color ?? '-'],
        ['Kilometer', number_format($car->mileage_km, 0, ',', '.') . ' km'],
        ['Lokasi', $car->location ?? '-'],
        ['Bahan Bakar', ucfirst($car->fuel_type ?? '-')],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-steel/20 border border-gunmetal/30 p-3">
            <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mb-1"><?php echo e($label); ?></p>
            <p class="font-heading text-sm text-silver-bright"><?php echo e($value); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/marketplace/car-spec-grid.blade.php ENDPATH**/ ?>