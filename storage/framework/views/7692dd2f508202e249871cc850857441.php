<?php $__env->startSection('title', $car->full_name . ' — AUDrive Marketplace'); ?>
<?php $__env->startSection('meta_description', 'Beli ' . $car->full_name . ' dengan garansi resmi dan inspeksi 150+ titik di AUDrive. Harga terbaik ' . $car->formatted_price); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    
    <nav class="flex items-center gap-2 text-xs text-silver-dim mb-6 font-heading">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-silver-bright transition">Home</a>
        <span>/</span>
        <a href="<?php echo e(route('marketplace.index')); ?>" class="hover:text-silver-bright transition">Marketplace</a>
        <span>/</span>
        <span class="text-giallo font-semibold"><?php echo e($car->full_name); ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-carbon border border-gunmetal rounded-lg overflow-hidden dashboard-card">
                <div class="relative aspect-[16/10] bg-steel/30 overflow-hidden">
                    <?php if($car->primaryPhoto): ?>
                        <img id="main-car-photo" src="<?php echo e(asset('storage/' . $car->primaryPhoto->path)); ?>" alt="<?php echo e($car->full_name); ?>" class="w-full h-full object-cover" />
                    <?php elseif($car->photos->isNotEmpty()): ?>
                        <img id="main-car-photo" src="<?php echo e(asset('storage/' . $car->photos->first()->path)); ?>" alt="<?php echo e($car->full_name); ?>" class="w-full h-full object-cover" />
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gunmetal">
                            <span class="text-7xl">🏎</span>
                        </div>
                    <?php endif; ?>

                    <div class="absolute top-4 left-4">
                        <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $car->status->value]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car->status->value)]); ?>
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

                    <?php if($car->featured_at): ?>
                        <div class="absolute top-4 right-4">
                            <span class="bg-giallo text-nero font-heading text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-sm shadow">
                                ★ Pilihan Terbaik
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                
                <?php if($car->photos->count() > 1): ?>
                    <div class="p-3 bg-steel/20 border-t border-gunmetal flex gap-2 overflow-x-auto">
                        <?php $__currentLoopData = $car->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" onclick="document.getElementById('main-car-photo').src='<?php echo e(asset('storage/' . $photo->path)); ?>'"
                                    class="w-16 h-12 flex-shrink-0 border border-gunmetal hover:border-giallo rounded overflow-hidden transition">
                                <img src="<?php echo e(asset('storage/' . $photo->path)); ?>" class="w-full h-full object-cover" alt="Foto unit" />
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h2 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">⚡</span> Spesifikasi Teknis
                </h2>
                <?php if (isset($component)) { $__componentOriginalace7119ea87ade4fbe55dab00c39691e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalace7119ea87ade4fbe55dab00c39691e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketplace.car-spec-grid','data' => ['car' => $car]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketplace.car-spec-grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['car' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalace7119ea87ade4fbe55dab00c39691e)): ?>
<?php $attributes = $__attributesOriginalace7119ea87ade4fbe55dab00c39691e; ?>
<?php unset($__attributesOriginalace7119ea87ade4fbe55dab00c39691e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalace7119ea87ade4fbe55dab00c39691e)): ?>
<?php $component = $__componentOriginalace7119ea87ade4fbe55dab00c39691e; ?>
<?php unset($__componentOriginalace7119ea87ade4fbe55dab00c39691e); ?>
<?php endif; ?>
            </div>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h2 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                    Deskripsi Kendaraan
                </h2>
                <div class="text-silver-dim text-xs md:text-sm leading-relaxed whitespace-pre-line font-body">
                    <?php echo e($car->description ?? 'Unit mobil bekas berkualitas tinggi dengan rekam jejak servis berkala rutin. Interior dan eksterior dalam kondisi sangat terawat.'); ?>

                </div>

                
                <div class="mt-6 pt-4 border-t border-gunmetal/40 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">BPKB</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block"><?php echo e($car->bpkb_status ?? 'Lengkap & Asli'); ?></span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">STNK</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block"><?php echo e($car->stnk_status ?? 'Pajak Hidup'); ?></span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">Garansi</span>
                        <span class="text-giallo font-semibold mt-0.5 block"><?php echo e($car->warranty_status ?? 'Garansi 30 Hari'); ?></span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">Stok</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block"><?php echo e($car->stock_status ?? 'Ready Unit'); ?></span>
                    </div>
                </div>
            </div>

            
            <?php if($car->latestInspection): ?>
                <div>
                    <?php if (isset($component)) { $__componentOriginal967e3f0bad7ad4d67c605b5674b20656 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal967e3f0bad7ad4d67c605b5674b20656 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketplace.inspection-panel','data' => ['inspection' => $car->latestInspection]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketplace.inspection-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['inspection' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car->latestInspection)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal967e3f0bad7ad4d67c605b5674b20656)): ?>
<?php $attributes = $__attributesOriginal967e3f0bad7ad4d67c605b5674b20656; ?>
<?php unset($__attributesOriginal967e3f0bad7ad4d67c605b5674b20656); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal967e3f0bad7ad4d67c605b5674b20656)): ?>
<?php $component = $__componentOriginal967e3f0bad7ad4d67c605b5674b20656; ?>
<?php unset($__componentOriginal967e3f0bad7ad4d67c605b5674b20656); ?>
<?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-6">
            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <span class="text-silver-dim text-[11px] font-heading uppercase tracking-wider block">Harga Penjualan (OTR)</span>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="font-display text-3xl font-bold text-giallo">
                        <?php echo e($car->formatted_price); ?>

                    </span>
                </div>
                <div class="mt-3 text-xs text-silver-dim">
                    Kode Unit: <span class="font-mono text-silver-bright font-semibold"><?php echo e($car->code); ?></span>
                </div>

                
                <div class="mt-6 pt-4 border-t border-gunmetal/40 space-y-2">
                    <a href="https://wa.me/6281234567890?text=Halo%20AUDrive,%20saya%20tertarik%20dengan%20unit%20<?php echo e(urlencode($car->full_name)); ?>%20(Kode:%20<?php echo e($car->code); ?>)"
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#25D366] hover:bg-[#20ba5a] text-white font-heading font-semibold text-xs uppercase tracking-wider rounded transition">
                        💬 Hubungi via WhatsApp
                    </a>
                </div>
            </div>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h3 class="font-heading text-xs font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">🚗</span> Jadwalkan Test Drive
                </h3>
                <?php if(auth()->guard()->check()): ?>
                    <?php if (isset($component)) { $__componentOriginalb5894777f831183beee67d47d0ba3350 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5894777f831183beee67d47d0ba3350 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketplace.test-drive-form','data' => ['car' => $car]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketplace.test-drive-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['car' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5894777f831183beee67d47d0ba3350)): ?>
<?php $attributes = $__attributesOriginalb5894777f831183beee67d47d0ba3350; ?>
<?php unset($__attributesOriginalb5894777f831183beee67d47d0ba3350); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5894777f831183beee67d47d0ba3350)): ?>
<?php $component = $__componentOriginalb5894777f831183beee67d47d0ba3350; ?>
<?php unset($__componentOriginalb5894777f831183beee67d47d0ba3350); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-xs text-silver-dim space-y-3">
                        <p>Silakan login terlebih dahulu untuk mengajukan jadwal test drive unit ini.</p>
                        <a href="<?php echo e(route('login')); ?>" class="inline-block px-5 py-2 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded hover:bg-giallo/90 transition">
                            Login Akun
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h3 class="font-heading text-xs font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">🏷️</span> Ajukan Penawaran Harga
                </h3>
                <?php if(auth()->guard()->check()): ?>
                    <?php if (isset($component)) { $__componentOriginalc83155eb7d67cb1551163612a3d24390 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc83155eb7d67cb1551163612a3d24390 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketplace.offer-form','data' => ['car' => $car]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketplace.offer-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['car' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc83155eb7d67cb1551163612a3d24390)): ?>
<?php $attributes = $__attributesOriginalc83155eb7d67cb1551163612a3d24390; ?>
<?php unset($__attributesOriginalc83155eb7d67cb1551163612a3d24390); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc83155eb7d67cb1551163612a3d24390)): ?>
<?php $component = $__componentOriginalc83155eb7d67cb1551163612a3d24390; ?>
<?php unset($__componentOriginalc83155eb7d67cb1551163612a3d24390); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-xs text-silver-dim space-y-3">
                        <p>Silakan login untuk mengirimkan penawaran harga kepada sales representatif.</p>
                        <a href="<?php echo e(route('login')); ?>" class="inline-block px-5 py-2 border border-white/20 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded hover:border-giallo transition">
                            Login Akun
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if(isset($relatedCars) && $relatedCars->isNotEmpty()): ?>
        <div class="mt-16 pt-10 border-t border-gunmetal/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-display text-2xl font-bold text-silver-bright">UNIT REKOMENDASI LAINNYA</h2>
                    <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Koleksi sejenis dari merek <?php echo e($car->brand); ?></p>
                </div>
                <a href="<?php echo e(route('marketplace.index', ['brand' => $car->brand])); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition">
                    Lihat Semua <?php echo e($car->brand); ?> →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php $__currentLoopData = $relatedCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relCar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginalb0f8c0afc770c8b627ddeaef2c77d2d1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb0f8c0afc770c8b627ddeaef2c77d2d1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketplace.car-card','data' => ['car' => $relCar]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketplace.car-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['car' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($relCar)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb0f8c0afc770c8b627ddeaef2c77d2d1)): ?>
<?php $attributes = $__attributesOriginalb0f8c0afc770c8b627ddeaef2c77d2d1; ?>
<?php unset($__attributesOriginalb0f8c0afc770c8b627ddeaef2c77d2d1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb0f8c0afc770c8b627ddeaef2c77d2d1)): ?>
<?php $component = $__componentOriginalb0f8c0afc770c8b627ddeaef2c77d2d1; ?>
<?php unset($__componentOriginalb0f8c0afc770c8b627ddeaef2c77d2d1); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/marketplace/show.blade.php ENDPATH**/ ?>