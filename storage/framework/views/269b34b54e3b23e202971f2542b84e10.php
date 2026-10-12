<?php $__env->startSection('title', 'Buat Tiket Bantuan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-2xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('customer.tickets.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Tiket
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pusat Layanan</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            BUAT TIKET BANTUAN
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Jelaskan pertanyaan atau kendala yang Anda hadapi secara rinci.
        </p>
    </div>

    
    <form action="<?php echo e(route('customer.tickets.store')); ?>" method="POST" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <?php echo csrf_field(); ?>

        <div>
            <label for="subject" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Subjek Masalah <span class="text-red-500">*</span>
            </label>
            <input type="text" name="subject" id="subject" value="<?php echo e(old('subject')); ?>" required placeholder="Contoh: Pertanyaan Garansi Transmisi Mobil" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
            <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label for="priority" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Tingkat Prioritas <span class="text-red-500">*</span>
            </label>
            <select name="priority" id="priority" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>Rendah (Pertanyaan umum)</option>
                <option value="medium" <?php echo e(old('priority', 'medium') == 'medium' ? 'selected' : ''); ?>>Sedang (Kendala servis rutin)</option>
                <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>Tinggi (Mogok / Kendala teknis)</option>
                <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>Mendesak (Emergency On-the-road)</option>
            </select>
            <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label for="description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Deskripsi Kendala <span class="text-red-500">*</span>
            </label>
            <textarea name="description" id="description" rows="5" required placeholder="Tuliskan kronologi, gejala kerusakan, atau pertanyaan selengkapnya..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="pt-2 flex justify-end gap-3">
            <a href="<?php echo e(route('customer.tickets.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button">
                Kirim Tiket
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/tickets/create.blade.php ENDPATH**/ ?>