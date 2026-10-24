@extends('layouts.admin')

@section('title', 'Riwayat Servis - ' . ($customer->user->name ?? 'Customer'))

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.index') }}" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <a href="{{ route('admin.customers.show', $customer) }}" class="text-silver-bright font-mono text-xs hover:underline">{{ $customer->code }}</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Riwayat Servis</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                RIWAYAT SERVIS BENGKEL
            </h1>
            <p class="text-sm text-silver-dim mt-1 font-mono">
                Klien: <span class="text-silver-bright font-bold">{{ $customer->user->name ?? 'Pelanggan' }}</span> ({{ $customer->code }})
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
                Kembali ke Profil
            </a>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Layanan Servis</th>
                        <th class="py-3 px-4">Kendaraan</th>
                        <th class="py-3 px-4">Jadwal Kedatangan</th>
                        <th class="py-3 px-4">Catatan Servis</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-bold text-silver-bright uppercase">
                                {{ str_replace('_', ' ', $booking->service_type) }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($booking->vehicle)
                                    <p class="font-bold text-silver-bright">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                                    <p class="text-[10px] font-mono text-giallo">{{ $booking->vehicle->plate_number ?: 'Tanpa Plat' }}</p>
                                @else
                                    <span class="text-silver-dim">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <span class="text-silver-bright">{{ $booking->preferred_date ? $booking->preferred_date->format('d M Y') : '-' }}</span>
                                <span class="block text-[10px]">{{ $booking->preferred_time }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim">
                                {{ $booking->notes ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold {{ $booking->status->value === 'confirmed' ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-steel/40 text-silver-dim' }}">
                                    {{ $booking->status->value ?? $booking->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-silver-dim">Belum ada riwayat pengerjaan servis untuk klien ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
