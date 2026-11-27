@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran Online')

@section('content')
<div class="space-y-6" x-data="{ image: null, zoom: 1 }">
    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">VERIFIKASI PEMBAYARAN ONLINE</h1>
        <p class="text-xs text-silver-dim mt-1">Cocokkan dengan mutasi rekening sebelum menyetujui.</p>
    </div>

    @forelse($waiting as $payment)
        @php $order = $payment->paymentable; @endphp
        <div class="panel border border-gunmetal bg-carbon p-5 grid md:grid-cols-[180px_1fr] gap-5 text-xs">
            <button type="button" @click="image = '{{ route('payments.proof', $payment) }}'; zoom = 1" title="Perbesar bukti">
                <img src="{{ route('payments.proof', $payment) }}" alt="Bukti transfer {{ $order->code }}" class="w-full h-44 object-cover border border-gunmetal">
            </button>
            <div class="space-y-3">
                <div class="flex flex-wrap justify-between gap-2">
                    <span class="font-mono font-bold text-giallo">#{{ $order->code }}</span>
                    <span class="text-silver-dim">Diunggah {{ $payment->uploaded_at->format('d M Y H:i') }}</span>
                </div>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-silver">
                    <dt class="text-silver-dim">Customer</dt><dd>{{ $order->customer?->user?->name }}</dd>
                    <dt class="text-silver-dim">Tagihan</dt><dd class="font-mono font-bold">Rp {{ number_format($order->total_idr - $order->paid_idr, 0, ',', '.') }}</dd>
                    <dt class="text-silver-dim">Nominal transfer</dt>
                    <dd class="font-mono font-bold {{ $payment->amount_idr < $order->total_idr - $order->paid_idr ? 'text-danger' : 'text-verde' }}">Rp {{ number_format($payment->amount_idr, 0, ',', '.') }}</dd>
                    <dt class="text-silver-dim">Pengirim</dt><dd>{{ $payment->sender_name }}</dd>
                    <dt class="text-silver-dim">Bank tujuan</dt><dd>{{ $payment->destination_bank }}</dd>
                    <dt class="text-silver-dim">Barang</dt><dd>{{ $order->items->map(fn ($i) => "{$i->description} x{$i->qty}")->implode(', ') }}</dd>
                </dl>
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gunmetal/40">
                    <form method="POST" action="{{ route('admin.payment-verifications.approve', $payment) }}" onsubmit="return confirm('Dana sudah masuk mutasi bank?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-verde text-nero font-heading font-bold uppercase rounded">Approve Payment</button>
                    </form>
                    <form method="POST" action="{{ route('admin.payment-verifications.reject', $payment) }}" class="flex flex-1 gap-2">
                        @csrf
                        <input type="text" name="rejection_reason" required maxlength="255" placeholder="Alasan: nominal kurang, bukti buram, ..." class="flex-1 min-w-40 bg-nero border border-gunmetal px-3 py-2 text-silver-bright">
                        <button type="submit" class="px-4 py-2 border border-danger/50 text-danger font-heading font-bold uppercase rounded">Reject Payment</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="panel border border-gunmetal bg-carbon p-8 text-center text-xs text-silver-dim">Tidak ada bukti transfer yang menunggu.</div>
    @endforelse

    @if($history->isNotEmpty())
        <div class="panel border border-gunmetal bg-carbon divide-y divide-gunmetal/40 text-xs">
            <p class="px-5 py-3 font-heading uppercase text-silver-bright">Riwayat Verifikasi</p>
            @foreach($history as $payment)
                <div class="flex flex-wrap justify-between gap-2 px-5 py-2.5">
                    <span class="font-mono text-giallo">#{{ $payment->paymentable?->code }}</span>
                    <span class="text-silver-dim">{{ $payment->verified_at->format('d M H:i') }}</span>
                    <span class="font-mono text-silver">Rp {{ number_format($payment->amount_idr, 0, ',', '.') }}</span>
                    <span class="{{ $payment->rejection_reason ? 'text-danger' : 'text-verde' }}">{{ $payment->rejection_reason ? 'Ditolak: ' . $payment->rejection_reason : 'Disetujui' }}</span>
                    <a href="{{ route('payments.proof', $payment) }}" target="_blank" rel="noopener" class="text-giallo underline">Bukti</a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Zoom bukti transfer --}}
    <div x-show="image" x-cloak @keydown.escape.window="image = null" class="fixed inset-0 z-[60] bg-nero/95 flex flex-col">
        <div class="flex justify-end gap-2 p-3">
            <button type="button" @click="zoom = Math.max(0.5, zoom - 0.5)" class="px-3 py-1 border border-gunmetal text-silver-bright">−</button>
            <button type="button" @click="zoom = Math.min(4, zoom + 0.5)" class="px-3 py-1 border border-gunmetal text-silver-bright">+</button>
            <button type="button" @click="image = null" class="px-3 py-1 border border-gunmetal text-silver-bright">Tutup</button>
        </div>
        <div class="flex-1 overflow-auto p-4">
            <img :src="image" :style="`width: ${zoom * 40}rem; max-width: none`" alt="Bukti transfer" class="mx-auto">
        </div>
    </div>
</div>
@endsection
