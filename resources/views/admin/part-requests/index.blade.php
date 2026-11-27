@extends('layouts.admin')

@section('title', 'Usulan Suku Cadang')

@section('content')
@php $role = auth()->user()->role; @endphp
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">USULAN SUKU CADANG</h1>
        <p class="text-xs text-silver-dim mt-1">Gudang cek stok &amp; harga, lalu Sales kirim rincian lewat WhatsApp dan catat pilihan customer.</p>
    </div>

    @forelse($workOrders as $workOrder)
        @php
            $customerUser = $workOrder->vehicle?->customer?->user;
            $phone = preg_replace('/\D/', '', (string) $customerUser?->phone);
            $phone = str_starts_with($phone, '0') ? '62' . substr($phone, 1) : $phone;
            $lines = $workOrder->items->map(fn ($i) => "- {$i->description} x{$i->qty}: Rp " . number_format($i->subtotal_idr, 0, ',', '.'));
            $message = "Halo {$customerUser?->name}, pada servis {$workOrder->code} kami menemukan kebutuhan suku cadang:\n"
                . $lines->implode("\n")
                . "\n\nPilihan Anda per item: (1) beli di bengkel, (2) bawa sendiri (hanya jasa pasang, tanpa garansi part), atau (3) tidak diganti.";
        @endphp
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs">
                    <p class="font-mono font-bold text-giallo">{{ $workOrder->code }}</p>
                    <p class="text-silver-dim">{{ $customerUser?->name ?? 'Customer' }} &bull; {{ $workOrder->vehicle?->brand }} {{ $workOrder->vehicle?->model }} &bull; Kepala Mekanik: {{ $workOrder->mechanic?->name ?? '-' }}</p>
                </div>
                @if(in_array($role, ['super-admin', 'sales'], true) && $phone && $workOrder->items->every(fn ($i) => $i->inventory_checked_at))
                    <a href="https://wa.me/{{ $phone }}?text={{ rawurlencode($message) }}" target="_blank" rel="noopener"
                       class="px-4 py-2 bg-verde text-nero font-heading font-bold text-xs uppercase rounded">Chat WhatsApp Customer</a>
                @endif
            </div>

            @foreach($workOrder->items as $item)
                <div class="border-t border-gunmetal/40 pt-3 space-y-2 text-xs">
                    <div class="flex flex-wrap justify-between gap-2">
                        <span class="text-silver-bright">{{ $item->description }} x{{ $item->qty }}</span>
                        <span class="font-mono text-silver-dim">
                            Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }} &bull; Stok: {{ $item->sparePart?->available_stock ?? 0 }}
                            &bull; {{ $item->inventory_checked_at ? 'Sudah dicek gudang' : 'Menunggu cek gudang' }}
                        </span>
                    </div>

                    @if(! $item->inventory_checked_at && in_array($role, ['super-admin', 'inventory'], true))
                        <form method="POST" action="{{ route('admin.part-requests.check', $item) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-giallo text-nero font-heading font-bold text-[10px] uppercase rounded">Stok &amp; Harga Sudah Dicek</button>
                        </form>
                    @endif

                    @if($item->inventory_checked_at && in_array($role, ['super-admin', 'sales'], true))
                        <form method="POST" action="{{ route('admin.part-requests.decide', $item) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <select name="decision" required class="bg-nero border border-gunmetal px-2 py-1.5 text-silver-bright">
                                <option value="">Pilihan customer...</option>
                                <option value="buy">Beli di bengkel</option>
                                <option value="bring_own">Bawa sendiri</option>
                                <option value="skip">Tidak usah ganti</option>
                            </select>
                            <input type="number" name="install_fee_idr" min="0" placeholder="Jasa pasang (bawa sendiri)" class="bg-nero border border-gunmetal px-2 py-1.5 text-silver-bright">
                            <input type="text" name="note" maxlength="255" placeholder="Catatan / alasan tidak ganti" class="flex-1 min-w-48 bg-nero border border-gunmetal px-2 py-1.5 text-silver-bright">
                            <button type="submit" class="px-3 py-1.5 bg-giallo text-nero font-heading font-bold text-[10px] uppercase rounded">Catat</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <div class="panel border border-gunmetal bg-carbon p-8 text-center text-xs text-silver-dim">Tidak ada usulan suku cadang yang menunggu.</div>
    @endforelse
</div>
@endsection
