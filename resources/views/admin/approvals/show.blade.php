@extends('layouts.admin')

@section('title', 'Pengajuan Approval #' . $approval->id)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.approvals.index') }}" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Pusat Approval
        </a>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-3">
            PENGAJUAN APPROVAL #{{ $approval->id }}
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            {{ ucfirst(str_replace('_', ' ', $approval->type)) }} • Diajukan {{ $approval->created_at->format('d M Y, H:i') }}
        </p>
    </div>

    @if(session('success'))
        <x-ui.alert type="success" :message="session('success')"/>
    @endif
    @if(session('error'))
        <x-ui.alert type="danger" :message="session('error')"/>
    @endif

    {{-- Ringkasan --}}
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Pemohon</p>
                <p class="text-silver-bright font-heading font-semibold mt-1">{{ $approval->requester?->name ?? '-' }}</p>
                <p class="font-mono text-[10px] text-silver-dim">{{ $approval->requester?->role ?? '' }} • {{ $approval->requester?->email ?? '' }}</p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</p>
                <p class="mt-1">
                    @if($approval->status->value === 'pending')
                        <span class="px-2.5 py-1 bg-giallo/10 text-giallo font-heading text-[10px] uppercase tracking-wider border border-giallo/40">⏳ Pending</span>
                    @elseif($approval->status->value === 'approved')
                        <span class="px-2.5 py-1 bg-verde/10 text-verde font-heading text-[10px] uppercase tracking-wider border border-verde/40">✓ Disetujui</span>
                    @else
                        <span class="px-2.5 py-1 bg-danger/10 text-danger font-heading text-[10px] uppercase tracking-wider border border-danger/40">✕ Ditolak</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tipe Pengajuan</p>
                <p class="text-silver-bright font-heading font-semibold mt-1 uppercase">{{ str_replace('_', ' ', $approval->type) }}</p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Keputusan</p>
                @if($approval->approver)
                    <p class="text-silver-bright font-heading font-semibold mt-1">{{ $approval->approver->name }}</p>
                    <p class="text-[10px] text-silver-dim">{{ $approval->decided_at?->format('d M Y, H:i') }}</p>
                @else
                    <p class="text-silver-dim mt-1">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Rincian Pengajuan --}}
    <div class="panel border border-giallo/30 bg-carbon p-6 space-y-3">
        <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Rincian Pengajuan</h2>
        <table class="w-full text-xs">
            <tbody class="divide-y divide-gunmetal/30">
                @php
                    $fieldLabels = [
                        'sales_order_id' => 'ID Sales Order',
                        'payment_id' => 'ID Pembayaran',
                        'spare_part_id' => 'ID Spare Part',
                        'amount_idr' => 'Nominal (IDR)',
                        'new_price_idr' => 'Harga Baru (IDR)',
                        'old_price_idr' => 'Harga Lama (IDR)',
                        'new_stock' => 'Stok Baru',
                        'old_stock' => 'Stok Lama',
                        'reason' => 'Alasan Pengajuan',
                    ];
                @endphp
                @forelse($approval->payload ?? [] as $key => $value)
                    <tr>
                        <td class="py-2.5 font-heading text-[10px] uppercase tracking-wider text-silver-dim align-top w-1/3">
                            {{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                        </td>
                        <td class="py-2.5 text-silver-bright font-mono">
                            @if($value === null || $value === '')
                                -
                            @elseif($key === 'reason')
                                <span class="text-silver-bright italic">"{{ $value }}"</span>
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="py-2.5 text-silver-dim">Payload kosong.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($approval->type === 'discount' && isset($approval->payload['sales_order_id']))
            @php $detailOrder = \App\Models\SalesOrder::with('invoice')->find($approval->payload['sales_order_id']); @endphp
            @if($detailOrder?->invoice)
                <a href="{{ route('admin.invoices.show', $detailOrder->invoice->id) }}" class="inline-block font-heading text-[10px] uppercase tracking-wider text-giallo hover:text-arancio">
                    Lihat Invoice Terkait &rarr;
                </a>
            @endif
        @endif
    </div>

    {{-- Aksi Keputusan --}}
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Keputusan</h2>

        @if($approval->isPending())
            @if($approval->requested_by === auth()->id())
                <p class="text-xs text-danger flex items-center gap-2">
                    <span>🚫</span> Anda tidak dapat menyetujui pengajuan yang Anda buat sendiri.
                </p>
            @else
                <div class="space-y-4">
                    <form action="{{ route('admin.approvals.approve', $approval) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Catatan Persetujuan (opsional)</label>
                            <textarea name="notes" rows="2" maxlength="500" placeholder="mis. sesuai SLA, dasar keputusan, dsb." class="w-full bg-nero border border-verde/40 px-3 py-2 text-xs text-silver-bright focus:border-verde focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-verde hover:bg-emerald-600 text-nero font-heading text-xs font-bold uppercase tracking-wider transition-premium">
                            ✓ Setujui Pengajuan
                        </button>
                    </form>

                    <form action="{{ route('admin.approvals.reject', $approval) }}" method="POST" class="space-y-3 pt-3 border-t border-gunmetal/40">
                        @csrf
                        <div>
                            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Alasan Penolakan <span class="text-danger">(wajib)</span></label>
                            <textarea name="notes" rows="2" required maxlength="500" placeholder="Tuliskan dasar penolakan agar pengaju memahami." class="w-full bg-nero border border-danger/40 px-3 py-2 text-xs text-silver-bright focus:border-danger focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-danger hover:bg-red-600 text-white font-heading text-xs font-bold uppercase tracking-wider transition-premium">
                            ✕ Tolak Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        @else
            @if($approval->reason)
                <p class="text-xs text-silver-dim italic">Catatan keputusan: "{{ $approval->reason }}"</p>
            @endif
            <p class="text-xs text-silver-dim">Pengajuan ini telah diputuskan dan tidak dapat diubah. Pembuat pengajuan perlu mengajukan baru bila diperlukan.</p>
        @endif
    </div>
</div>
@endsection
