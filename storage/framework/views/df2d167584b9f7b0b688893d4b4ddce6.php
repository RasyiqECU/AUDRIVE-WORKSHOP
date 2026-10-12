<?php $__env->startSection('title', 'Layanan Bengkel & Service Center — AUDrive'); ?>
<?php $__env->startSection('meta_description', 'Layanan bengkel mobil premium AUDrive dengan teknisi profesional bersertifikat, garansi pengerjaan, dan diagnosis AI.'); ?>

<?php $__env->startSection('content'); ?>
<?php if(!isset($bookings)): ?>
    
    <section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
        
        <img src="<?php echo e(asset('images/bengkel.gif')); ?>"
             alt="AUDrive Performance Workshop & AI Diagnostic Lab"
             class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0" />

        
        <div class="absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-nero/90 via-nero/50 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-nero/90 via-nero/30 to-nero/75 z-10 pointer-events-none"></div>
        <div class="absolute inset-0 bg-nero/30 backdrop-brightness-95 z-10 pointer-events-none"></div>
        <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-nero via-nero/80 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-hazard-stripes opacity-40 z-20"></div>

        
        <div class="relative z-20 container mx-auto px-4 md:px-8 py-20 flex flex-col justify-between h-full max-h-screen">
            <div class="h-16"></div>

            <div class="max-w-3xl my-auto">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-nero/70 border border-giallo/40 backdrop-blur-md rounded mb-6">
                    <span class="inline-block w-2 h-2 bg-giallo rounded-full animate-pulse shadow-sm shadow-giallo"></span>
                    <span class="font-heading text-[11px] uppercase tracking-[0.35em] text-giallo font-bold">
                        Official Certified Workshop & AI Diagnostic Lab
                    </span>
                </div>

                <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                    PRESISI TINGGI. <span class="text-giallo">DIAGNOSIS AI.</span><br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                        PERFORMA MAKSIMAL.
                    </span>
                </h1>

                <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                    Perawatan performa tinggi untuk supercar & mobil premium Anda. Dikerjakan oleh teknisi bersertifikasi pabrikan dengan suku cadang 100% original dan teknologi AI Sound Diagnostic.
                </p>

                <div class="flex flex-wrap items-center gap-4 mt-8">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('workshop.booking.create')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                            🔧 Booking Servis Sekarang
                        </a>
                        <a href="<?php echo e(route('workshop.ai-scan.create')); ?>" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-giallo/40 hover:border-giallo text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                            🔊 Scan Suara Mesin (AI)
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                            🔑 Login untuk Booking Servis
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-white/40 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                            Daftar Akun Baru
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
                <div class="flex items-center gap-3">
                    <span class="text-giallo font-bold">BENGKEL AUDRIVE</span>
                    <span class="h-3 w-px bg-gunmetal"></span>
                    <span class="uppercase tracking-widest text-[10px]">Supercar Cleanroom & Dyno Bay</span>
                </div>

                <a href="#workshop-services" class="flex items-center gap-2 hover:text-giallo transition group">
                    <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Paket Layanan</span>
                    <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                        <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    
    <section id="workshop-services" class="container mx-auto px-4 py-14">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-giallo text-xs uppercase tracking-widest font-heading font-semibold">Pilihan Layanan</span>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">PAKET PERAWATAN & REPARASI</h2>
            <p class="text-silver-dim text-sm mt-2">Dikerjakan oleh teknisi ahli menggunakan tools standar internasional.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $services ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-carbon border border-gunmetal rounded-lg p-6 hover:border-giallo/50 transition-all duration-300 flex flex-col justify-between group shadow-lg">
                    <div>
                        <div class="w-12 h-12 rounded bg-giallo/10 border border-giallo/30 flex items-center justify-center text-giallo mb-4 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-silver-bright mb-2"><?php echo e($item['name']); ?></h3>
                        <p class="text-silver-dim text-xs leading-relaxed mb-4"><?php echo e($item['description']); ?></p>
                    </div>
                    <div class="pt-4 border-t border-gunmetal/40 flex items-center justify-between text-xs">
                        <span class="text-giallo font-semibold font-mono"><?php echo e($item['price']); ?></span>
                        <span class="text-silver-dim font-mono"><?php echo e($item['duration']); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
            <div class="p-6 bg-carbon border border-gunmetal/60 rounded-lg">
                <div class="text-2xl mb-2">🛡️</div>
                <h4 class="font-heading text-sm font-bold text-silver-bright uppercase">Garansi Servis</h4>
                <p class="text-xs text-silver-dim mt-1">Garansi pengerjaan & suku cadang hingga 30 hari.</p>
            </div>
            <div class="p-6 bg-carbon border border-gunmetal/60 rounded-lg">
                <div class="text-2xl mb-2">⚙️</div>
                <h4 class="font-heading text-sm font-bold text-silver-bright uppercase">Part 100% Asli</h4>
                <p class="text-xs text-silver-dim mt-1">Hanya menggunakan suku cadang original bergaransi resmi.</p>
            </div>
            <div class="p-6 bg-carbon border border-gunmetal/60 rounded-lg">
                <div class="text-2xl mb-2">🤖</div>
                <h4 class="font-heading text-sm font-bold text-silver-bright uppercase">AI Sound Diagnostic</h4>
                <p class="text-xs text-silver-dim mt-1">Deteksi kerusakan mesin lewat analisis suara berbasis AI.</p>
            </div>
            <div class="p-6 bg-carbon border border-gunmetal/60 rounded-lg">
                <div class="text-2xl mb-2">⏱️</div>
                <h4 class="font-heading text-sm font-bold text-silver-bright uppercase">Transparan & Realtime</h4>
                <p class="text-xs text-silver-dim mt-1">Pantau status pengerjaan mobil secara online & realtime.</p>
            </div>
        </div>
    </section>
