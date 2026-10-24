
<nav class="fixed top-0 left-0 right-0 z-50 bg-nero/60 backdrop-blur-md border-b border-white/10 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                <img src="<?php echo e(\App\Services\SettingService::getLogo()); ?>" alt="<?php echo e(\App\Services\SettingService::getName()); ?> Logo" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
                <div>
                    <h1 class="font-display text-giallo text-sm tracking-wider font-bold"><?php echo e(\App\Services\SettingService::getName()); ?></h1>
                    <p class="font-mono text-[8px] text-silver-dim tracking-widest uppercase"><?php echo e(\App\Services\SettingService::getTagline()); ?></p>
                </div>
            </a>


            
            <div class="hidden md:flex items-center gap-6">
                <a href="<?php echo e(route('marketplace.index')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium <?php echo e(request()->routeIs('marketplace.*') ? 'text-giallo' : ''); ?>">
                    Marketplace
                </a>
                <a href="<?php echo e(route('workshop.index')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium <?php echo e(request()->routeIs('workshop.*') ? 'text-giallo' : ''); ?>">
                    Bengkel
                </a>
                <a href="<?php echo e(route('parts.index')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium <?php echo e(request()->routeIs('parts.*') ? 'text-giallo' : ''); ?>">
                    Spare Part
                </a>
                <a href="<?php echo e(route('about')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                    Tentang
                </a>
                <a href="<?php echo e(route('contact')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                    Kontak
                </a>
            </div>


            
            <div class="flex items-center gap-3">
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->role === 'customer'): ?>
                        <a href="<?php echo e(route('customer.dashboard')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Dashboard
                        </a>
                    <?php elseif(auth()->user()->role === 'mechanic'): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Panel Bengkel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Admin Panel
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                        Login
                    </a>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => ''.e(route('register')).'','variant' => 'primary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('register')).'','variant' => 'primary','size' => 'sm']); ?>
                        Daftar
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>



<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/partials/navbar-public.blade.php ENDPATH**/ ?>