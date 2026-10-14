<?php $__env->startSection('title', $sparePart->name . ' — Spare Part AUDrive'); ?>
<?php $__env->startSection('meta_description', 'Beli ' . $sparePart->name . ' (' . $sparePart->sku . ') di AUDrive. Kualitas orisinal, garansi resmi, dan kompatibel dengan berbagai kendaraan premium.'); ?>

<?php $__env->startSection('content'); ?>

<div class="bg-carbon border-b border-gunmetal/40 py-3">
    <div class="container mx-auto px-4 flex items-center justify-between text-xs">
        <div class="flex items-center gap-2 font-mono text-silver-dim">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-giallo">Home</a>
            <span>/</span>
            <a href="<?php echo e(route('parts.index')); ?>" class="hover:text-giallo">Spare Parts</a>
            <span>/</span>
            <?php if($sparePart->category): ?>
                <a href="<?php echo e(route('parts.category', $sparePart->category->slug)); ?>" class="hover:text-giallo"><?php echo e($sparePart->category->name); ?></a>
                <span>/</span>
            <?php endif; ?>
            <span class="text-silver-bright truncate max-w-[200px] sm:max-w-xs"><?php echo e($sparePart->name); ?></span>
        </div>

        <a href="<?php echo e(route('parts.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider flex items-center gap-1">
            ← Kembali ke Katalog
        </a>
    </div>
</div>


