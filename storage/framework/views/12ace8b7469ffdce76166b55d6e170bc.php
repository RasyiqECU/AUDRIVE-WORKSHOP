<?php $__env->startSection('title', 'Hubungi Kami — AUDrive Premium Automotive'); ?>
<?php $__env->startSection('meta_description', 'Hubungi showroom dan bengkel AUDrive untuk konsultasi pembelian kendaraan mewah, booking servis, pemesanan spare part, atau bantuan darurat.'); ?>

<?php $__env->startSection('content'); ?>

<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    
    <img src="<?php echo e(asset('images/kontak.jpg')); ?>"
         alt="AUDrive — Hubungi Kami"
         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0" />

    
    <div class="absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-nero/90 via-nero/50 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-nero/90 via-nero/40 to-nero/70 z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-nero/25 z-10 pointer-events-none"></div>
    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-nero via-nero/80 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-hazard-stripes opacity-40 z-20"></div>

    
    <div class="relative z-20 container mx-auto px-4 md:px-8 py-20 flex flex-col justify-between h-full max-h-screen">
        <div class="h-16"></div>

        <div class="max-w-3xl my-auto">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-nero/70 border border-giallo/40 backdrop-blur-md rounded mb-6">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full animate-pulse shadow-sm shadow-giallo"></span>
                <span class="font-heading text-[11px] uppercase tracking-[0.35em] text-giallo font-bold">
                    Concierge & VIP Support
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                HUBUNGI <span class="text-giallo">AUDRIVE.</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    KAMI SIAP MEMBANTU.
                </span>
            </h1>

            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                Tim VIP Advisor dan Customer Care kami siap membantu segala kebutuhan mobil premium, jadwal servis, ketersediaan suku cadang, dan konsultasi teknis.
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-8">
                <a href="#contact-content" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                    📞 Hubungi Kami
                </a>
                <a href="https://wa.me/6281218450622" target="_blank" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-giallo/50 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                    💬 WhatsApp VIP
                </a>
            </div>
        </div>

        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">AUDRIVE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">24/7 Premium Support</span>
            </div>
            <a href="#contact-content" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Scroll Down</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>


<div id="contact-content"></div>


<section class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-4">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 bg-carbon border-gunmetal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 bg-carbon border-gunmetal']); ?>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-giallo/10 border border-giallo/30 flex items-center justify-center text-giallo text-lg shrink-0">
                        📍
                    </div>
                    <div>
                        <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-1">Showroom & Workshop Utama</h3>
                        <p class="font-body text-xs text-silver-dim leading-relaxed">
                            Jln. Pitik Wangi Banyumas<br>
                            Kecamatan Serpong, Kota Tangerang Selatan<br>
                            Provinsi Banten, 15311
                        </p>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 bg-carbon border-gunmetal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 bg-carbon border-gunmetal']); ?>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-giallo/10 border border-giallo/30 flex items-center justify-center text-giallo text-lg shrink-0">
                        📞
                    </div>
                    <div>
                        <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-1">Telepon & WhatsApp</h3>
                        <p class="font-body text-xs text-silver-dim">
                            Hotline Showroom: <a href="tel:+6281218450622" class="text-silver-bright hover:text-giallo">+62 812-1845-0622</a><br>
                            WhatsApp VIP: <a href="https://wa.me/6281218450622" class="text-giallo hover:underline">+62 812-1845-0622</a><br>
                            Emergency Towing: <span class="text-danger font-mono font-bold">1500-888</span> (24/7)
                        </p>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 bg-carbon border-gunmetal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 bg-carbon border-gunmetal']); ?>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-giallo/10 border border-giallo/30 flex items-center justify-center text-giallo text-lg shrink-0">
                        ⏱️
                    </div>
                    <div>
                        <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-1">Jam Operasional</h3>
                        <p class="font-body text-xs text-silver-dim leading-relaxed">
                            Senin – Sabtu: 08:30 – 18:00 WIB<br>
                            Minggu & Libur: 09:00 – 16:00 WIB (Showroom)<br>
                            Layanan Towing Darurat: 24 Jam Nonstop
                        </p>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 bg-carbon border-gunmetal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 bg-carbon border-gunmetal']); ?>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-giallo/10 border border-giallo/30 flex items-center justify-center text-giallo text-lg shrink-0">
                        ✉️
                    </div>
                    <div>
                        <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-1">Email Resmi</h3>
                        <p class="font-body text-xs text-silver-dim">
                            Informasi: <a href="mailto:info@audrive.id" class="text-silver-bright hover:text-giallo">info@audrive.id</a><br>
                            VIP Sales: <a href="mailto:sales@audrive.id" class="text-silver-bright hover:text-giallo">sales@audrive.id</a><br>
                            Parts Division: <a href="mailto:parts@audrive.id" class="text-silver-bright hover:text-giallo">parts@audrive.id</a>
                        </p>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        </div>

        
        <div class="lg:col-span-2">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 md:p-8 bg-carbon border-gunmetal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 md:p-8 bg-carbon border-gunmetal']); ?>
                <h3 class="font-display text-xl font-bold text-silver-bright mb-2">KIRIM PESAN LANGSUNG</h3>
                <p class="font-body text-xs text-silver-dim mb-6">
                    Isi formulir di bawah ini dan konsultan kami akan merespons dalam waktu maksimal 15 menit pada jam operasional.
                </p>

                
                <?php if(session('success')): ?>
                    <div class="mb-4 flex items-start gap-3 bg-verde/10 border border-verde/40 rounded px-4 py-3">
                        <span class="text-verde text-lg shrink-0">✅</span>
                        <p class="font-body text-sm text-verde"><?php echo e(session('success')); ?></p>
                    </div>
                <?php endif; ?>

                
                <?php if($errors->any()): ?>
                    <div class="mb-4 bg-danger/10 border border-danger/40 rounded px-4 py-3">
                        <ul class="space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="font-body text-xs text-danger">• <?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('contact.send')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-1 font-semibold">Nama Lengkap *</label>
                            <input type="text" name="name" required placeholder="Contoh: Alexander Pratama"
                                   class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
                        </div>
                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-1 font-semibold">Nomor WhatsApp / HP *</label>
                            <input type="tel" name="phone" required placeholder="081234567890"
                                   class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-1 font-semibold">Alamat Email *</label>
                            <input type="email" name="email" required placeholder="alexander@domain.com"
                                   class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
                        </div>
                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-1 font-semibold">Kategori Keperluan</label>
                            <select name="topic" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                <option value="sales">Pembelian Mobil Baru / Pre-Owned</option>
                                <option value="workshop">Booking Servis & Maintenance</option>
                                <option value="parts">Pemesanan Spare Part / Aksesori</option>
                                <option value="ai">Konsultasi AI Sound Diagnostic</option>
                                <option value="other">Keperluan Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-1 font-semibold">Pesan / Detail Kendaraan</label>
                        <textarea name="message" rows="4" placeholder="Tuliskan pesan Anda atau detail tipe kendaraan yang ingin dikonsultasikan..."
                                  class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <span class="font-body text-[11px] text-silver-dim">🔒 Privasi data Anda terjamin aman.</span>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','size' => 'md']); ?>
                            Kirim Pesan
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
                </form>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/pages/contact.blade.php ENDPATH**/ ?>