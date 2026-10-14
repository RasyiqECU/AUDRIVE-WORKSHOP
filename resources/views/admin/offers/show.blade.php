@extends('layouts.admin')

@section('title', 'Detail Penawaran #' . $offer->id)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{ showRejectModal: false }">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-800 text-emerald-400 rounded text-xs flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-950/60 border border-red-800 text-red-400 rounded text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div>
        <a href="{{ route('admin.offers.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition">
            ← Kembali ke Daftar Offers
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs text-giallo font-mono">#OFR-{{ str_pad($offer->id, 5, '0', STR_PAD_LEFT) }}</span>
                <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                    REVIEW PENAWARAN HARGA MOBIL
                </h1>
            </div>
            <div>
                @php
                    $st = $offer->status instanceof \App\Enums\OfferStatus ? $offer->status->value : (string)$offer->status;
                @endphp
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    @if($st === 'accepted') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    @elseif($st === 'rejected') bg-red-950/60 text-red-400 border border-red-800
                    @else bg-amber-950/60 text-amber-400 border border-amber-800 @endif">
                    {{ $offer->status instanceof \App\Enums\OfferStatus ? $offer->status->label() : ucfirst($st) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Banner Status Accepted + Link to POS --}}
    @if($st === 'accepted')
        <div class="panel border border-emerald-800/80 bg-emerald-950/30 p-5 rounded space-y-3">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-heading text-sm uppercase tracking-wider text-emerald-400 font-bold flex items-center gap-2">
                        <span>✓</span> Penawaran Diterima & Mobil Di-reserve
                    </h3>
                    <p class="text-xs text-silver-dim mt-1">
                        Unit telah dikunci (reserved) untuk calon pembeli. Sales Order resmi telah dibuat secara otomatis.
                    </p>
                    @if($salesOrder)
                        <div class="mt-2 text-xs font-mono space-y-0.5">
                            <span class="text-silver-bright font-bold">No. Sales Order: #{{ $salesOrder->code }}</span>
                            <span class="text-silver-dim block">Status Pembayaran:
                                <strong class="{{ $salesOrder->status->value === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ strtoupper($salesOrder->status->value) }}
                                </strong>
                            </span>
                        </div>
                    @endif
                </div>

                @if($salesOrder)
                    @if($salesOrder->status->value === 'paid')
                        <a href="{{ route('admin.pos.receipt', $salesOrder) }}"
                           class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-600 text-white font-heading text-xs uppercase font-bold tracking-wider rounded transition">
                            🧾 Lihat Struk Kasir
                        </a>
                    @else
                        <a href="{{ route('admin.pos.index', ['car_sales_id' => $salesOrder->id]) }}"
                           class="px-4 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading text-xs uppercase font-bold tracking-wider rounded transition shadow-lg shadow-giallo/20 flex items-center gap-2">
                            <span>💳 Bayar di Kasir (POS)</span>
                            <span>→</span>
                        </a>
                    @endif
                @endif
            </div>
        </div>
    @elseif($st === 'rejected')
        <div class="panel border border-red-800/80 bg-red-950/30 p-4 rounded text-xs text-red-300">
            <strong>Penawaran ini telah ditolak.</strong>
            @if($offer->note && str_contains($offer->note, 'Alasan penolakan:'))
                <p class="mt-1 font-mono text-silver-dim">{{ $offer->note }}</p>
            @endif
        </div>
    @endif

    {{-- Details Card --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Buyer Info --}}
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
                Informasi Calon Pembeli
            </h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-silver-dim">Nama:</span>
                    <span class="text-silver-bright font-bold">{{ $offer->customer?->user?->name ?? 'Guest User' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Email:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->customer?->user?->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">No. Telepon:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->customer?->phone ?? $offer->customer?->user?->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Tanggal Penawaran:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($offer->expires_at)
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Batas Waktu:</span>
                        <span class="text-silver-dim font-mono">{{ $offer->expires_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif
            </div>

            @if($offer->note)
                <div class="pt-3 border-t border-gunmetal/30">
                    <span class="text-[10px] text-silver-dim uppercase font-heading block mb-1">Catatan dari Customer:</span>
                    <p class="text-xs text-silver-muted bg-steel/20 p-2.5 rounded border border-gunmetal/30 font-sans italic">
                        "{{ $offer->note }}"
                    </p>
                </div>
            @endif
        </div>

        {{-- Unit Mobil & Harga --}}
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
                Unit Mobil & Harga
            </h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-silver-dim">Unit Mobil:</span>
                    <span class="text-silver-bright font-bold">{{ $offer->carUnit?->full_name ?? ($offer->carUnit?->brand . ' ' . $offer->carUnit?->model) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Kode Unit:</span>
                    <span class="text-silver-bright font-mono font-bold">{{ $offer->carUnit?->code ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Status Unit:</span>
                    <span class="text-silver-bright font-semibold uppercase">{{ $offer->carUnit?->status?->label() ?? 'Available' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Harga Listing:</span>
                    <span class="text-silver-dim line-through font-mono">Rp {{ number_format($offer->carUnit?->price_sell_idr ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gunmetal/30">
                    <span class="text-silver-dim font-heading uppercase text-[11px]">Harga Penawaran:</span>
                    <span class="text-giallo font-mono text-xl font-bold">Rp {{ number_format($offer->amount_idr, 0, ',', '.') }}</span>
                </div>
                @php
                    $diff = ($offer->carUnit?->price_sell_idr ?? 0) - $offer->amount_idr;
                @endphp
                @if($diff != 0)
                    <div class="flex justify-between text-[11px] font-mono">
                        <span class="text-silver-dim">Selisih Penawaran:</span>
                        <span class="{{ $diff > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                            {{ $diff > 0 ? '-' : '+' }} Rp {{ number_format(abs($diff), 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            </div>

            @if($offer->carUnit)
                <div class="pt-2">
                    <a href="{{ route('admin.cars.show', $offer->carUnit->id) }}" class="text-xs text-giallo hover:underline inline-flex items-center gap-1 font-heading uppercase">
                        Lihat Detail Unit Mobil →
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ACTION BUTTONS IF PENDING --}}
    @if($st === 'pending')
        <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright border-b border-gunmetal/40 pb-2">
                Respon & Keputusan Penawaran
            </h3>
            <p class="text-xs text-silver-dim">
                Menerima penawaran akan otomatis menandai unit sebagai <strong>Dipesan (Reserved)</strong> dan membuat <strong>Sales Order</strong> sehingga kasir dapat memproses transaksi pembayaran mobil.
            </p>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                {{-- Accept Form --}}
                <form action="{{ route('admin.offers.accept', $offer->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin menerima penawaran ini sebesar Rp {{ number_format($offer->amount_idr, 0, ',', '.') }}? Unit mobil akan di-reserve.')">
                    @csrf
                    <button type="submit"
                            class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-heading text-xs uppercase font-bold tracking-wider rounded transition flex items-center gap-2 shadow-lg shadow-emerald-900/30">
                        <span>✓</span> Terima Penawaran (Accept)
                    </button>
                </form>

                {{-- Reject Button triggering modal --}}
                <button type="button" @click="showRejectModal = true"
                        class="px-5 py-3 bg-steel/60 hover:bg-steel border border-red-800/60 text-red-400 hover:text-red-300 font-heading text-xs uppercase font-bold tracking-wider rounded transition flex items-center gap-2">
                    <span>✕</span> Tolak Penawaran
                </button>
            </div>
        </div>

        {{-- REJECT MODAL --}}
        <div x-show="showRejectModal"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-nero/80 backdrop-blur-sm p-4">
            <div class="panel border border-gunmetal bg-carbon max-w-md w-full p-6 space-y-4 shadow-2xl"
                 @click.away="showRejectModal = false">
                <h3 class="font-heading text-sm uppercase tracking-wider text-red-400 font-bold">
                    Konfirmasi Penolakan Penawaran
                </h3>
                <p class="text-xs text-silver-dim">
                    Apakah Anda yakin menolak penawaran harga sebesar <strong>Rp {{ number_format($offer->amount_idr, 0, ',', '.') }}</strong> untuk unit {{ $offer->carUnit?->full_name }}?
                </p>

                <form action="{{ route('admin.offers.reject', $offer->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] uppercase text-silver-dim mb-1 font-heading">Alasan Penolakan (Opsional)</label>
                        <textarea name="reason" rows="3" placeholder="Contoh: Harga penawaran terlalu jauh dari target margin."
                                  class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-red-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2 border border-gunmetal text-silver-dim text-xs rounded hover:bg-steel transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-700 hover:bg-red-600 text-white font-heading text-xs uppercase font-bold rounded transition">
                            Tolak Penawaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
