@extends('layouts.admin')

@section('title', 'Manajemen Booking Servis')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                BOOKING SERVIS WORKSHOP
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Daftar antrian booking servis berkala & perbaikan supercar dari pelanggan
            </p>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Kendaraan</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4">Jadwal Kedatangan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright">{{ $booking->customer->user->name ?? 'Customer' }}</p>
                                <p class="font-mono text-[10px] text-silver-dim">{{ $booking->customer->user->phone ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($booking->vehicle)
                                    <p class="font-bold text-silver-bright">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                                    <span class="font-mono text-[10px] text-giallo">{{ $booking->vehicle->plate_number ?: 'Tanpa Plat' }}</span>
                                @else
                                    <span class="text-silver-dim">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-heading uppercase tracking-wider text-silver-bright">
                                {{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <p class="text-silver-bright">{{ $booking->preferred_date ? $booking->preferred_date->format('d M Y') : '-' }}</p>
                                <p class="text-silver-dim text-[10px]">{{ $booking->preferred_time }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-ui.status-pill :status="$booking->status->value ?? $booking->status" />
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="{{ route('admin.service-bookings.show', $booking) }}" class="px-3 py-1.5 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail / Konversi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Belum ada antrian booking servis
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
