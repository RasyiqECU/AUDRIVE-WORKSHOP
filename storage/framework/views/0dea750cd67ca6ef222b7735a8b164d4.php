<?php $__env->startSection('title', 'Checkout Suku Cadang — AUDrive'); ?>
<?php $__env->startSection('meta_description', 'Penyelesaian pesanan suku cadang dan instruksi pengiriman AUDrive.'); ?>

<?php $__env->startSection('content'); ?>
<section class="container mx-auto px-4 py-10">
    <div class="max-w-5xl mx-auto">
        <div class="mb-8 pb-4 border-b border-gunmetal/40">
            <a href="<?php echo e(route('parts.cart')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider mb-2 inline-block">
                ← Kembali ke Keranjang
            </a>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright">
                CHECKOUT <span class="text-giallo">PESANAN</span>
            </h1>
            <p class="font-body text-xs text-silver-dim mt-1">Lengkapi metode pengiriman dan data penerima suku cadang Anda.</p>
        </div>

        <form action="<?php echo e(route('parts.checkout.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-7 space-y-6">
                    
                    <div class="bg-carbon border border-gunmetal p-6 rounded shadow-xl">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            1. Pilih Metode Pengiriman
                        </h2>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 bg-nero border border-gunmetal/60 rounded cursor-pointer hover:border-giallo/50 transition">
                                <input type="radio" name="shipping_method" value="delivery" checked class="mt-1 text-giallo focus:ring-0"
                                       onchange="document.getElementById('address-section').classList.remove('hidden')">
                                <div>
                                    <span class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider block">Kirim ke Alamat (Delivery Ekspedisi)</span>
                                    <span class="font-body text-xs text-silver-dim">Pengiriman menggunakan kurir resmi dengan proteksi asuransi penuh langsung ke lokasi Anda.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 bg-nero border border-gunmetal/60 rounded cursor-pointer hover:border-giallo/50 transition">
                                <input type="radio" name="shipping_method" value="pickup" class="mt-1 text-giallo focus:ring-0"
                                       onchange="document.getElementById('address-section').classList.add('hidden')">
                                <div>
                                    <span class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider block">Ambil Sendiri di Showroom / Bengkel (Self-Pickup)</span>
                                    <span class="font-body text-xs text-silver-dim">Ambil langsung di Showroom Utama AUDrive Jakarta Utara atau jadwalkan pemasangan langsung di tempat.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    
                    <div id="address-section" class="bg-carbon border border-gunmetal p-6 rounded shadow-xl">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            2. Alamat Lengkap Pengiriman
                        </h2>

                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2 font-semibold">Alamat Penerima / Catatan Kurir</label>
                            <textarea name="shipping_address" rows="4" placeholder="Tuliskan nama penerima, no. HP aktif, nama jalan, nomor rumah/kantor, RT/RW, kelurahan, kecamatan, kota, dan kode pos..."
                                      class="w-full bg-steel/30 border border-gunmetal/60 rounded p-3 text-xs text-silver-bright focus:border-giallo focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-5">
                    <div class="bg-carbon border border-gunmetal p-6 rounded shadow-xl sticky top-24">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            Daftar Barang (<?php echo e($cart->items->count()); ?>)
                        </h2>

                        <?php $subtotal = 0; ?>
                        <div class="space-y-3 mb-6 max-h-60 overflow-y-auto divide-y divide-gunmetal/30 pr-2">
                            <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $itemTotal = ($item->sparePart->price_idr ?? 0) * $item->qty;
                                    $subtotal += $itemTotal;
                                ?>
                                <div class="pt-2 first:pt-0 flex items-center justify-between text-xs">
                                    <div class="pr-2">
                                        <p class="font-heading font-semibold text-silver-bright line-clamp-1"><?php echo e($item->sparePart->name ?? 'Part'); ?></p>
                                        <p class="font-mono text-[10px] text-silver-dim"><?php echo e($item->qty); ?>x @ Rp <?php echo e(number_format($item->sparePart->price_idr ?? 0, 0, ',', '.')); ?></p>
                                    </div>
                                    <span class="font-mono text-silver-bright font-bold whitespace-nowrap">Rp <?php echo e(number_format($itemTotal, 0, ',', '.')); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php
                            $tax = (int)($subtotal * 0.11);
                            $grandTotal = $subtotal + $tax;
                        ?>

                        <div class="space-y-2 pt-4 border-t border-gunmetal/40 font-body text-xs mb-6">
                            <div class="flex justify-between text-silver-dim">
                                <span>Subtotal</span>
                                <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
                            </div>
                            <div class="flex justify-between text-silver-dim">
                                <span>PPN (11%)</span>
                                <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($tax, 0, ',', '.')); ?></span>
                            </div>
                            <div class="pt-3 border-t border-gunmetal/40 flex justify-between items-end">
                                <span class="font-heading font-semibold text-silver-bright uppercase">Total Tagihan</span>
                                <span class="font-mono text-lg font-bold text-giallo">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-lg shadow-giallo/20">
                            Konfirmasi & Buat Tagihan Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/spare-parts/checkout.blade.php ENDPATH**/ ?>