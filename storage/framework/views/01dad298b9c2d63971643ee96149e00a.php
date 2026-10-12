<?php $__env->startSection('title', 'Detail Unit — ' . $car->brand . ' ' . $car->model); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?php echo e(route('admin.cars.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Daftar Unit</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="font-mono text-xs text-silver-dim"><?php echo e($car->code); ?></span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                <?php echo e($car->brand); ?> <?php echo e($car->model); ?> <span class="text-silver-dim text-xl">(<?php echo e($car->year); ?>)</span>
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <?php if($car->featured_at): ?>
                <form action="<?php echo e(route('admin.cars.featured.remove', $car)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-3 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-dim text-xs font-heading uppercase rounded">
                        Hapus Featured
                    </button>
                </form>
            <?php else: ?>
                <form action="<?php echo e(route('admin.cars.featured', $car)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3 py-2 bg-giallo/20 hover:bg-giallo text-giallo hover:text-nero border border-giallo/40 text-xs font-heading uppercase rounded font-bold transition-premium">
                        ⭐ Jadikan Featured
                    </button>
                </form>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.cars.edit', $car)); ?>" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase tracking-wider rounded transition-premium">
                ✏️ Edit
            </a>

            <form action="<?php echo e(route('admin.cars.destroy', $car)); ?>" method="POST" onsubmit="return confirm('Hapus unit mobil ini secara permanen?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-danger/10 hover:bg-danger text-danger hover:text-white border border-danger/30 text-xs font-heading uppercase tracking-wider rounded transition-premium">
                    🗑 Hapus
                </button>
            </form>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="panel border border-gunmetal bg-carbon p-4">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Galeri Foto</h3>
                <?php if($car->photos && $car->photos->count() > 0): ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php $__currentLoopData = $car->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="aspect-video bg-nero border border-gunmetal overflow-hidden rounded">
                                <img src="<?php echo e(asset('storage/' . $photo->photo_path)); ?>" alt="<?php echo e($car->model); ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="p-8 bg-nero border border-gunmetal text-center text-silver-dim text-xs">
                        Belum ada foto yang diunggah untuk unit ini.
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi & Kelengkapan</h3>
                <p class="text-xs text-silver leading-relaxed whitespace-pre-line">
                    <?php echo e($car->description ?: 'Tidak ada deskripsi tambahan.'); ?>

                </p>
            </div>

            
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Penawaran Masuk (<?php echo e($car->offers ? $car->offers->count() : 0); ?>)</h3>
                <?php if($car->offers && $car->offers->count() > 0): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $car->offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-3 bg-steel/20 border border-gunmetal text-xs">
                                <div>
                                    <p class="font-bold text-silver-bright"><?php echo e($offer->customer->user->name ?? 'Customer'); ?></p>
                                    <p class="font-mono text-[10px] text-silver-dim"><?php echo e($offer->created_at->format('d M Y H:i')); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono font-bold text-giallo">Rp <?php echo e(number_format($offer->offered_price_idr, 0, ',', '.')); ?></p>
                                    <span class="text-[10px] uppercase text-silver-dim"><?php echo e($offer->status); ?></span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-silver-dim">Belum ada penawaran harga untuk unit ini.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="space-y-6">
            
            <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
                <div>
                    <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Harga Jual Listing</span>
                    <p class="font-display text-2xl font-bold text-giallo mt-1">
                        Rp <?php echo e(number_format($car->price_sell_idr, 0, ',', '.')); ?>

                    </p>
                    <?php if($car->price_buy_idr): ?>
                        <p class="font-mono text-xs text-silver-dim mt-1">Harga Beli: Rp <?php echo e(number_format($car->price_buy_idr, 0, ',', '.')); ?></p>
                    <?php endif; ?>
                </div>

                <div class="pt-3 border-t border-gunmetal">
                    <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status Unit</span>
                    <div class="mt-2">
                        <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $car->status->value ?? $car->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car->status->value ?? $car->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $attributes = $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $component = $__componentOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
                    </div>
                </div>

                
                <form action="<?php echo e(route('admin.cars.status', $car)); ?>" method="POST" class="pt-3 border-t border-gunmetal space-y-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim">Ubah Status</label>
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 bg-nero border border-gunmetal px-2 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            <option value="available" <?php echo e(($car->status->value ?? $car->status) === 'available' ? 'selected' : ''); ?>>Tersedia</option>
                            <option value="reserved" <?php echo e(($car->status->value ?? $car->status) === 'reserved' ? 'selected' : ''); ?>>Dipesan</option>
                            <option value="sold" <?php echo e(($car->status->value ?? $car->status) === 'sold' ? 'selected' : ''); ?>>Terjual</option>
                            <option value="draft" <?php echo e(($car->status->value ?? $car->status) === 'draft' ? 'selected' : ''); ?>>Draft</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-steel hover:bg-giallo hover:text-nero border border-gunmetal text-xs uppercase font-heading font-semibold transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Spesifikasi Unit</h3>
                <dl class="divide-y divide-gunmetal/40 text-xs font-mono">
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Kode Unit</dt>
                        <dd class="text-silver-bright font-bold"><?php echo e($car->code); ?></dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Merk & Model</dt>
                        <dd class="text-silver-bright"><?php echo e($car->brand); ?> <?php echo e($car->model); ?></dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Tahun</dt>
                        <dd class="text-silver-bright"><?php echo e($car->year); ?></dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Transmisi</dt>
                        <dd class="text-silver-bright"><?php echo e($car->transmission); ?></dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Odometer</dt>
                        <dd class="text-silver-bright"><?php echo e(number_format($car->mileage_km)); ?> KM</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Warna</dt>
                        <dd class="text-silver-bright"><?php echo e($car->color ?: '-'); ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/cars/show.blade.php ENDPATH**/ ?>