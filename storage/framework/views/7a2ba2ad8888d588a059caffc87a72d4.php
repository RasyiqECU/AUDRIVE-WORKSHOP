<?php $__env->startSection('title', 'Detail Tiket #' . $ticket->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('customer.tickets.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Tiket
        </a>
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs text-giallo font-mono">#TK-<?php echo e(str_pad($ticket->id, 5, '0', STR_PAD_LEFT)); ?></span>
                <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide mt-1">
                    <?php echo e($ticket->subject); ?>

                </h1>
            </div>
            <div>
                <?php
                    $statusVal = $ticket->status->value ?? (string) $ticket->status;
                ?>
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    <?php if($statusVal === 'open'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    <?php elseif($statusVal === 'in_progress'): ?> bg-amber-950/60 text-amber-400 border border-amber-800
                    <?php elseif($statusVal === 'resolved'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    <?php else: ?> bg-steel text-silver-dim border border-gunmetal <?php endif; ?>">
                    <?php echo e(str_replace('_', ' ', $statusVal)); ?>

                </span>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gunmetal/40 pb-3 text-xs text-silver-dim">
            <span>Dibuat: <strong class="text-silver-bright"><?php echo e($ticket->created_at->format('d M Y, H:i')); ?></strong></span>
            <span>Prioritas: <strong class="text-giallo uppercase"><?php echo e($ticket->priority->value ?? $ticket->priority); ?></strong></span>
        </div>
        <div class="text-xs text-silver-bright leading-relaxed whitespace-pre-line">
            <?php echo e($ticket->description); ?>

        </div>
    </div>

    
    <?php if($ticket->messages->count() > 0): ?>
        <div class="space-y-3">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-dim px-1">Riwayat Balasan</h3>
            <?php $__currentLoopData = $ticket->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel p-4 border <?php echo e($msg->sender_type === 'staff' ? 'bg-steel/40 border-giallo/30' : 'bg-carbon border-gunmetal'); ?> rounded-lg space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="font-bold flex items-center gap-1.5 <?php echo e($msg->sender_type === 'staff' ? 'text-giallo' : 'text-silver-bright'); ?>">
                            <?php echo e($msg->sender_type === 'staff' ? '🛡️ AUDrive Concierge / Support' : '👤 Anda'); ?>

                            <?php if($msg->sender_type === 'staff' && $msg->user): ?>
                                <span class="text-[10px] text-silver-dim font-normal">(<?php echo e($msg->user->name); ?>)</span>
                            <?php endif; ?>
                        </span>
                        <span class="text-silver-dim text-[10px] font-mono"><?php echo e($msg->created_at->format('d M Y, H:i')); ?></span>
                    </div>
                    <div class="text-xs text-silver-bright leading-relaxed whitespace-pre-line">
                        <?php echo e($msg->message); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Kirim Pesan Tambahan</h3>
        <form action="<?php echo e(route('customer.tickets.reply', $ticket->id)); ?>" method="POST" class="space-y-3">
            <?php echo csrf_field(); ?>
            <textarea name="message" rows="4" required placeholder="Tulis balasan atau informasi tambahan untuk teknisi support..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none"></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button">
                    Kirim Balasan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/tickets/show.blade.php ENDPATH**/ ?>