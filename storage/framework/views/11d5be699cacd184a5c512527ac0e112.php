<?php $__env->startSection('title', 'Register'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">DAFTAR</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Buat akun AUDrive Anda</p>
    </div>


    
    <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>


        
        <div>
            <label for="name" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Nama Lengkap <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="<?php echo e(old('name')); ?>"
                required
                autofocus
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="Nama lengkap Anda"
            />
            <?php $__errorArgs = ['name'];
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
            <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="<?php echo e(old('email')); ?>"
                required
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
            <label for="phone" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Nomor Telepon <span class="text-danger">*</span>
            </label>
            <input
                id="phone"
                type="tel"
                name="phone"
                value="<?php echo e(old('phone')); ?>"
                required
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="08xxxxxxxxxx"
            />
            <?php $__errorArgs = ['phone'];
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
                Password <span class="text-danger">*</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
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


        
        <div>
            <label for="password_confirmation" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Konfirmasi Password <span class="text-danger">*</span>
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="••••••••"
            />
        </div>


        
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button mt-6"
        >
            Daftar
        </button>
    </form>


    
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <p class="text-xs text-silver-dim">
            Sudah punya akun?
            <a href="<?php echo e(route('login')); ?>" class="text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                Login
            </a>
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/auth/register.blade.php ENDPATH**/ ?>