<section class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-5">
            <div class="bg-carbon border border-gunmetal rounded overflow-hidden sticky top-24 shadow-xl">
                <div class="relative aspect-square bg-steel/30 flex items-center justify-center p-6">
                    <?php if($sparePart->image): ?>
                        <img src="<?php echo e(asset('storage/' . $sparePart->image)); ?>" alt="<?php echo e($sparePart->name); ?>"
                             class="max-h-full max-w-full object-contain" />
                    <?php else: ?>
                        <div class="text-center text-gunmetal">
                            <span class="text-8xl block mb-2">⚙️</span>
                            <span class="font-mono text-xs text-silver-dim uppercase">AUDrive Certified Genuine Part</span>
                        </div>
                    <?php endif; ?>

                    <?php if($sparePart->isLowStock()): ?>
                        <div class="absolute top-4 right-4">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?>Stok Terbatas (Tersisa <?php echo e($sparePart->stock); ?>) <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        </div>
                    <?php elseif($sparePart->stock <= 0): ?>
                        <div class="absolute top-4 right-4">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger']); ?>Stok Habis <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="absolute top-4 right-4">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success']); ?>Tersedia (Ready Stock) <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="p-4 bg-nero/50 border-t border-gunmetal/40 grid grid-cols-2 gap-3 font-mono text-[11px]">
                    <div>
                        <span class="text-silver-dim block">Kode SKU:</span>
                        <span class="text-giallo font-bold"><?php echo e($sparePart->sku); ?></span>
                    </div>
                    <div>
                        <span class="text-silver-dim block">Merek / Manufaktur:</span>
                        <span class="text-silver-bright font-semibold"><?php echo e($sparePart->brand ?? 'OEM Original'); ?></span>
                    </div>
                    <?php if($sparePart->weight_kg): ?>
                        <div>
                            <span class="text-silver-dim block">Berat Pengiriman:</span>
                            <span class="text-silver-bright"><?php echo e(number_format((float)$sparePart->weight_kg, 2)); ?> kg</span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-silver-dim block">Status:</span>
                        <span class="text-verde font-semibold"><?php echo e(strtoupper($sparePart->status->value)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                <div class="flex items-center gap-2 mb-2">
                    <?php if($sparePart->category): ?>
                        <a href="<?php echo e(route('parts.category', $sparePart->category->slug)); ?>" class="font-heading text-[11px] uppercase tracking-wider text-giallo bg-giallo/10 border border-giallo/30 px-2 py-0.5 rounded">
                            <?php echo e($sparePart->category->name); ?>

                        </a>
                    <?php endif; ?>
                    <span class="font-mono text-xs text-silver-dim">SKU: <?php echo e($sparePart->sku); ?></span>
                </div>

                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mb-4">
                    <?php echo e($sparePart->name); ?>

                </h1>

                
                <div class="bg-nero border border-gunmetal/60 p-4 rounded mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] text-silver-dim uppercase block">Harga Satuan (Inc. PPN)</span>
                        <span class="font-mono text-2xl md:text-3xl font-bold text-giallo">
                            Rp <?php echo e(number_format($sparePart->price_idr, 0, ',', '.')); ?>

                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-xs block text-silver-dim">Ketersediaan Gudang:</span>
                        <span class="font-mono text-sm font-bold <?php echo e($sparePart->stock > 0 ? 'text-verde' : 'text-danger'); ?>">
                            <?php echo e($sparePart->stock > 0 ? $sparePart->stock . ' Unit Siap Kirim' : 'Habis (Pre-Order via Konsultan)'); ?>

                        </span>
                    </div>
                </div>

                
                <?php if($sparePart->stock > 0): ?>
                    <form action="<?php echo e(route('parts.cart.store')); ?>" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="spare_part_id" value="<?php echo e($sparePart->id); ?>">

                        <div class="flex flex-wrap items-center gap-4">
                            <div class="w-32">
                                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1 font-semibold">Jumlah (Qty)</label>
                                <div class="flex items-center border border-gunmetal rounded bg-nero">
                                    <button type="button" onclick="const q = document.getElementById('qty'); if(q.value > 1) q.value--;" class="px-3 py-2 text-silver hover:text-giallo focus:outline-none">-</button>
                                    <input type="number" id="qty" name="qty" value="1" min="1" max="<?php echo e($sparePart->stock); ?>"
                                           class="w-full bg-transparent text-center text-xs text-silver-bright font-mono focus:outline-none py-2" />
                                    <button type="button" onclick="const q = document.getElementById('qty'); if(q.value < <?php echo e($sparePart->stock); ?>) q.value++;" class="px-3 py-2 text-silver hover:text-giallo focus:outline-none">+</button>
                                </div>
                            </div>

                            <div class="flex-1 min-w-[200px] pt-4">
                                <button type="submit" class="w-full py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-lg shadow-giallo/20 flex items-center justify-center gap-2">
                                    <span>🛒 Masukkan ke Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="p-4 bg-danger/10 border border-danger/30 rounded text-center">
                        <p class="font-heading text-xs text-danger uppercase font-semibold mb-2">Stok Saat Ini Sedang Habis</p>
                        <p class="font-body text-xs text-silver-dim mb-3">Hubungi konsultan kami untuk pemesanan khusus / indent komponen langka.</p>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('contact')).'','variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('contact')).'','variant' => 'secondary','size' => 'sm']); ?>
                            Ajukan Permintaan Part (Indent)
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
                    </div>
                <?php endif; ?>

                
                <div class="mt-6 pt-4 border-t border-gunmetal/40 flex flex-wrap gap-3">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('workshop.index')).'','variant' => 'ghost','size' => 'sm','class' => 'flex-1 text-center justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('workshop.index')).'','variant' => 'ghost','size' => 'sm','class' => 'flex-1 text-center justify-center']); ?>
                        🔧 Booking Pemasangan di Bengkel
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
                    <a href="https://wa.me/6281122334455?text=Halo%20AUDrive,%20saya%20ingin%20tanya%20tentang%20spare%20part%20<?php echo e(urlencode($sparePart->name)); ?>%20(SKU:%20<?php echo e($sparePart->sku); ?>)" target="_blank"
                       class="px-4 py-2 bg-verde/10 hover:bg-verde/20 border border-verde/30 text-verde font-heading text-xs uppercase tracking-wider rounded transition flex items-center gap-2">
                        <span>💬 Konsultasi WhatsApp</span>
                    </a>
                </div>
            </div>

            
            <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                <h2 class="font-display text-base font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                    Deskripsi & Spesifikasi Produk
                </h2>
                <div class="font-body text-xs md:text-sm text-silver-dim leading-relaxed whitespace-pre-line space-y-3">
                    <?php if($sparePart->description): ?>
                        <?php echo e($sparePart->description); ?>

                    <?php else: ?>
                        <p>Komponen pengganti resmi dengan standar presisi tinggi, memastikan performa maksimal serta daya tahan optimum bagi kendaraan premium Anda.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($sparePart->compatibilities->count() > 0): ?>
                <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                    <h2 class="font-display text-base font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center justify-between">
                        <span>Daftar Kompatibilitas Kendaraan</span>
                        <span class="font-mono text-xs text-giallo font-normal"><?php echo e($sparePart->compatibilities->count()); ?> Model Terverifikasi</span>
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body text-xs">
                            <thead>
                                <tr class="border-b border-gunmetal text-silver-dim font-heading uppercase text-[10px]">
                                    <th class="py-2.5 px-3">Merek Mobil</th>
                                    <th class="py-2.5 px-3">Model / Tipe</th>
                                    <th class="py-2.5 px-3 text-right">Rentang Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gunmetal/30">
                                <?php $__currentLoopData = $sparePart->compatibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-steel/10">
                                        <td class="py-2.5 px-3 font-heading font-semibold text-silver-bright"><?php echo e($comp->vehicle_brand); ?></td>
                                        <td class="py-2.5 px-3 text-silver"><?php echo e($comp->vehicle_model); ?></td>
                                        <td class="py-2.5 px-3 text-right font-mono text-giallo"><?php echo e($comp->year_from); ?> - <?php echo e($comp->year_to); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if(isset($relatedParts) && $relatedParts->count() > 0): ?>
        <div class="mt-16 pt-8 border-t border-gunmetal/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-display text-xl font-bold text-silver-bright">PRODUK TERKAIT LAINNYA</h2>
                    <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-0.5">Suku cadang dalam kategori serupa</p>
                </div>
                <a href="<?php echo e(route('parts.index')); ?>" class="font-heading text-xs text-giallo hover:underline uppercase tracking-wider">
                    Lihat Semua Part →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php $__currentLoopData = $relatedParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relPart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginalb98de063c94d934e5bb57e4284c5a4e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb98de063c94d934e5bb57e4284c5a4e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.parts.part-card','data' => ['part' => $relPart]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('parts.part-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['part' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($relPart)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb98de063c94d934e5bb57e4284c5a4e9)): ?>
<?php $attributes = $__attributesOriginalb98de063c94d934e5bb57e4284c5a4e9; ?>
<?php unset($__attributesOriginalb98de063c94d934e5bb57e4284c5a4e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb98de063c94d934e5bb57e4284c5a4e9)): ?>
<?php $component = $__componentOriginalb98de063c94d934e5bb57e4284c5a4e9; ?>
<?php unset($__componentOriginalb98de063c94d934e5bb57e4284c5a4e9); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/spare-parts/show.blade.php ENDPATH**/ ?>