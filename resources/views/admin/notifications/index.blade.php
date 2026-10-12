@extends('layouts.admin')

@section('title', 'Notifikasi Admin')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pusat Informasi Sistem</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                NOTIFIKASI MASUK (ADMIN)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Pantau pesanan baru, booking servis masuk, persetujuan (approval), dan aktivitas sistem AUDrive secara real-time.
            </p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    ✓ Tandai Semua Dibaca ({{ $unreadCount }})
                </button>
            </form>
        @endif
    </div>

    {{-- Notification List --}}
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $isUnread = is_null($notification->read_at);
            @endphp
            <div class="panel border {{ $isUnread ? 'border-giallo/50 bg-carbon/90 shadow-lg' : 'border-gunmetal/60 bg-carbon/50' }} p-5 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $isUnread ? 'bg-giallo/20 text-giallo' : 'bg-steel text-silver-dim' }}">
                        @if(($data['type'] ?? '') === 'service_booking')
                            🔧
                        @elseif(($data['type'] ?? '') === 'order' || ($data['type'] ?? '') === 'spare_part')
                            📦
                        @elseif(($data['type'] ?? '') === 'approval')
                            ⚖️
                        @elseif(($data['type'] ?? '') === 'offer')
                            🏎️
                        @else
                            🔔
                        @endif
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-heading text-sm text-silver-bright font-bold">
                                {{ $data['title'] ?? $data['subject'] ?? 'Notifikasi Sistem AUDrive' }}
                            </h4>
                            @if($isUnread)
                                <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider bg-giallo text-carbon font-bold rounded">
                                    Baru
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-silver-muted leading-relaxed">
                            {{ $data['message'] ?? $data['description'] ?? 'Terdapat aktivitas baru pada sistem.' }}
                        </p>
                        <p class="text-[10px] font-mono text-silver-dim pt-1">
                            {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d M Y, H:i') }})
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                    @if(isset($data['action_url']))
                        <a href="{{ $data['action_url'] }}" class="px-3 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button transition-premium">
                            Lihat Detail →
                        </a>
                    @endif
                    @if($isUnread)
                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="Tandai telah dibaca" class="p-2 text-silver-dim hover:text-silver-bright text-xs">
                                ✓
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="panel border border-gunmetal/70 bg-carbon p-12 text-center text-silver-dim">
                <span class="text-4xl block mb-3 opacity-60">🔔</span>
                <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Belum Ada Notifikasi Masuk</p>
                <p class="text-xs mt-1">Notifikasi aktivitas booking, pesanan, dan persetujuan akan tampil di sini.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
