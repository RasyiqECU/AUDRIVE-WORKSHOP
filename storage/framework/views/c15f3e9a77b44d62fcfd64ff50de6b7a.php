


<?php $__env->startSection('title', 'Dashboard'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="relative overflow-hidden border border-gunmetal bg-carbon p-6 md:p-8">
        <img src="<?php echo e(\App\Services\SettingService::getDashboardBackground()); ?>" 
             alt="Dashboard Banner" 
             class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-20 select-none z-0" />
        <div class="absolute inset-0 bg-gradient-to-r from-carbon via-carbon/90 to-carbon/70 z-0 pointer-events-none"></div>

        <div class="relative z-10">
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Selamat Datang</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1"><?php echo e($customer->user->name); ?></h1>
            <p class="font-mono text-xs text-silver-dim mt-1">Customer ID: <?php echo e($customer->code); ?></p>
        </div>
    </div>


    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php if (isset($component)) { $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.stat-card','data' => ['label' => 'Kendaraan','value' => $customer->vehicles_count,'icon' => '🚗','color' => 'giallo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Kendaraan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customer->vehicles_count),'icon' => '🚗','color' => 'giallo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $attributes = $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $component = $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.stat-card','data' => ['label' => 'Booking Aktif','value' => $activeBookings->count(),'icon' => '📅','color' => 'arancio']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Booking Aktif','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeBookings->count()),'icon' => '📅','color' => 'arancio']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $attributes = $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $component = $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.stat-card','data' => ['label' => 'Loyalty Point','value' => number_format($customer->loyalty_points, 0, ',', '.'),'icon' => '⭐','color' => 'verde']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Loyalty Point','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($customer->loyalty_points, 0, ',', '.')),'icon' => '⭐','color' => 'verde']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $attributes = $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $component = $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.stat-card','data' => ['label' => 'Voucher Aktif','value' => $activeVouchers->count(),'icon' => '🎁','color' => 'silver']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Voucher Aktif','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activeVouchers->count()),'icon' => '🎁','color' => 'silver']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $attributes = $__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__attributesOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42)): ?>
<?php $component = $__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42; ?>
<?php unset($__componentOriginal51ca8f9b5f2319ee98f72ec28ea4da42); ?>
<?php endif; ?>
    </div>


    
    <div>
        <h2 class="font-heading text-xs uppercase tracking-[0.2em] text-silver-bright mb-3">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="<?php echo e(route('workshop.booking.create')); ?>" class="bg-carbon border border-arancio/30 hover:border-arancio p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🔧</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Booking Servis</span>
            </a>
            <a href="<?php echo e(route('workshop.ai-scan.create')); ?>" class="bg-carbon border border-verde/30 hover:border-verde p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🔊</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">AI Sound Scan</span>
            </a>
            <a href="<?php echo e(route('marketplace.index')); ?>" class="bg-carbon border border-giallo/30 hover:border-giallo p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🏎</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Jelajahi Mobil</span>
            </a>
            <a href="<?php echo e(route('parts.index')); ?>" class="bg-carbon border border-silver/20 hover:border-silver p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">⚙️</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Beli Spare Part</span>
            </a>
        </div>
    </div>


    
    <div class="grid md:grid-cols-2 gap-6">
        
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Booking Aktif</h3>
                <a href="<?php echo e(route('workshop.booking.index')); ?>" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
            </div>
            <div class="p-4 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $activeBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-steel/20 border border-gunmetal/30 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-heading text-xs text-silver-bright"><?php echo e(ucfirst(str_replace('_', ' ', $booking->service_type))); ?></span>
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'info']); ?><?php echo e(ucfirst($booking->status->label())); ?> <?php echo $__env->renderComponent(); ?>
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
                        <p class="font-mono text-[10px] text-silver-dim"><?php echo e($booking->preferred_date->format('d M Y')); ?>, <?php echo e($booking->preferred_time); ?></p>
                        <p class="font-body text-[11px] text-silver-dim mt-1"><?php echo e($booking->vehicle->full_name); ?></p>
                        <?php if($booking->workOrder): ?>
                            <div class="mt-2.5 pt-2 border-t border-gunmetal/30 flex justify-between items-center">
                                <span class="font-mono text-[10px] text-silver-dim">WO: <?php echo e($booking->workOrder->code); ?></span>
                                <a href="<?php echo e(route('workshop.tracking.show', $booking->workOrder)); ?>" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                                    Lacak Servis &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['icon' => '📅','title' => 'Tidak ada booking aktif','description' => 'Booking servis Anda akan muncul di sini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '📅','title' => 'Tidak ada booking aktif','description' => 'Booking servis Anda akan muncul di sini.']); ?>
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
                <?php endif; ?>
            </div>
        </div>


        
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Notifikasi</h3>
                <a href="<?php echo e(route('customer.notifications.index')); ?>" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
            </div>
            <div class="p-4 space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start gap-2 bg-steel/20 border border-gunmetal/30 p-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-giallo mt-1.5 shrink-0"></div>
                        <div>
                            <p class="font-body text-[11px] text-silver-bright"><?php echo e($notification->data['message'] ?? 'Notifikasi baru'); ?></p>
                            <p class="font-mono text-[9px] text-silver-dim mt-0.5"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['icon' => '🔔','title' => 'Tidak ada notifikasi','description' => 'Notifikasi baru akan muncul di sini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '🔔','title' => 'Tidak ada notifikasi','description' => 'Notifikasi baru akan muncul di sini.']); ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>


    
    <?php if($recentScans->count() > 0): ?>
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Scan AI Terbaru</h3>
            <a href="#" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
        </div>
        <div class="p-4">
            <div class="space-y-3">
                <?php $__currentLoopData = $recentScans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between bg-steel/20 border border-gunmetal/30 p-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔊</span>
                            <div>
                                <p class="font-heading text-xs text-silver-bright"><?php echo e(str_replace('_', ' ', ucfirst($scan->predicted_class ?? 'Menunggu hasil'))); ?></p>
                                <p class="font-mono text-[10px] text-silver-dim"><?php echo e($scan->created_at->format('d M Y')); ?></p>
                            </div>
                        </div>
                        <?php if($scan->confidence): ?>
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-steel/50 overflow-hidden">
                                    <div class="h-full <?php echo e($scan->confidence >= 0.8 ? 'bg-verde' : 'bg-arancio'); ?>" style="width: <?php echo e($scan->confidence * 100); ?>%"></div>
                                </div>
                                <span class="font-mono text-[10px] text-silver-dim"><?php echo e(round($scan->confidence * 100)); ?>%</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/dashboard.blade.php ENDPATH**/ ?>