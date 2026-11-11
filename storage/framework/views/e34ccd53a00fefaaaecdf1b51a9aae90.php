<?php $__env->startSection('title', 'Profil Klien - ' . ($customer->user->name ?? 'Customer')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono"><?php echo e($customer->code); ?></span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                <?php echo e($customer->user->name ?? 'Pelanggan'); ?>

            </h1>
            <p class="text-sm text-silver-dim mt-1 font-mono">
                Kode Pelanggan: <span class="text-giallo font-bold"><?php echo e($customer->code); ?></span> • Terdaftar sejak <?php echo e($customer->created_at->format('d M Y')); ?>

            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.customers.edit', $customer)); ?>" class="px-4 py-2 bg-steel/50 hover:bg-giallo hover:text-carbon border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                ✏️ Edit Profil
            </a>
            <a href="<?php echo e(route('admin.customers.vehicles', $customer)); ?>" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2">
                <span>🏎</span> Garasi Mobil
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="panel border border-gunmetal bg-carbon p-6 space-y-6">
            <div class="text-center pb-6 border-b border-gunmetal">
                <div class="w-20 h-20 mx-auto rounded-full bg-steel/60 border-2 border-giallo/40 flex items-center justify-center font-heading text-2xl font-bold text-giallo shadow-[0_0_15px_rgba(245,196,0,0.2)]">
                    <?php echo e(substr($customer->user->name ?? 'C', 0, 1)); ?>

                </div>
                <h3 class="font-heading text-base font-bold text-silver-bright mt-3">
                    <?php echo e($customer->user->name ?? 'Pelanggan'); ?>

                </h3>
                <span class="inline-flex px-2.5 py-0.5 rounded text-[10px] font-mono uppercase bg-giallo/10 border border-giallo/30 text-giallo font-bold mt-1">
                    <?php echo e($customer->code); ?>

                </span>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Alamat Email</span>
                    <span class="text-silver-bright font-medium"><?php echo e($customer->user->email ?? '-'); ?></span>
                </div>
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Nomor Telepon</span>
                    <span class="text-silver-bright font-mono"><?php echo e($customer->user->phone ?? '-'); ?></span>
                </div>
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Nomor KTP</span>
                    <span class="text-silver-bright font-mono"><?php echo e($customer->ktp_number ?: '-'); ?></span>
                </div>
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Tanggal Lahir</span>
                    <span class="text-silver-bright font-mono"><?php echo e($customer->birth_date ? $customer->birth_date->format('d F Y') : '-'); ?></span>
                </div>
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Alamat Lengkap</span>
                    <span class="text-silver-bright"><?php echo e($customer->address ?: '-'); ?></span>
                </div>
                <div>
                    <span class="text-silver-dim text-[10px] uppercase font-heading block">Kota & Provinsi</span>
                    <span class="text-silver-bright"><?php echo e($customer->city ?: '-'); ?>, <?php echo e($customer->province ?: '-'); ?></span>
                </div>
            </div>

            <div class="pt-4 border-t border-gunmetal">
                <span class="text-silver-dim text-[10px] uppercase font-heading block">Loyalty Points VIP</span>
                <p class="font-mono text-2xl font-bold text-giallo mt-1">
                    <?php echo e(number_format($customer->loyalty_points, 0, ',', '.')); ?> <span class="text-xs font-normal text-silver-dim">Pts</span>
                </p>
            </div>
        </div>

        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="panel border border-gunmetal bg-carbon overflow-hidden">
                <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                    <div class="flex items-center gap-2">
                        <span class="text-arancio">🏎</span>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                            Garasi Mobil Pelanggan (<?php echo e($customer->vehicles->count()); ?>)
                        </h2>
                    </div>
                    <a href="<?php echo e(route('admin.customers.vehicles', $customer)); ?>" class="text-[10px] font-heading uppercase text-giallo hover:underline">
                        Buka Manajemen Garasi &rarr;
                    </a>
                </div>
                <div class="p-4 divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $customer->vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-steel/40 border border-gunmetal flex items-center justify-center text-lg">
                                    🏎
                                </div>
                                <div>
                                    <h4 class="font-bold text-silver-bright text-xs"><?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?> (<?php echo e($vehicle->year); ?>)</h4>
                                    <p class="text-[10px] font-mono text-giallo">Plat: <?php echo e($vehicle->plate_number ?: 'Tanpa Plat'); ?> • VIN: <?php echo e($vehicle->vin ?: '-'); ?></p>
                                </div>
                            </div>
                            <span class="font-mono text-xs text-silver-dim"><?php echo e($vehicle->color ?: 'Warna N/A'); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-silver-dim text-center py-6">Klien belum mendaftarkan unit kendaraan.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="panel border border-gunmetal bg-carbon overflow-hidden">
                <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                    <div class="flex items-center gap-2">
                        <span class="text-verde">🔧</span>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                            Riwayat Booking Servis Terkini
                        </h2>
                    </div>
                    <a href="<?php echo e(route('admin.customers.history', $customer)); ?>" class="text-[10px] font-heading uppercase text-giallo hover:underline">
                        Lihat Seluruh Riwayat &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                                <th class="py-2.5 px-4">Layanan</th>
                                <th class="py-2.5 px-4">Jadwal</th>
                                <th class="py-2.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gunmetal/30">
                            <?php $__empty_1 = true; $__currentLoopData = $customer->serviceBookings->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-steel/10 transition-premium">
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-silver-bright uppercase"><?php echo e(str_replace('_', ' ', $booking->service_type)); ?></p>
                                        <p class="text-[10px] font-mono text-silver-dim"><?php echo e($booking->notes ?: 'Tanpa catatan'); ?></p>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-silver-dim">
                                        <?php echo e($booking->preferred_date ? $booking->preferred_date->format('d/m/Y') : '-'); ?> <?php echo e($booking->preferred_time); ?>

                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold <?php echo e($booking->status->value === 'confirmed' ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-steel/40 text-silver-dim'); ?>">
                                            <?php echo e($booking->status->value ?? $booking->status); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-silver-dim">Belum ada riwayat booking servis.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/customers/show.blade.php ENDPATH**/ ?>