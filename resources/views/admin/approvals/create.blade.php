@extends('layouts.admin')

@section('title', 'Ajukan Approval')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pengajuan Persetujuan</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                AJUKAN APPROVAL
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Ajukan keputusan yang berdampak finansial ke Manager. Pengajuan Anda akan dinilai bersama catatan yang jelas.
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
            <span>&larr;</span> Kembali
        </a>
    </div>

    @if(session('success'))
        <x-ui.alert type="success" :message="session('success')"/>
    @endif
    @if(session('error'))
        <x-ui.alert type="danger" :message="session('error')"/>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="panel border border-danger/40 bg-danger/10 p-4 text-xs text-danger">
            @foreach($errors->all() as $err) <p>• {{ $err }}</p> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.approvals.store') }}" class="panel border border-gunmetal bg-carbon p-6 space-y-5">
        @csrf

        {{-- Tipe Pengajuan --}}
        <div>
            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Tipe Pengajuan</label>
            <select id="approvalType" name="type" class="w-full bg-nero border border-gunmetal px-3 py-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none" required>
                @foreach($allowedTypes as $t)
                    <option value="{{ $t }}">{{ match ($t) {
                        'discount' => 'Diskon Transaksi',
                        'refund' => 'Refund Pembayaran',
                        'price_adjustment' => 'Koreksi Harga Spare Part',
                        'stock_adjustment' => 'Penyesuaian Stok',
                        default => ucfirst(str_replace('_', ' ', $t)),
                    } }}</option>
                @endforeach
            </select>
            <p id="typeHint" class="text-[10px] text-silver-dim mt-1.5"></p>
        </div>

        {{-- Discount --}}
        <div data-type-section="discount" class="hidden space-y-3 border border-gunmetal/50 p-4">
            <p class="font-heading text-[10px] uppercase tracking-wider text-giallo">Detail Diskon</p>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Pesanan Terdampak</label>
                <select name="sales_order_id" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">— Pilih Sales Order —</option>
                    @foreach($salesOrders as $order)
                        <option value="{{ $order->id }}">
                            #{{ $order->code }} — {{ $order->customer?->name ?? '-' }} (Rp {{ number_format($order->total_idr, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Nominal Diskon (IDR)</label>
                <input type="number" name="amount_idr" min="1" step="1000" placeholder="mis. 500000" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
            </div>
        </div>

        {{-- Refund --}}
        <div data-type-section="refund" class="hidden space-y-3 border border-gunmetal/50 p-4">
            <p class="font-heading text-[10px] uppercase tracking-wider text-giallo">Detail Refund</p>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Pembayaran yang Direfund</label>
                <select name="payment_id" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">— Pilih Pembayaran Berhasil —</option>
                    @foreach($payments as $payment)
                        <option value="{{ $payment->id }}">
                            #{{ $payment->reference ?? $payment->id }} — {{ $payment->paymentable?->code ?? $payment->paymentable_id }} — Rp {{ number_format($payment->amount_idr, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Nominal Refund (IDR)</label>
                <input type="number" name="amount_idr" min="1" step="1000" placeholder="mis. 250000" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
            </div>
        </div>

        {{-- Price Adjustment --}}
        <div data-type-section="price_adjustment" class="hidden space-y-3 border border-gunmetal/50 p-4">
            <p class="font-heading text-[10px] uppercase tracking-wider text-giallo">Koreksi Harga</p>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Spare Part</label>
                <select name="spare_part_id" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">— Pilih Spare Part —</option>
                    @foreach($spareParts as $part)
                        <option value="{{ $part->id }}">{{ $part->sku }} — {{ $part->name }} (Rp {{ number_format($part->price_idr, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Harga Baru (IDR)</label>
                <input type="number" name="new_price_idr" min="0" step="1000" placeholder="mis. 1250000" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
            </div>
        </div>

        {{-- Stock Adjustment --}}
        <div data-type-section="stock_adjustment" class="hidden space-y-3 border border-gunmetal/50 p-4">
            <p class="font-heading text-[10px] uppercase tracking-wider text-giallo">Penyesuaian Stok</p>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Spare Part</label>
                <select name="spare_part_id" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">— Pilih Spare Part —</option>
                    @foreach($spareParts as $part)
                        <option value="{{ $part->id }}">{{ $part->sku }} — {{ $part->name }} (stok: {{ $part->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Stok Riil Hasil Hitung Fisik (unit)</label>
                <input type="number" name="new_stock" min="0" step="1" placeholder="mis. 12" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none" />
            </div>
        </div>

        {{-- Catatan --}}
        <div>
            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Alasan / Catatan Pengajuan</label>
            <textarea name="notes" rows="3" required maxlength="500" placeholder="Jelaskan alasan pengajuan, e.g. komplain customer, hasil stock opname, dsb." class="w-full bg-nero border border-gunmetal px-3 py-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none resize-none"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gunmetal/40">
            <a href="{{ url()->previous() }}" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading text-xs font-bold uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20">
                Kirim Pengajuan ke Manager
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const hints = {
        discount: 'Gunakan untuk perintah diskon yang melebihi wewenang Anda. Manager yang menyetujui nominalnya.',
        refund: 'Gunakan untuk void/refund pembayaran customer (mis. pembatalan servis, salah input kasir).',
        price_adjustment: 'Koreksi harga jual katalog spare part. Pastikan dasar koreksinya jelas.',
        stock_adjustment: 'Setelah stock opname/update fisik. Sistem akan mencatat movement "Adjustment".',
    };

    const typeSelect = document.getElementById('approvalType');
    if (!typeSelect) return;

    function refresh() {
        const t = typeSelect.value;
        document.querySelectorAll('[data-type-section]').forEach(function (el) {
            const active = el.getAttribute('data-type-section') === t;
            el.classList.toggle('hidden', !active);
            // Field pada section non-aktif di-suppress agar tidak ikut ter-submit
            el.querySelectorAll('input, select, textarea').forEach(function (inner) {
                inner.disabled = !active;
            });
        });
        const hint = document.getElementById('typeHint');
        if (hint) hint.textContent = hints[t] || '';
    }

    typeSelect.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
