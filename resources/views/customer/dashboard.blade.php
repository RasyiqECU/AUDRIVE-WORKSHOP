@extends('layouts.customer')


@section('title', 'Dashboard')


@section('content')
<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Selamat Datang</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright tracking-wide mt-1">{{ $customer->user->name }}</h1>
        <p class="font-mono text-xs text-silver-dim mt-1">Customer ID: {{ $customer->code }}</p>
    </div>


    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-ui.stat-card label="Kendaraan" :value="$customer->vehicles_count" icon="🚗" color="giallo" />
        <x-ui.stat-card label="Booking Aktif" :value="$activeBookings->count()" icon="📅" color="arancio" />
        <x-ui.stat-card label="Loyalty Point" :value="number_format($customer->loyalty_points, 0, ',', '.')" icon="⭐" color="verde" />
        <x-ui.stat-card label="Voucher Aktif" :value="$activeVouchers->count()" icon="🎁" color="silver" />
    </div>


    {{-- Quick Actions --}}
    <div>
        <h2 class="font-heading text-xs uppercase tracking-[0.2em] text-silver-bright mb-3">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('workshop.booking.create') }}" class="bg-carbon border border-arancio/30 hover:border-arancio p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🔧</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Booking Servis</span>
            </a>
            <a href="{{ route('workshop.ai-scan.create') }}" class="bg-carbon border border-verde/30 hover:border-verde p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🔊</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">AI Sound Scan</span>
            </a>
            <a href="{{ route('marketplace.index') }}" class="bg-carbon border border-giallo/30 hover:border-giallo p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">🏎</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Jelajahi Mobil</span>
            </a>
            <a href="{{ route('parts.index') }}" class="bg-carbon border border-silver/20 hover:border-silver p-4 text-center transition-premium">
                <span class="text-2xl block mb-2">⚙️</span>
                <span class="font-heading text-[10px] text-silver-bright uppercase tracking-wider">Beli Spare Part</span>
            </a>
        </div>
    </div>


    {{-- Active Bookings & Notifications --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- Active Bookings --}}
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Booking Aktif</h3>
                <a href="{{ route('workshop.booking.index') }}" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
            </div>
            <div class="p-4 space-y-3">
                @forelse($activeBookings as $booking)
                    <div class="bg-steel/20 border border-gunmetal/30 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-heading text-xs text-silver-bright">{{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}</span>
                            <x-ui.badge type="info">{{ ucfirst($booking->status->label()) }}</x-ui.badge>
                        </div>
                        <p class="font-mono text-[10px] text-silver-dim">{{ $booking->preferred_date->format('d M Y') }}, {{ $booking->preferred_time }}</p>
                        <p class="font-body text-[11px] text-silver-dim mt-1">{{ $booking->vehicle->full_name }}</p>
                    </div>
                @empty
                    <x-ui.empty-state icon="📅" title="Tidak ada booking aktif" description="Booking servis Anda akan muncul di sini." />
                @endforelse
            </div>
        </div>


        {{-- Notifications --}}
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Notifikasi</h3>
                <a href="{{ route('customer.notifications.index') }}" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
            </div>
            <div class="p-4 space-y-2">
                @forelse($notifications as $notification)
                    <div class="flex items-start gap-2 bg-steel/20 border border-gunmetal/30 p-2.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-giallo mt-1.5 shrink-0"></div>
                        <div>
                            <p class="font-body text-[11px] text-silver-bright">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                            <p class="font-mono text-[9px] text-silver-dim mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state icon="🔔" title="Tidak ada notifikasi" description="Notifikasi baru akan muncul di sini." />
                @endforelse
            </div>
        </div>
    </div>


    {{-- Recent AI Scans --}}
    @if($recentScans->count() > 0)
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Scan AI Terbaru</h3>
            <a href="#" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">Lihat Semua</a>
        </div>
        <div class="p-4">
            <div class="space-y-3">
                @foreach($recentScans as $scan)
                    <div class="flex items-center justify-between bg-steel/20 border border-gunmetal/30 p-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔊</span>
                            <div>
                                <p class="font-heading text-xs text-silver-bright">{{ str_replace('_', ' ', ucfirst($scan->predicted_class ?? 'Menunggu hasil')) }}</p>
                                <p class="font-mono text-[10px] text-silver-dim">{{ $scan->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        @if($scan->confidence)
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-steel/50 overflow-hidden">
                                    <div class="h-full {{ $scan->confidence >= 0.8 ? 'bg-verde' : 'bg-arancio' }}" style="width: {{ $scan->confidence * 100 }}%"></div>
                                </div>
                                <span class="font-mono text-[10px] text-silver-dim">{{ round($scan->confidence * 100) }}%</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection



