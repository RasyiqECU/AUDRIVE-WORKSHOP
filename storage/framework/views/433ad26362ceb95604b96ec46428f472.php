<?php $__env->startSection('title', 'Lacak Pengerjaan Servis #' . $workOrder->code . ' — AUDrive'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusHierarchy = [
        'booking'         => 0,
        'queued'          => 1,
        'inspection'      => 1,
        'in_progress'     => 2,
        'waiting_parts'   => 2,
        'quality_control' => 3,
        'completed'       => 4,
        'delivered'       => 5,
    ];

    $currentStatusValue = $workOrder->status instanceof \App\Enums\WorkOrderStatus 
        ? $workOrder->status->value 
        : (string) $workOrder->status;

    $isCancelled = $currentStatusValue === 'cancelled';
    $currentIdx = $statusHierarchy[$currentStatusValue] ?? 1;

    $statusBadgeStyles = [
        'booking'         => 'bg-silver/20 text-silver-bright border-silver/40',
        'queued'          => 'bg-silver/20 text-silver-bright border-silver/40',
        'inspection'      => 'bg-silver/20 text-silver-bright border-silver/40',
        'in_progress'     => 'bg-arancio/20 text-arancio border-arancio/40',
        'waiting_parts'   => 'bg-arancio/20 text-arancio border-arancio/40',
        'quality_control' => 'bg-giallo/20 text-giallo border-giallo/40',
        'completed'       => 'bg-giallo/20 text-giallo border-giallo/40',
        'delivered'       => 'bg-verde/20 text-verde border-verde/40',
        'cancelled'       => 'bg-danger/20 text-danger border-danger/40',
    ];

    $statusLabelIndo = [
        'booking'         => 'Booking Diterima',
        'queued'          => 'Antrean & Inspeksi Masuk',
        'inspection'      => 'Inspeksi Awal',
        'in_progress'     => 'Sedang Dikerjakan',
        'waiting_parts'   => 'Menunggu Suku Cadang',
        'quality_control' => 'Quality Control & Test Drive',
        'completed'       => 'Selesai — Siap Diambil',
        'delivered'       => 'Diserahkan (Selesai Diambil)',
        'cancelled'       => 'Pengerjaan Dibatalkan',
    ];

    $steps = [
        [
            'id'    => 'booking',
            'title' => 'Booking Diterima & Terdaftar',
            'desc'  => 'Permintaan servis kendaraan telah terkonfirmasi dan terdaftar dalam sistem reservasi AUDrive.',
            'time'  => $workOrder->serviceBooking?->created_at ?? $workOrder->created_at,
        ],
        [
            'id'    => 'queued',
            'title' => 'Check-in & Inspeksi Masuk',
            'desc'  => 'Unit tiba di cleanroom workshop. Teknisi melakukan pengecekan visual, pencatatan odometer, dan alokasi stall.',
            'time'  => $statusTimestamps['queued'] ?? $workOrder->created_at,
        ],
        [
            'id'    => 'in_progress',
            'title' => 'Pengerjaan Servis & Perbaikan',
            'desc'  => 'Mekanik spesialis melakukan tindakan servis berkala, tuning performa, atau penggantian suku cadang.',
            'time'  => $workOrder->started_at ?? ($statusTimestamps['in_progress'] ?? null),
        ],
        [
            'id'    => 'quality_control',
            'title' => 'Quality Control & Final Test Drive',
            'desc'  => 'Pemeriksaan kepatuhan standar pabrikan, kalibrasi ECU diagnostik, dan uji performa jalan oleh Kepala Mekanik.',
            'time'  => $statusTimestamps['quality_control'] ?? null,
        ],
        [
            'id'    => 'completed',
            'title' => 'Pengerjaan Selesai & Siap Diambil',
            'desc'  => 'Semua prosedur perbaikan telah rampung dan unit telah dibersihkan. Kendaraan siap diserahkan kepada pemilik.',
            'time'  => $workOrder->completed_at ?? ($statusTimestamps['completed'] ?? null),
        ],
        [
            'id'    => 'delivered',
            'title' => 'Kendaraan Diserahkan / Selesai Diambil',
            'desc'  => 'Kendaraan telah resmi diserahterimakan kembali kepada pemilik. Layanan pengerjaan servis telah selesai.',
            'time'  => $statusTimestamps['delivered'] ?? ($currentStatusValue === 'delivered' ? $workOrder->updated_at : null),
        ],
    ];
?>

<div class="container mx-auto px-4 py-8 max-w-5xl">
    
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('workshop.booking.index')); ?>" class="font-heading text-xs text-giallo hover:underline uppercase tracking-wider">
                    &larr; Daftar Booking
                </a>
                <span class="text-silver-dim/40 text-xs">/</span>
                <a href="<?php echo e(route('workshop.history')); ?>" class="font-heading text-xs text-silver-dim hover:text-silver-bright uppercase tracking-wider">
                    Riwayat Servis
                </a>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2 tracking-wide uppercase">
                PELACAKAN SERVIS: <span class="text-giallo font-mono"><?php echo e($workOrder->code); ?></span>
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Pantau perkembangan tahap pengerjaan unit kendaraan Anda secara transparan dan realtime.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <?php if($workOrder->service_booking_id): ?>
                <a href="<?php echo e(route('workshop.booking.show', $workOrder->service_booking_id)); ?>" 
                   class="px-4 py-2 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    Detail Booking
                </a>
            <?php endif; ?>
            <?php if($workOrder->serviceBooking): ?>
                <a href="<?php echo e(route('workshop.booking.receipt', $workOrder->serviceBooking)); ?>" 
                   class="px-4 py-2 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    Kwitansi
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <?php if($currentStatusValue === 'delivered'): ?>
                <div class="p-5 bg-verde/10 border border-verde/30 rounded-lg flex items-start gap-4 shadow-lg shadow-verde/5">
                    <div class="w-10 h-10 rounded-full bg-verde/20 border border-verde/40 flex items-center justify-center text-verde text-lg shrink-0 mt-0.5">
                        ✓
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-heading text-sm font-bold text-verde uppercase tracking-wider">
                                Kendaraan Telah Resmi Diserahkan / Selesai Diambil
                            </h4>
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-verde text-nero rounded font-mono">
                                DELIVERED
                            </span>
                        </div>
                        <p class="text-xs text-silver-bright leading-relaxed">
                            Unit kendaraan Anda telah diserahterimakan kembali kepada pemilik setelah seluruh tahapan inspeksi, pengerjaan, dan Quality Control diselesaikan dengan standar tertinggi AUDrive.
                        </p>
                        <?php if($statusTimestamps['delivered'] ?? null): ?>
                            <p class="text-[11px] font-mono text-verde pt-1">
                                📅 Waktu Serah Terima: <?php echo e($statusTimestamps['delivered']->translatedFormat('l, d F Y — H:i')); ?> WIB
                            </p>
                        <?php elseif($workOrder->updated_at): ?>
                            <p class="text-[11px] font-mono text-verde pt-1">
                                📅 Waktu Serah Terima: <?php echo e($workOrder->updated_at->translatedFormat('l, d F Y — H:i')); ?> WIB
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif($currentStatusValue === 'completed'): ?>
                <div class="p-5 bg-giallo/10 border border-giallo/40 rounded-lg flex items-start gap-4 shadow-lg shadow-giallo/5">
                    <div class="w-10 h-10 rounded-full bg-giallo/20 border border-giallo/40 flex items-center justify-center text-giallo text-lg shrink-0 mt-0.5">
                        🚗
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-heading text-sm font-bold text-giallo uppercase tracking-wider">
                                Servis Selesai — Kendaraan Siap Diambil
                            </h4>
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-giallo text-nero rounded font-mono">
                                READY FOR PICKUP
                            </span>
                        </div>
                        <p class="text-xs text-silver-bright leading-relaxed">
                            Semua tahapan pengerjaan telah selesai dan unit telah melalui proses detailing serta uji kendali mutu (Quality Control). Anda dapat mengambil unit di Workshop AUDrive atau menghubungi tim kami untuk proses serah terima.
                        </p>
                        <?php if($workOrder->completed_at): ?>
                            <p class="text-[11px] font-mono text-giallo pt-1">
                                ⏱ Selesai Dikerjakan: <?php echo e($workOrder->completed_at->translatedFormat('d F Y — H:i')); ?> WIB
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif($isCancelled): ?>
                <div class="p-5 bg-danger/10 border border-danger/40 rounded-lg flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-danger/20 border border-danger/40 flex items-center justify-center text-danger text-lg shrink-0 mt-0.5">
                        ✕
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-heading text-sm font-bold text-danger uppercase tracking-wider">
                            Work Order Servis Dibatalkan
                        </h4>
                        <p class="text-xs text-silver-dim leading-relaxed">
                            Pengerjaan work order ini telah dibatalkan. Jika Anda membutuhkan informasi lebih lanjut mengenai alasan pembatalan, silakan hubungi tim customer service kami.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-4 bg-steel/30 border border-gunmetal rounded-lg flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-giallo opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-giallo"></span>
                        </span>
                        <div>
                            <span class="font-heading text-xs font-semibold uppercase text-silver-bright tracking-wider">
                                Status Pengerjaan Realtime
                            </span>
                            <p class="text-[11px] text-silver-dim">Unit sedang dalam penanganan tim teknisi spesialis.</p>
                        </div>
                    </div>
                    <span class="font-mono text-xs text-giallo font-semibold uppercase tracking-wider">
                        Tahap <?php echo e(min($currentIdx + 1, 6)); ?> dari 6
                    </span>
                </div>
            <?php endif; ?>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 md:p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gunmetal">
                    <div>
                        <span class="text-[10px] uppercase tracking-[0.2em] text-silver-dim font-heading block">
                            Alur Pengerjaan Unit
                        </span>
                        <h3 class="font-heading text-base font-bold text-silver-bright uppercase tracking-wider mt-0.5">
                            Timeline Pengerjaan Servis
                        </h3>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-heading font-semibold rounded uppercase tracking-wider border <?php echo e($statusBadgeStyles[$currentStatusValue] ?? 'bg-steel text-silver-bright border-gunmetal'); ?>">
                        <?php echo e($statusLabelIndo[$currentStatusValue] ?? ucfirst($currentStatusValue)); ?>

                    </span>
                </div>

                
                <div class="relative space-y-6">
                    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            if ($isCancelled) {
                                $isDone = false;
                                $isActive = false;
                            } else {
                                $isDone = $idx <= $currentIdx;
                                $isActive = ($idx === $currentIdx);
                            }
                            $isDeliveredFinal = ($currentStatusValue === 'delivered' && $idx === 5);
                        ?>

                        <div class="relative flex items-start gap-4 md:gap-6 group">
                            
                            <div class="flex flex-col items-center shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 <?php echo e($isDeliveredFinal 
                                        ? 'bg-verde text-nero ring-4 ring-verde/30 font-extrabold shadow-lg shadow-verde/20' 
                                        : ($isActive 
                                            ? 'bg-giallo text-nero ring-4 ring-giallo/30 font-extrabold shadow-lg shadow-giallo/20' 
                                            : ($isDone 
                                                ? 'bg-verde/20 text-verde border border-verde/40' 
                                                : 'bg-nero border border-gunmetal text-silver-dim/60'))); ?>">
                                    <?php if($isDone && (!$isActive || $isDeliveredFinal)): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <?php echo e($idx + 1); ?>

                                    <?php endif; ?>
                                </div>

                                <?php if(!$loop->last): ?>
                                    <div class="w-0.5 h-12 md:h-14 my-1 transition-colors duration-300 <?php echo e(($idx < $currentIdx && !$isCancelled) ? 'bg-verde/50' : 'bg-gunmetal'); ?>"></div>
                                <?php endif; ?>
                            </div>

                            
                            <div class="pt-1 flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                    <h4 class="font-heading text-sm font-bold tracking-wide <?php echo e($isDeliveredFinal 
                                            ? 'text-verde' 
                                            : ($isActive 
                                                ? 'text-giallo' 
                                                : ($isDone ? 'text-silver-bright' : 'text-silver-dim/60'))); ?>">
                                        <?php echo e($step['title']); ?>

                                    </h4>

                                    
                                    <?php if($isDeliveredFinal): ?>
                                        <span class="px-2 py-0.5 text-[9px] font-heading font-semibold uppercase tracking-wider bg-verde/20 text-verde border border-verde/40 rounded">
                                            ✓ Telah Diserahkan
                                        </span>
                                    <?php elseif($isActive): ?>
                                        <span class="px-2 py-0.5 text-[9px] font-heading font-semibold uppercase tracking-wider bg-giallo/20 text-giallo border border-giallo/40 rounded flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-giallo animate-pulse"></span>
                                            Sedang Berjalan
                                        </span>
                                    <?php elseif($isDone): ?>
                                        <span class="px-2 py-0.5 text-[9px] font-heading font-semibold uppercase tracking-wider bg-verde/10 text-verde/80 rounded">
                                            Selesai
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 text-[9px] font-heading font-semibold uppercase tracking-wider text-silver-dim/50 rounded">
                                            Menunggu
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-xs <?php echo e($isDone ? 'text-silver-dim' : 'text-silver-dim/50'); ?> leading-relaxed">
                                    <?php echo e($step['desc']); ?>

                                </p>

                                
                                <?php if($step['time'] && $isDone): ?>
                                    <div class="mt-2 flex items-center gap-1.5 text-[11px] font-mono <?php echo e($isDeliveredFinal ? 'text-verde' : 'text-silver-dim'); ?>">
                                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span><?php echo e($step['time']->translatedFormat('d M Y, H:i')); ?> WIB</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <?php if($workOrder->items && $workOrder->items->isNotEmpty()): ?>
                <div class="bg-carbon border border-gunmetal rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4 border-b border-gunmetal pb-3">
                        <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider">
                            Rincian Tindakan Servis & Suku Cadang
                        </h3>
                        <span class="text-xs font-mono text-silver-dim">
                            <?php echo e($workOrder->items->count()); ?> Item Terdaftar
                        </span>
                    </div>

                    <div class="divide-y divide-gunmetal/40">
                        <?php $__currentLoopData = $workOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="py-3 flex justify-between items-center text-xs">
                                <div>
                                    <span class="text-silver-bright font-medium block text-sm"><?php echo e($item->item_name); ?></span>
                                    <span class="text-silver-dim text-[11px] font-mono mt-0.5 block">
                                        Tipe: <span class="uppercase text-giallo"><?php echo e($item->item_type ?? 'Jasa'); ?></span> 
                                        &bull; Jumlah: <?php echo e($item->quantity); ?>x
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-silver-bright font-mono font-semibold text-xs">
                                        Rp <?php echo e(number_format($item->subtotal_idr ?? ($item->price_idr * $item->quantity), 0, ',', '.')); ?>

                                    </span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($workOrder->total_idr): ?>
                        <div class="mt-4 pt-4 border-t border-gunmetal flex justify-between items-center text-sm">
                            <span class="font-heading font-bold text-silver-bright uppercase tracking-wider">
                                Total Biaya Pengerjaan:
                            </span>
                            <span class="font-mono font-bold text-giallo text-base">
                                Rp <?php echo e(number_format($workOrder->total_idr, 0, ',', '.')); ?>

                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-6">
            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6">
                <div class="flex items-center justify-between mb-4 border-b border-gunmetal pb-2">
                    <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider">
                        Unit Kendaraan
                    </h3>
                    <span class="text-[10px] font-heading font-semibold text-giallo uppercase tracking-wider">
                        Supercar
                    </span>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-silver-dim block text-[11px] uppercase tracking-wider font-heading">Nomor Polisi</span>
                        <span class="font-mono text-base font-bold text-silver-bright tracking-wider bg-nero px-3 py-1.5 border border-gunmetal inline-block rounded mt-1 uppercase">
                            <?php echo e($workOrder->vehicle?->license_plate ?? 'N/A'); ?>

                        </span>
                    </div>

                    <div>
                        <span class="text-silver-dim block text-[11px] uppercase tracking-wider font-heading">Model Kendaraan</span>
                        <span class="text-silver-bright font-semibold text-sm block mt-0.5">
                            <?php echo e($workOrder->vehicle?->brand); ?> <?php echo e($workOrder->vehicle?->model); ?>

                        </span>
                        <?php if($workOrder->vehicle?->year || $workOrder->vehicle?->color): ?>
                            <span class="text-silver-dim text-[11px] block mt-0.5">
                                <?php echo e($workOrder->vehicle?->year); ?> &bull; Warna: <?php echo e($workOrder->vehicle?->color ?? '-'); ?>

                            </span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <span class="text-silver-dim block text-[11px] uppercase tracking-wider font-heading">Nomor Rangka (VIN)</span>
                        <span class="font-mono text-silver-bright text-xs block mt-0.5 break-all">
                            <?php echo e($workOrder->vehicle?->vin ?? '-'); ?>

                        </span>
                    </div>

                    <?php if($workOrder->mechanic): ?>
                        <div class="pt-3 border-t border-gunmetal">
                            <span class="text-silver-dim block text-[11px] uppercase tracking-wider font-heading">Teknisi Bertugas</span>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-6 h-6 rounded-full bg-steel flex items-center justify-center text-[10px] text-giallo font-bold font-heading">
                                    <?php echo e(substr($workOrder->mechanic->name, 0, 1)); ?>

                                </div>
                                <span class="text-silver-bright font-medium"><?php echo e($workOrder->mechanic->name); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-carbon border border-gunmetal rounded-lg p-6">
                <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal pb-2">
                    Informasi Pengerjaan
                </h3>

                <div class="text-xs space-y-3">
                    <div>
                        <span class="text-silver-dim block text-[11px]">Nomor Work Order (WO)</span>
                        <span class="text-silver-bright font-mono font-bold"><?php echo e($workOrder->code); ?></span>
                    </div>

                    <?php if($workOrder->serviceBooking): ?>
                        <div>
                            <span class="text-silver-dim block text-[11px]">Jenis Layanan</span>
                            <span class="text-silver-bright font-semibold uppercase">
                                <?php echo e(str_replace('_', ' ', $workOrder->serviceBooking->service_type)); ?>

                            </span>
                        </div>
                        <div>
                            <span class="text-silver-dim block text-[11px]">Jadwal Booking Masuk</span>
                            <span class="text-silver-bright">
                                <?php echo e($workOrder->serviceBooking->preferred_date?->translatedFormat('d M Y')); ?> pk <?php echo e($workOrder->serviceBooking->preferred_time); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if($workOrder->notes): ?>
                        <div class="pt-3 border-t border-gunmetal">
                            <span class="text-silver-dim block text-[11px]">Catatan Teknisi / Keluhan</span>
                            <p class="text-silver-bright italic bg-nero p-2.5 rounded border border-gunmetal mt-1 text-[11px]">
                                "<?php echo e($workOrder->notes); ?>"
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-steel/20 border border-gunmetal rounded-lg p-5 text-center space-y-3">
                <span class="text-2xl block">💬</span>
                <h4 class="font-heading text-xs font-bold text-silver-bright uppercase tracking-wider">
                    Butuh Bantuan atau Informasi Tambahan?
                </h4>
                <p class="text-[11px] text-silver-dim leading-relaxed">
                    Concierge dan tim layanan teknis AUDrive siap melayani Anda jika membutuhkan update khusus mengenai unit kendaraan Anda.
                </p>
                <a href="<?php echo e(route('contact')); ?>" class="inline-block w-full py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider transition rounded">
                    Hubungi Concierge
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/workshop/tracking.blade.php ENDPATH**/ ?>