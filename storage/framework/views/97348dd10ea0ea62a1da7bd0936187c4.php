<?php $__env->startSection('title', 'Login'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">LOGIN</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Masuk ke akun AUDrive Anda</p>
    </div>


    
    <?php if(session('status')): ?>
        <div class="bg-verde/10 border border-verde/30 p-3 text-sm text-verde">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>


    
    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>


        
        <div>
            <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="<?php echo e(old('email')); ?>"
                required
                autofocus
                autocomplete="username"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="email@audrive.local"
            />
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>


        
        <div>
            <label for="password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="••••••••"
            />
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>


        
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gunmetal bg-nero text-giallo focus:ring-giallo focus:ring-offset-0"
                />
                <span class="ms-2 text-xs text-silver-dim font-heading uppercase tracking-wider">Ingat Saya</span>
            </label>


            <?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(route('password.request')); ?>" class="text-xs text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                    Lupa Password?
                </a>
            <?php endif; ?>
        </div>


        
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button"
        >
            Masuk
        </button>
    </form>


    
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <p class="text-xs text-silver-dim">
            Belum punya akun?
            <a href="<?php echo e(route('register')); ?>" class="text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/auth/login.blade.php ENDPATH**/ ?>