<?php else: ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="font-display text-2xl font-bold text-silver-bright">DAFTAR BOOKING SERVIS</h1>
                <p class="text-xs text-silver-dim mt-1">Kelola dan pantau status servis kendaraan Anda</p>
            </div>
            <a href="<?php echo e(route('workshop.booking.create')); ?>" class="inline-flex items-center px-4 py-2 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded hover:bg-giallo/90 transition">
                + Buat Booking Baru
            </a>
        </div>

        <?php if($bookings->isEmpty()): ?>
            <div class="text-center py-16 bg-carbon border border-gunmetal rounded-lg">
                <p class="text-silver-dim text-sm">Belum ada booking servis aktif.</p>
                <a href="<?php echo e(route('workshop.booking.create')); ?>" class="inline-block mt-4 text-giallo text-xs uppercase tracking-wider hover:underline">
                    Buat booking sekarang &rarr;
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 bg-carbon border border-gunmetal rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-heading font-bold text-silver-bright text-sm uppercase"><?php echo e($booking->service_type); ?></span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase tracking-wider bg-giallo/20 text-giallo border border-giallo/30">
                                    <?php echo e($booking->status->value ?? $booking->status); ?>

                                </span>
                            </div>
                            <p class="text-xs text-silver-dim mt-1">
                                Kendaraan: <span class="text-silver-bright"><?php echo e($booking->vehicle?->license_plate ?? 'N/A'); ?></span> — <?php echo e($booking->vehicle?->brand); ?> <?php echo e($booking->vehicle?->model); ?>

                            </p>
                            <p class="text-xs text-silver-dim">
                                Jadwal: <?php echo e($booking->preferred_date?->format('d M Y')); ?> pk <?php echo e($booking->preferred_time); ?>

                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('workshop.booking.show', $booking)); ?>" class="px-3 py-1.5 border border-white/20 text-xs text-silver-bright rounded hover:border-giallo transition">
                                Detail
                            </a>
                            <?php if($booking->workOrder): ?>
                                <a href="<?php echo e(route('workshop.tracking.show', $booking->workOrder)); ?>" class="px-3 py-1.5 bg-giallo/20 text-giallo text-xs rounded hover:bg-giallo/30 transition">
                                    Lacak Servis
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="mt-4">
                    <?php echo e($bookings->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (isset($bookings) && auth()->check() ? 'layouts.app' : 'layouts.public'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/workshop/index.blade.php ENDPATH**/ ?>