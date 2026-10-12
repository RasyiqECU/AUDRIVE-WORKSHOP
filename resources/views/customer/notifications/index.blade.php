@extends('layouts.customer')

@section('title', 'Notifikasi')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pemberitahuan</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                NOTIFIKASI SAYA
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Riwayat pemberitahuan booking servis, status pesanan, promo, dan pengingat
            </p>
        </div>

        @if($notifications->count() > 0)
            <form action="{{ route('customer.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    ✓ Tandai Semua Sudah Dibaca
                </button>
            </form>
        @endif
    </div>

    {{-- Notification List --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="divide-y divide-gunmetal/40">
            @forelse($notifications as $notification)
                <div class="p-4 md:p-5 flex items-start justify-between gap-4 {{ $notification->read_at ? 'bg-carbon' : 'bg-steel/20 border-l-2 border-l-giallo' }} hover:bg-steel/30 transition-premium">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ $notification->read_at ? 'bg-steel/40 text-silver-dim' : 'bg-giallo/20 text-giallo' }} flex items-center justify-center shrink-0 mt-0.5 text-sm">
                            🔔
                        </div>
                        <div>
                            <p class="text-xs text-silver-bright font-semibold leading-relaxed">
                                {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Notifikasi baru' }}
                            </p>
                            @if(isset($notification->data['detail']))
                                <p class="text-xs text-silver-dim mt-1">
                                    {{ $notification->data['detail'] }}
                                </p>
                            @endif
                            <p class="font-mono text-[10px] text-silver-dim mt-1.5 flex items-center gap-2">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <span>{{ $notification->created_at->format('d M Y H:i') }}</span>
                            </p>
                        </div>
                    </div>

                    @if(!$notification->read_at)
                        <form action="{{ route('customer.notifications.read', $notification->id) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-2.5 py-1 text-[10px] font-heading uppercase tracking-wider text-giallo hover:text-arancio hover:underline">
                                Tandai Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center text-silver-dim">
                    <span class="text-4xl block mb-2 opacity-50">📬</span>
                    <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Belum Ada Notifikasi</p>
                    <p class="text-xs text-silver-dim mt-1">Notifikasi booking, pesanan, dan aktivitas Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
