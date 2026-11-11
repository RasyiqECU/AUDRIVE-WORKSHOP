
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

<form method="POST" action="<?php echo e(route('marketplace.offer.store', $car->slug)); ?>" <?php echo e($attributes->merge(['class' => 'space-y-4'])); ?>>
    <?php echo csrf_field(); ?>

    <div class="bg-steel/30 p-3.5 rounded border border-gunmetal/60 space-y-1">
        <span class="text-silver-dim text-[10px] font-heading uppercase tracking-wider block">Total Harga Pembelian</span>
        <div class="text-xl font-display font-bold text-giallo">
            <?php echo e($car->formatted_price); ?>

        </div>
        <p class="text-[11px] text-silver-dim">
            Harga On The Road (OTR) resmi AUDrive dengan garansi 150+ titik inspeksi.
        </p>
    </div>

    <div>
        <label for="order-note" class="block font-heading text-xs uppercase text-silver-dim tracking-wider mb-1">
            Catatan / Preferensi Pembeli (Opsional)
        </label>
        <textarea id="order-note"
                  name="note"
                  rows="3"
                  placeholder="Contoh: Rencana bayar cash / transfer, perkiraan jadwal pelunasan atau pengambilan unit..."
                  class="w-full bg-steel/40 border border-gunmetal rounded p-2.5 text-xs text-silver-bright placeholder:text-silver-dim/50 focus:border-giallo focus:outline-none transition"></textarea>
    </div>

    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','fullWidth' => true,'class' => 'py-3 text-xs tracking-wider uppercase font-bold flex items-center justify-center gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','fullWidth' => true,'class' => 'py-3 text-xs tracking-wider uppercase font-bold flex items-center justify-center gap-2']); ?>
        <span>⚡</span> Beli / Pesan Mobil Sekarang
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

    <p class="text-[10px] text-silver-dim text-center leading-normal">
        Unit akan langsung berstatus <strong class="text-arancio font-semibold">Dipesan (Reserved)</strong> untuk Anda setelah pesanan dikirim.
    </p>
</form>
<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/marketplace/offer-form.blade.php ENDPATH**/ ?>