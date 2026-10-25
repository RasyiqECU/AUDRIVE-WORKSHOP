<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(\App\Services\SettingService::getName()); ?> — <?php echo $__env->yieldContent('title', 'Auth'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/lamborghini.css', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-nero text-silver font-body antialiased min-h-screen bg-carbon-fiber relative overflow-x-hidden">
    
    <?php
        $loginBg = \App\Services\SettingService::getLoginBackground();
    ?>
    <?php if($loginBg): ?>
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none select-none">
            <img src="<?php echo e($loginBg); ?>" 
                 alt="Background" 
                 class="w-full h-full object-cover object-center scale-105 transition-all duration-700" />
            <div class="absolute inset-0 bg-nero/75 backdrop-blur-[2px]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-nero via-nero/60 to-nero/85"></div>
            <div class="absolute inset-0 hex-pattern opacity-25"></div>
        </div>
    <?php else: ?>
        <!-- Default Background Effects -->
        <div class="absolute inset-0 hex-pattern opacity-20 pointer-events-none"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-giallo/5 rounded-full blur-[120px] pointer-events-none"></div>
    <?php endif; ?>

    <div class="min-h-screen flex flex-col items-center justify-center p-4 relative z-10">
        
        <div class="relative z-10 mb-8 text-center">
            <div class="inline-flex items-center gap-3 mb-4">
                <img src="<?php echo e(\App\Services\SettingService::getLogo()); ?>" 
                     alt="<?php echo e(\App\Services\SettingService::getName()); ?> Logo" 
                     class="w-14 h-14 object-contain drop-shadow-[0_0_12px_rgba(245,196,0,0.35)]" />
                <div class="text-left">
                    <h1 class="font-display text-2xl font-bold tracking-wider">
                        <span class="text-silver-bright"><?php echo e(\App\Services\SettingService::getNamePart(1)); ?></span><span class="text-giallo"><?php echo e(\App\Services\SettingService::getNamePart(2)); ?></span>
                    </h1>
                    <p class="font-heading text-[10px] uppercase tracking-[0.3em] text-silver-dim">
                        <?php echo e(\App\Services\SettingService::getTagline()); ?>

                    </p>
                </div>
            </div>
        </div>

        
        <div class="relative z-10 w-full max-w-md">
            <div class="panel-chamfered bg-carbon/95 backdrop-blur-md border border-gunmetal p-8 shadow-2xl">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>

        
        <p class="relative z-10 mt-8 font-mono text-[10px] text-silver-dim uppercase tracking-wider">
            &copy; <?php echo e(date('Y')); ?> <?php echo e(\App\Services\SettingService::getName()); ?>. All rights reserved.
        </p>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/layouts/guest.blade.php ENDPATH**/ ?>