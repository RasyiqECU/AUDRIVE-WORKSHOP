<?php $__env->startSection('title', 'Katalog Suku Cadang & Aksesori Premium — AUDrive'); ?>
<?php $__env->startSection('meta_description', 'Beli suku cadang orisinal (OEM) & aftermarket performa tinggi bergaransi untuk mobil premium dan supercar di AUDrive. Kualitas terjamin dengan layanan instalasi profesional.'); ?>

<?php $__env->startSection('content'); ?>

<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    
    <img src="<?php echo e(\App\Services\SettingService::getSparepartBackground()); ?>"
         alt="AUDrive Genuine Spare Parts & Performance Components"
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
                    100% Genuine OEM & High Performance Parts
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                GENUINE OEM & <span class="text-giallo">PERFORMANCE</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    SPARE PARTS.
                </span>
            </h1>

            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                Komponen mesin presisi, pengereman keramik, elektrikal, suspensi sport, dan pelumas performa tinggi untuk supercar & mobil premium dengan garansi resmi.
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-8">
                <a href="#spareparts-catalog" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                    ⚙️ Cari Suku Cadang
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('parts.cart')); ?>" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-giallo/50 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                        🛒 Keranjang Belanja
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('contact')); ?>" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-white/40 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                        💬 Konsultasi Part Indent
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">SPARE PART STORE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">100% Certified Direct Manufacturer</span>
            </div>

            <a href="#spareparts-catalog" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Katalog Part</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>


<section id="spareparts-catalog" class="container mx-auto px-4 py-8">
    
    <div class="bg-carbon border border-gunmetal p-5 mb-8 rounded shadow-lg">
        <form method="GET" action="<?php echo e(route('parts.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            
            <div class="lg:col-span-2">
                <label for="search" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Cari Nama Part / Kode SKU / Merek
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="<?php echo e($filters['search'] ?? request('search')); ?>"
                           placeholder="Contoh: Brake Pad Ceramic, Oil Filter, Spark Plug..."
                           class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none placeholder-silver-dim/50">
                    <?php if(!empty($filters['search'])): ?>
                        <a href="<?php echo e(route('parts.index')); ?>" class="absolute right-2.5 top-2 text-silver-dim hover:text-silver-bright text-xs">✕</a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div>
                <label for="category_id" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Kategori
                </label>
                <select name="category_id" id="category_id" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e((string)($filters['category_id'] ?? request('category_id')) === (string)$cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label for="vehicle_brand" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Kompatibilitas Mobil
                </label>
                <input type="text" name="vehicle_brand" id="vehicle_brand" value="<?php echo e($filters['vehicle_brand'] ?? request('vehicle_brand')); ?>"
                       placeholder="Contoh: Porsche, BMW, Audi..."
                       class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none placeholder-silver-dim/50">
            </div>

            
            <div>
                <label for="sort" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Urutkan
                </label>
                <select name="sort" id="sort" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="name_asc" <?php echo e(($filters['sort'] ?? '') === 'name_asc' ? 'selected' : ''); ?>>Nama (A - Z)</option>
                    <option value="name_desc" <?php echo e(($filters['sort'] ?? '') === 'name_desc' ? 'selected' : ''); ?>>Nama (Z - A)</option>
                    <option value="price_asc" <?php echo e(($filters['sort'] ?? '') === 'price_asc' ? 'selected' : ''); ?>>Harga Terendah</option>
                    <option value="price_desc" <?php echo e(($filters['sort'] ?? '') === 'price_desc' ? 'selected' : ''); ?>>Harga Tertinggi</option>
                </select>
            </div>

            
            <div class="sm:col-span-2 lg:col-span-5 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gunmetal/40">
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo/90 text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all shadow-md shadow-giallo/10">
                        Cari Spare Part
                    </button>
                    <?php if(!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['vehicle_brand']) || request()->has('sort')): ?>
                        <a href="<?php echo e(route('parts.index')); ?>" class="px-4 py-2 border border-gunmetal hover:border-silver-dim text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition">
                            Reset Filter
                        </a>
                    <?php endif; ?>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('parts.cart')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-steel/40 hover:bg-steel/60 border border-gunmetal hover:border-giallo/50 rounded font-heading text-xs text-silver-bright transition">
                        <span>🛒 Keranjang Belanja</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    
    <?php if($categories->count() > 0): ?>
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 scrollbar-thin">
            <a href="<?php echo e(route('parts.index')); ?>"
               class="px-3.5 py-1.5 rounded-full text-xs font-heading uppercase tracking-wider whitespace-nowrap border transition-premium <?php echo e(empty($filters['category_id']) ? 'bg-giallo text-nero border-giallo font-bold' : 'bg-steel/30 text-silver-dim border-gunmetal hover:border-giallo/40 hover:text-silver-bright'); ?>">
                Semua Part
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('parts.category', $category->slug)); ?>"
                   class="px-3.5 py-1.5 rounded-full text-xs font-heading uppercase tracking-wider whitespace-nowrap border transition-premium <?php echo e((string)($filters['category_id'] ?? '') === (string)$category->id ? 'bg-giallo text-nero border-giallo font-bold' : 'bg-steel/30 text-silver-dim border-gunmetal hover:border-giallo/40 hover:text-silver-bright'); ?>">
                    <?php echo e($category->name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="flex items-center justify-between mb-6">
        <p class="font-mono text-xs text-silver-dim">
            Menampilkan <span class="text-silver-bright font-bold"><?php echo e($parts->total()); ?></span> suku cadang
        </p>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginalb98de063c94d934e5bb57e4284c5a4e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb98de063c94d934e5bb57e4284c5a4e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.parts.part-card','data' => ['part' => $part]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('parts.part-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['part' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($part)]); ?>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-12">
                <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['icon' => '⚙️','title' => 'Tidak Ada Suku Cadang Ditemukan','description' => 'Coba ubah kata kunci pencarian atau reset filter untuk melihat katalog suku cadang lainnya.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '⚙️','title' => 'Tidak Ada Suku Cadang Ditemukan','description' => 'Coba ubah kata kunci pencarian atau reset filter untuk melihat katalog suku cadang lainnya.']); ?>
                    <div class="mt-4">
                        <a href="<?php echo e(route('parts.index')); ?>" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase font-semibold rounded">
                            Tampilkan Semua Part
                        </a>
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

    
    <?php if($parts->hasPages()): ?>
        <div class="mt-10">
            <?php echo e($parts->withQueryString()->links()); ?>

        </div>
    <?php endif; ?>
</section>


<section class="bg-carbon border-t border-gunmetal/40 py-12 mt-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🛡️</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">100% Suku Cadang Asli</h3>
                    <p class="font-body text-xs text-silver-dim">Garansi keaslian komponen langsung dari produsen OEM terpercaya dunia.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🔧</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">Layanan Pasang di Bengkel</h3>
                    <p class="font-body text-xs text-silver-dim">Dapat langsung dipasang oleh teknisi master bersertifikat AUDrive.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🚚</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">Pengiriman Cepat & Aman</h3>
                    <p class="font-body text-xs text-silver-dim">Kemasan proteksi ganda dengan asuransi pengiriman ke seluruh Indonesia.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/spare-parts/index.blade.php ENDPATH**/ ?>