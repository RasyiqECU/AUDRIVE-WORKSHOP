<?php $__env->startSection('title', 'Manajemen Unit Mobil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN UNIT MOBIL
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Katalog supercar & mobil premium yang tersedia di showroom dan marketplace AUDrive
            </p>
        </div>
        <a href="<?php echo e(route('admin.cars.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
            <span>+</span> Tambah Unit Mobil
        </a>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="<?php echo e(route('admin.cars.index')); ?>" class="flex flex-wrap items-center gap-3">
            <input type="text"
                   name="search"
                   value="<?php echo e(request('search')); ?>"
                   placeholder="Cari merk, model, atau kode unit..."
                   class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none min-w-[240px]">

            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="available" <?php echo e(request('status') === 'available' ? 'selected' : ''); ?>>Tersedia (Available)</option>
                <option value="reserved" <?php echo e(request('status') === 'reserved' ? 'selected' : ''); ?>>Dipesan (Reserved)</option>
                <option value="sold" <?php echo e(request('status') === 'sold' ? 'selected' : ''); ?>>Terjual (Sold)</option>
                <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="inspection" <?php echo e(request('status') === 'inspection' ? 'selected' : ''); ?>>Inspeksi</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
            <?php if(request()->hasAny(['search', 'status'])): ?>
                <a href="<?php echo e(route('admin.cars.index')); ?>" class="text-xs text-silver-dim hover:text-giallo uppercase tracking-wider">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Unit Mobil</th>
                        <th class="py-3 px-4">Kode & Tahun</th>
                        <th class="py-3 px-4">Harga Beli / Jual</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Featured</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-10 bg-nero border border-gunmetal overflow-hidden rounded-sm flex items-center justify-center shrink-0">
                                        <?php if($car->primaryPhoto): ?>
                                            <img src="<?php echo e(asset('storage/' . $car->primaryPhoto->photo_path)); ?>" alt="<?php echo e($car->model); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-lg">🏎</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-display font-bold text-silver-bright text-sm tracking-wide"><?php echo e($car->brand); ?> <?php echo e($car->model); ?></p>
                                        <p class="font-mono text-[10px] text-silver-dim"><?php echo e($car->transmission); ?> &bull; <?php echo e(number_format($car->mileage_km)); ?> KM &bull; <?php echo e($car->color); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <span class="text-giallo font-semibold"><?php echo e($car->code); ?></span>
                                <span class="block text-silver-dim text-[10px]"><?php echo e($car->year); ?></span>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <p class="text-silver-bright font-semibold">Rp <?php echo e(number_format($car->price_sell_idr, 0, ',', '.')); ?></p>
                                <?php if($car->price_buy_idr): ?>
                                    <p class="text-silver-dim text-[10px]">Beli: Rp <?php echo e(number_format($car->price_buy_idr, 0, ',', '.')); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
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
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($car->featured_at): ?>
                                    <span class="px-2 py-0.5 bg-giallo/20 border border-giallo text-giallo text-[9px] font-heading font-semibold uppercase tracking-wider rounded-sm">Featured</span>
                                <?php else: ?>
                                    <span class="text-silver-dim text-[10px] font-mono">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="<?php echo e(route('admin.cars.show', $car)); ?>" class="px-2.5 py-1 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail
                                </a>
                                <a href="<?php echo e(route('admin.cars.edit', $car)); ?>" class="px-2.5 py-1 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Tidak ada unit mobil yang ditemukan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($cars->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($cars->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/cars/index.blade.php ENDPATH**/ ?>