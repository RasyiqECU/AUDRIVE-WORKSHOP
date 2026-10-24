


<?php $__env->startSection('title', 'AI Sound Scan'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
            AI Sound Scan
        </h1>
        <p class="text-sm text-silver-dim mt-2">
            Upload rekaman suara mesin untuk analisis kerusakan otomatis menggunakan AI
        </p>
    </div>


    
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                <span class="text-giallo">🎤</span> Upload Audio
            </h2>
        </div>


        <form action="<?php echo e(route('customer.ai-scan.store')); ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            <?php echo csrf_field(); ?>


            
            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    Kendaraan (Opsional)
                </label>
                <select name="customer_vehicle_id" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium">
                    <option value="">Pilih kendaraan...</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vehicle->id); ?>">
                            <?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?> - <?php echo e($vehicle->plate_number); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-silver-dim mt-1">Pilih kendaraan untuk hasil analisis yang lebih akurat</p>
            </div>


            
            <div x-data="{ fileName: '', fileSize: '' }">
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    File Audio <span class="text-danger">*</span>
                </label>


                <div class="border-2 border-dashed border-gunmetal hover:border-giallo/40 transition-premium p-8 text-center cursor-pointer"
                     @click="$refs.audioInput.click()"
                     @dragover.prevent="$el.classList.add('border-giallo')"
                     @dragleave.prevent="$el.classList.remove('border-giallo')"
                     @drop.prevent="
                         $refs.audioInput.files = $event.dataTransfer.files;
                         fileName = $event.dataTransfer.files[0].name;
                         fileSize = ($event.dataTransfer.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                     ">


                    <input type="file"
                           name="audio"
                           accept=".wav,.mp3,.m4a"
                           x-ref="audioInput"
                           class="hidden"
                           @change="
                               fileName = $event.target.files[0].name;
                               fileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                           "
                           required>


                    <template x-if="!fileName">
                        <div>
                            <span class="text-4xl text-gunmetal block mb-3">🎵</span>
                            <p class="font-heading text-xs text-silver-dim uppercase tracking-wider">
                                Klik atau drag file audio ke sini
                            </p>
                            <p class="font-mono text-[10px] text-silver-dim/50 mt-2">
                                Format: WAV, MP3, M4A • Maks 20MB
                            </p>
                        </div>
                    </template>


                    <template x-if="fileName">
                        <div>
                            <span class="text-3xl text-verde block mb-2">✓</span>
                            <p class="font-heading text-xs text-verde uppercase tracking-wider" x-text="fileName"></p>
                            <p class="font-mono text-[10px] text-silver-dim mt-1" x-text="fileSize"></p>
                        </div>
                    </template>
                </div>


                <?php $__errorArgs = ['audio'];
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


            
            <div class="bg-steel/20 border border-gunmetal/30 p-4">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                    💡 Tips Rekaman
                </h3>
                <ul class="text-xs text-silver-dim space-y-1">
                    <li>• Rekam suara mesin dalam kondisi idle (mesin menyala tapi tidak digas)</li>
                    <li>• Pastikan rekaman minimal 5 detik</li>
                    <li>• Hindari noise background berlebih</li>
                    <li>• Dekatkan microphone ke mesin (30-50 cm)</li>
                </ul>
            </div>


            
            <button type="submit" class="w-full px-5 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Mulai Analisis
            </button>
        </form>
    </div>


    
    <?php if(isset($recentScans) && $recentScans->count() > 0): ?>
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
                    Scan Terbaru
                </h2>
                <a href="<?php echo e(route('customer.ai-scan.index')); ?>" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">
                    Lihat Semua →
                </a>
            </div>


            <div class="divide-y divide-gunmetal/30">
                <?php $__currentLoopData = $recentScans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('customer.ai-scan.show', $scan)); ?>" class="block p-4 hover:bg-steel/20 transition-premium">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🔊</span>
                                <div>
                                    <p class="font-heading text-sm text-silver-bright">
                                        <?php echo e($scan->vehicle ? $scan->vehicle->brand . ' ' . $scan->vehicle->model : 'Tanpa kendaraan'); ?>

                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        <?php echo e($scan->created_at->format('d M Y, H:i')); ?>

                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border <?php echo e($scan->getStatusColor()); ?> bg-steel/30">
                                    <?php echo e($scan->getStatusLabel()); ?>

                                </span>
                                <?php if($scan->predicted_class): ?>
                                    <p class="font-mono text-[10px] text-silver-dim mt-1">
                                        <?php echo e(str_replace('_', ' ', ucfirst($scan->predicted_class))); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/ai-scan/create.blade.php ENDPATH**/ ?>