<?php $__env->startSection('title', 'Keranjang Belanja Spare Part — AUDrive'); ?>
<?php $__env->startSection('meta_description', 'Keranjang belanja suku cadang & aksesori mobil premium AUDrive.'); ?>

<?php $__env->startSection('content'); ?>
<section class="container mx-auto px-4 py-10">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gunmetal/40">
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright">
                    KERANJANG <span class="text-giallo">BELANJA</span>
                </h1>
                <p class="font-body text-xs text-silver-dim mt-1">Kelola daftar suku cadang yang akan Anda pesan.</p>
            </div>
            <a href="<?php echo e(route('parts.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
                ← Lanjut Belanja
            </a>
        </div>

        <?php if($cart->items->count() > 0): ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8">
                    <div class="bg-carbon border border-gunmetal rounded overflow-hidden shadow-xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left font-body text-xs">
                                <thead>
                                    <tr class="bg-steel/30 border-b border-gunmetal font-heading uppercase text-[10px] text-silver-dim">
                                        <th class="py-3 px-4">Produk / Suku Cadang</th>
                                        <th class="py-3 px-4 text-right">Harga</th>
                                        <th class="py-3 px-4 text-center">Jumlah</th>
                                        <th class="py-3 px-4 text-right">Subtotal</th>
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gunmetal/30">
                                    <?php $subtotal = 0; ?>
                                    <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $itemTotal = ($item->sparePart->price_idr ?? 0) * $item->qty;
                                            $subtotal += $itemTotal;
                                        ?>
                                        <tr class="hover:bg-steel/10 transition-premium">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 bg-steel/30 border border-gunmetal rounded flex items-center justify-center text-xl shrink-0 overflow-hidden">
                                                        <?php if($item->sparePart?->image): ?>
                                                            <img src="<?php echo e(asset('storage/' . $item->sparePart->image)); ?>" alt="<?php echo e($item->sparePart->name); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <span>⚙️</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <a href="<?php echo e($item->sparePart ? route('parts.show', $item->sparePart->slug) : '#'); ?>" class="font-heading text-xs font-semibold text-silver-bright hover:text-giallo line-clamp-1">
                                                            <?php echo e($item->sparePart->name ?? 'Part Tidak Ditemukan'); ?>

                                                        </a>
                                                        <p class="font-mono text-[10px] text-silver-dim">SKU: <?php echo e($item->sparePart->sku ?? '-'); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-right font-mono text-silver-bright whitespace-nowrap">
                                                Rp <?php echo e(number_format($item->sparePart->price_idr ?? 0, 0, ',', '.')); ?>

                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <form method="POST" action="<?php echo e(route('parts.cart.update', $item)); ?>" class="inline-flex items-center gap-1">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <input type="number" name="qty" value="<?php echo e($item->qty); ?>" min="1" max="<?php echo e($item->sparePart->stock ?? 99); ?>"
                                                           onchange="this.form.submit()"
                                                           class="w-14 bg-nero border border-gunmetal rounded text-center text-xs text-silver-bright py-1 focus:border-giallo focus:outline-none font-mono" />
                                                </form>
                                            </td>
                                            <td class="py-4 px-4 text-right font-mono font-bold text-giallo whitespace-nowrap">
                                                Rp <?php echo e(number_format($itemTotal, 0, ',', '.')); ?>

                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <form method="POST" action="<?php echo e(route('parts.cart.destroy', $item)); ?>" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" onclick="return confirm('Hapus item ini dari keranjang?')" class="text-silver-dim hover:text-danger text-xs transition p-1">
                                                        ✕
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-4">
                    <div class="bg-carbon border border-gunmetal p-6 rounded shadow-xl sticky top-24">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            Ringkasan Pesanan
                        </h2>

                        <?php
                            $tax = (int)($subtotal * 0.11);
                            $grandTotal = $subtotal + $tax;
                        ?>

                        <div class="space-y-3 font-body text-xs mb-6">
                            <div class="flex justify-between text-silver-dim">
                                <span>Subtotal (<?php echo e($cart->items->sum('qty')); ?> item)</span>
                                <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
                            </div>
                            <div class="flex justify-between text-silver-dim">
                                <span>PPN (11%)</span>
                                <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($tax, 0, ',', '.')); ?></span>
                            </div>
                            <div class="pt-3 border-t border-gunmetal/40 flex justify-between items-end">
                                <span class="font-heading font-semibold text-silver-bright uppercase">Total Pembayaran</span>
                                <span class="font-mono text-lg font-bold text-giallo">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></span>
                            </div>
                        </div>

                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('parts.checkout')).'','variant' => 'primary','size' => 'md','class' => 'w-full text-center justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('parts.checkout')).'','variant' => 'primary','size' => 'md','class' => 'w-full text-center justify-center']); ?>
                            Lanjut ke Pembayaran →
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

                        <div class="mt-4 pt-4 border-t border-gunmetal/30 text-center font-mono text-[10px] text-silver-dim">
                            🔒 Checkout aman & terenkripsi oleh AUDrive Gateway
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="py-16 text-center">
                <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['icon' => '🛒','title' => 'Keranjang Belanja Masih Kosong','description' => 'Anda belum menambahkan suku cadang atau komponen ke dalam keranjang belanja.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '🛒','title' => 'Keranjang Belanja Masih Kosong','description' => 'Anda belum menambahkan suku cadang atau komponen ke dalam keranjang belanja.']); ?>
                    <div class="mt-6">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('parts.index')).'','variant' => 'primary','size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('parts.index')).'','variant' => 'primary','size' => 'md']); ?>
                            Jelajahi Katalog Spare Part
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
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $attributes = $__attributesOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__attributesOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $component = $__componentOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__componentOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/spare-parts/cart.blade.php ENDPATH**/ ?>