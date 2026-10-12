<?php $__env->startSection('title', 'Point of Sale (Kasir)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{
    activeTab: '<?php echo e($selectedWorkOrder ? 'work-orders' : 'work-orders'); ?>',
    cart: [],
    selectedWo: <?php echo e($selectedWorkOrder ? json_encode([
        'id' => $selectedWorkOrder->id,
        'code' => $selectedWorkOrder->code,
        'customer_name' => $selectedWorkOrder->vehicle?->customer?->user?->name ?? 'Customer',
        'customer_phone' => $selectedWorkOrder->vehicle?->customer?->user?->phone ?? '-',
        'vehicle' => $selectedWorkOrder->vehicle ? ($selectedWorkOrder->vehicle->brand . ' ' . $selectedWorkOrder->vehicle->model . ' (' . ($selectedWorkOrder->vehicle->plate_number ?: 'Tanpa Plat') . ')') : '-',
        'total_jasa' => $selectedWorkOrder->total_jasa_idr,
        'total_part' => $selectedWorkOrder->total_part_idr,
        'subtotal' => $selectedWorkOrder->total_idr,
        'items' => $selectedWorkOrder->items->map(fn($item) => [
            'type' => $item->item_type ?? 'service',
            'desc' => $item->description ?: ($item->sparePart?->name ?? 'Item'),
            'qty' => $item->qty,
            'price' => $item->unit_price_idr,
            'subtotal' => $item->subtotal_idr
        ])
    ]) : 'null'); ?>,
    discountWo: 0,
    paymentMethodWo: 'cash',
    partsSearch: '',

    selectWorkOrder(wo) {
        this.selectedWo = wo;
        this.discountWo = 0;
    },

    get woTax() {
        if (!this.selectedWo) return 0;
        let discounted = Math.max(0, this.selectedWo.subtotal - this.discountWo);
        return Math.round(discounted * 0.11);
    },

    get woTotal() {
        if (!this.selectedWo) return 0;
        let discounted = Math.max(0, this.selectedWo.subtotal - this.discountWo);
        return discounted + this.woTax;
    },

    addToCart(part) {
        let existing = this.cart.find(item => item.id === part.id);
        if (existing) {
            if (existing.qty < part.stock) {
                existing.qty++;
            }
        } else {
            this.cart.push({
                id: part.id,
                name: part.name,
                sku: part.sku,
                price: part.price_idr,
                stock: part.stock,
                qty: 1
            });
        }
    },

    removeFromCart(index) {
        this.cart.splice(index, 1);
    },

    get cartSubtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },

    get cartTax() {
        return Math.round(this.cartSubtotal * 0.11);
    },

    get cartTotal() {
        return this.cartSubtotal + this.cartTax;
    }
}">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Kasir & Pembayaran</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                POINT OF SALE (POS)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kasir pembayaran Work Order servis bengkel & penjualan cepat suku cadang.
            </p>
        </div>

        
        <div class="flex bg-carbon border border-gunmetal p-1 rounded">
            <button @click="activeTab = 'work-orders'"
                    :class="activeTab === 'work-orders' ? 'bg-giallo text-nero font-bold' : 'text-silver-dim hover:text-silver-bright'"
                    class="px-4 py-2 text-xs font-heading uppercase tracking-wider rounded transition-all flex items-center gap-2">
                <span>🔧 Tagihan Servis (WO)</span>
                <?php if($pendingWorkOrders->count() > 0): ?>
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full"
                          :class="activeTab === 'work-orders' ? 'bg-nero text-giallo' : 'bg-giallo/20 text-giallo'">
                        <?php echo e($pendingWorkOrders->count()); ?>

                    </span>
                <?php endif; ?>
            </button>
            <button @click="activeTab = 'parts'"
                    :class="activeTab === 'parts' ? 'bg-giallo text-nero font-bold' : 'text-silver-dim hover:text-silver-bright'"
                    class="px-4 py-2 text-xs font-heading uppercase tracking-wider rounded transition-all flex items-center gap-2">
                <span>⚙️ Penjualan Spare Part</span>
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-2 space-y-4">

            
            <div x-show="activeTab === 'work-orders'" class="space-y-4">
                <div class="panel border border-gunmetal/80 bg-carbon overflow-hidden">
                    <div class="bg-steel/30 px-5 py-3.5 border-b border-gunmetal flex items-center justify-between">
                        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                            <span>Daftar Work Order Menunggu Pembayaran</span>
                            <span class="text-[10px] px-2 py-0.5 bg-giallo/10 text-giallo border border-giallo/30 rounded">
                                <?php echo e($pendingWorkOrders->count()); ?> Siap Bayar
                            </span>
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-gunmetal bg-steel/20 text-[10px] font-heading uppercase tracking-wider text-silver-dim">
                                    <th class="py-3 px-4">Work Order</th>
                                    <th class="py-3 px-4">Customer & Mobil</th>
                                    <th class="py-3 px-4">Mekanik</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Total Biaya</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gunmetal/30">
                                <?php $__empty_1 = true; $__currentLoopData = $pendingWorkOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $woData = [
                                            'id' => $wo->id,
                                            'code' => $wo->code,
                                            'customer_name' => $wo->vehicle?->customer?->user?->name ?? 'Customer',
                                            'customer_phone' => $wo->vehicle?->customer?->user?->phone ?? '-',
                                            'vehicle' => $wo->vehicle ? ($wo->vehicle->brand . ' ' . $wo->vehicle->model . ' (' . ($wo->vehicle->plate_number ?: 'Tanpa Plat') . ')') : '-',
                                            'total_jasa' => $wo->total_jasa_idr,
                                            'total_part' => $wo->total_part_idr,
                                            'subtotal' => $wo->total_idr,
                                            'items' => $wo->items->map(fn($it) => [
                                                'type' => $it->item_type ?? 'service',
                                                'desc' => $it->description ?: ($it->sparePart?->name ?? 'Item'),
                                                'qty' => $it->qty,
                                                'price' => $it->unit_price_idr,
                                                'subtotal' => $it->subtotal_idr
                                            ])
                                        ];
                                    ?>
                                    <tr class="hover:bg-steel/20 transition-all cursor-pointer"
                                        :class="selectedWo && selectedWo.id === <?php echo e($wo->id); ?> ? 'bg-giallo/10 border-l-2 border-giallo' : ''"
                                        @click="selectWorkOrder(<?php echo e(json_encode($woData)); ?>)">
                                        <td class="py-3 px-4 font-mono font-bold text-giallo">
                                            <?php echo e($wo->code); ?>

                                            <span class="block text-[10px] text-silver-dim font-normal"><?php echo e($wo->created_at->format('d/m H:i')); ?></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <p class="font-bold text-silver-bright"><?php echo e($wo->vehicle?->customer?->user?->name ?? 'Customer'); ?></p>
                                            <p class="text-[11px] text-silver-dim"><?php echo e($wo->vehicle ? "{$wo->vehicle->brand} {$wo->vehicle->model}" : '-'); ?> (<?php echo e($wo->vehicle?->plate_number ?: 'Tanpa Plat'); ?>)</p>
                                        </td>
                                        <td class="py-3 px-4 text-silver-dim text-[11px]">
                                            👨‍🔧 <?php echo e($wo->mechanic?->name ?? '-'); ?>

                                        </td>
                                        <td class="py-3 px-4">
                                            <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $wo->status->value ?? $wo->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($wo->status->value ?? $wo->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $attributes = $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $component = $__componentOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-silver-bright">
                                            Rp <?php echo e(number_format($wo->total_idr, 0, ',', '.')); ?>

                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <button type="button"
                                                    class="px-3 py-1.5 bg-giallo text-nero font-heading font-bold text-[10px] uppercase rounded hover:bg-arancio transition-premium">
                                                Pilih Bayar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                            ✓ Tidak ada tagihan Work Order yang tertunda.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div x-show="activeTab === 'parts'" class="space-y-4">
                <div class="panel border border-gunmetal/70 bg-carbon p-4">
                    <input type="text"
                           x-model="partsSearch"
                           placeholder="Ketik nama suku cadang atau scan barcode SKU..."
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-[550px] overflow-y-auto p-1">
                    <?php $__empty_1 = true; $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div x-show="!partsSearch || '<?php echo e(strtolower($part->name . ' ' . $part->sku)); ?>'.includes(partsSearch.toLowerCase())"
                             @click="addToCart(<?php echo e(json_encode($part)); ?>)"
                             class="panel border border-gunmetal/60 bg-carbon hover:border-giallo/60 p-4 flex flex-col justify-between transition-all cursor-pointer">
                            <div>
                                <span class="text-[10px] font-mono text-silver-dim block"><?php echo e($part->sku); ?></span>
                                <h4 class="font-heading text-xs font-bold text-silver-bright mt-1"><?php echo e($part->name); ?></h4>
                                <p class="text-[11px] text-silver-dim mt-0.5"><?php echo e($part->category?->name); ?></p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-gunmetal/40 flex items-center justify-between">
                                <span class="font-mono text-xs text-giallo font-bold">Rp <?php echo e(number_format($part->price_idr, 0, ',', '.')); ?></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded <?php echo e($part->stock > 0 ? 'bg-emerald-950/60 text-emerald-400' : 'bg-red-950/60 text-red-400'); ?>">
                                    Stok: <?php echo e($part->stock); ?>

                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full panel border border-gunmetal/70 bg-carbon p-6 text-center text-silver-dim">
                            Tidak ada spare part aktif.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        
        <div class="space-y-4">

            
            <div x-show="activeTab === 'work-orders'" class="panel border border-gunmetal bg-carbon p-5 space-y-4">
                <div class="border-b border-gunmetal/40 pb-3 flex items-center justify-between">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-giallo">
                        Kasir Tagihan Servis (WO)
                    </h3>
                    <template x-if="selectedWo">
                        <span class="font-mono text-xs font-bold text-silver-bright" x-text="'#' + selectedWo.code"></span>
                    </template>
                </div>

                <template x-if="!selectedWo">
                    <div class="text-center py-10 text-silver-dim text-xs space-y-2">
                        <p class="text-2xl">📋</p>
                        <p>Pilih salah satu Work Order di tabel sebelah kiri untuk memproses pembayaran kasir.</p>
                    </div>
                </template>

                <template x-if="selectedWo">
                    <form :action="'<?php echo e(url('admin/pos/work-orders')); ?>/' + selectedWo.id + '/pay'" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>

                        
                        <div class="bg-steel/20 border border-gunmetal/40 p-3 rounded space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-silver-dim">Pelanggan:</span>
                                <span class="font-bold text-silver-bright" x-text="selectedWo.customer_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-silver-dim">Telepon:</span>
                                <span class="font-mono text-silver-bright" x-text="selectedWo.customer_phone"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-silver-dim">Kendaraan:</span>
                                <span class="font-semibold text-giallo" x-text="selectedWo.vehicle"></span>
                            </div>
                        </div>

                        
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            <p class="text-[10px] uppercase tracking-wider text-silver-dim font-heading">Rincian Jasa & Spare Part:</p>
                            <template x-for="(item, idx) in selectedWo.items" :key="idx">
                                <div class="flex justify-between text-xs py-1 border-b border-gunmetal/20 font-mono">
                                    <div>
                                        <span class="text-silver-bright" x-text="item.desc"></span>
                                        <span class="text-[10px] text-silver-dim block" x-text="item.qty + ' x Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                    </div>
                                    <span class="text-silver-bright font-semibold" x-text="'Rp ' + Number(item.subtotal).toLocaleString('id-ID')"></span>
                                </div>
                            </template>
                        </div>

                        
                        <div>
                            <label class="block text-[11px] uppercase text-silver-dim mb-1 font-heading">Diskon Khusus (IDR)</label>
                            <input type="number"
                                   name="discount_idr"
                                   x-model.number="discountWo"
                                   min="0"
                                   :max="selectedWo.subtotal"
                                   placeholder="0"
                                   class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 font-mono focus:border-giallo focus:outline-none">
                        </div>

                        
                        <div>
                            <label class="block text-[11px] uppercase text-silver-dim mb-1 font-heading">Metode Pembayaran</label>
                            <select name="payment_method"
                                    x-model="paymentMethodWo"
                                    required
                                    class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 font-heading uppercase focus:border-giallo focus:outline-none">
                                <option value="cash">💵 Tunai (Cash)</option>
                                <option value="card">💳 Kartu Debit / Kredit</option>
                                <option value="transfer">🏦 Bank Transfer</option>
                                <option value="ewallet">📱 QRIS / E-Wallet</option>
                            </select>
                        </div>

                        
                        <div class="pt-3 border-t border-gunmetal space-y-1.5 font-mono text-xs">
                            <div class="flex justify-between text-silver-dim">
                                <span>Subtotal WO:</span>
                                <span x-text="'Rp ' + Number(selectedWo.subtotal).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between text-verde" x-show="discountWo > 0">
                                <span>Diskon:</span>
                                <span x-text="'- Rp ' + Number(discountWo).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex justify-between text-silver-dim">
                                <span>PPN (11%):</span>
                                <span x-text="'Rp ' + Number(woTax).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="pt-2 border-t border-gunmetal flex justify-between font-bold text-sm text-giallo">
                                <span>TOTAL BAYAR:</span>
                                <span x-text="'Rp ' + Number(woTotal).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        
                        <button type="submit"
                                class="w-full py-3.5 bg-giallo hover:bg-arancio text-nero font-heading text-xs uppercase font-bold tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium">
                            ✓ Konfirmasi Pembayaran Work Order
                        </button>
                    </form>
                </template>
            </div>

            
            <div x-show="activeTab === 'parts'" class="panel border border-gunmetal bg-carbon p-5 space-y-4">
                <div class="border-b border-gunmetal/40 pb-3 flex items-center justify-between">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-giallo">
                        Keranjang Kasir
                    </h3>
                    <span class="font-mono text-xs text-silver-dim" x-text="cart.length + ' item'"></span>
                </div>

                <form action="<?php echo e(route('admin.pos.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <template x-if="cart.length === 0">
                        <div class="text-center py-10 text-silver-dim text-xs space-y-2">
                            <p class="text-2xl">🛒</p>
                            <p>Keranjang kosong. Klik spare part dari katalog untuk menambah item.</p>
                        </div>
                    </template>

                    <template x-if="cart.length > 0">
                        <div class="space-y-4">
                            
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                <template x-for="(item, idx) in cart" :key="item.id">
                                    <div class="flex items-center justify-between text-xs py-2 border-b border-gunmetal/30 font-mono">
                                        <input type="hidden" :name="'items[' + idx + '][spare_part_id]'" :value="item.id">
                                        <div class="flex-1">
                                            <p class="text-silver-bright font-bold" x-text="item.name"></p>
                                            <span class="text-[10px] text-silver-dim" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="number"
                                                   :name="'items[' + idx + '][qty]'"
                                                   x-model.number="item.qty"
                                                   min="1"
                                                   :max="item.stock"
                                                   class="w-14 bg-steel/60 border border-gunmetal text-center text-silver-bright text-xs p-1 rounded font-mono">
                                            <button type="button" @click="removeFromCart(idx)" class="text-danger hover:text-red-400 text-sm px-1">✕</button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            
                            <div>
                                <label class="block text-[11px] uppercase text-silver-dim mb-1 font-heading">Metode Pembayaran</label>
                                <select name="payment_method" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 font-heading uppercase focus:border-giallo focus:outline-none">
                                    <option value="cash">💵 Tunai (Cash)</option>
                                    <option value="card">💳 Kartu Debit / Kredit</option>
                                    <option value="transfer">🏦 Bank Transfer</option>
                                    <option value="ewallet">📱 QRIS / E-Wallet</option>
                                </select>
                            </div>

                            
                            <div class="pt-3 border-t border-gunmetal space-y-1.5 font-mono text-xs">
                                <div class="flex justify-between text-silver-dim">
                                    <span>Subtotal:</span>
                                    <span x-text="'Rp ' + Number(cartSubtotal).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-silver-dim">
                                    <span>PPN (11%):</span>
                                    <span x-text="'Rp ' + Number(cartTax).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="pt-2 border-t border-gunmetal flex justify-between font-bold text-sm text-giallo">
                                    <span>TOTAL BAYAR:</span>
                                    <span x-text="'Rp ' + Number(cartTotal).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full py-3.5 bg-giallo hover:bg-arancio text-nero font-heading text-xs uppercase font-bold tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium">
                                ✓ Bayar Sekarang
                            </button>
                        </div>
                    </template>
                </form>
            </div>

            
            <div class="panel border border-gunmetal/60 bg-carbon p-4 space-y-3">
                <h4 class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">
                    Transaksi Kasir Terakhir
                </h4>
                <div class="space-y-2 font-mono text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-silver-dim hover:text-silver-bright py-1 border-b border-gunmetal/20">
                            <div>
                                <span class="text-silver-bright font-bold">#<?php echo e($tx->code); ?></span>
                                <span class="block text-[10px] text-silver-dim"><?php echo e($tx->customer?->user?->name ?? 'Walk-in'); ?></span>
                            </div>
                            <div class="text-right">
                                <span class="text-giallo font-bold">Rp <?php echo e(number_format($tx->total_idr, 0, ',', '.')); ?></span>
                                <a href="<?php echo e(route('admin.pos.receipt', $tx)); ?>" class="block text-[10px] text-silver-dim hover:text-giallo underline">Struk</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-[10px] text-silver-dim">Belum ada transaksi.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/pos/index.blade.php ENDPATH**/ ?>