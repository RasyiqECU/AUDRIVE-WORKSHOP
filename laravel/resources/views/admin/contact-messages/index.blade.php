@extends('layouts.admin')

@section('title', 'Pesan & Pertanyaan Customer')

@section('breadcrumb')
    <span class="text-giallo font-heading uppercase tracking-wider">Pesan Customer</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">CRM & Lead Management</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                INBOX PERTANYAAN & KONTAK CUSTOMER
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola dan respon pertanyaan dari customer melalui formulir kontak publik AUDrive secara cepat.
            </p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border {{ $unreadCount > 0 ? 'border-giallo/60 bg-carbon' : 'border-gunmetal/60 bg-carbon' }} p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Belum Dibalas</p>
                    <p class="font-display text-2xl font-bold {{ $unreadCount > 0 ? 'text-giallo' : 'text-silver-bright' }} mt-1">
                        {{ $unreadCount }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-giallo/10 border border-giallo/30 flex items-center justify-center text-lg">
                    📩
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Telah Dibalas</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1">
                        {{ $repliedCount }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    ✅
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Pertanyaan</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1">
                        {{ $totalCount }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/30 border border-gunmetal flex items-center justify-center text-lg">
                    📋
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-4">
        <form action="{{ route('admin.contact-messages.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor HP, atau isi pesan..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
            </div>
            <div>
                <select name="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Belum Dibalas / Baru</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                    <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Telah Dibalas</option>
                </select>
            </div>
            <div>
                <select name="topic" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    <option value="">Semua Topik</option>
                    <option value="sales" {{ request('topic') === 'sales' ? 'selected' : '' }}>Sales / Pembelian Mobil</option>
                    <option value="workshop" {{ request('topic') === 'workshop' ? 'selected' : '' }}>Workshop / Servis</option>
                    <option value="parts" {{ request('topic') === 'parts' ? 'selected' : '' }}>Spare Parts</option>
                    <option value="ai" {{ request('topic') === 'ai' ? 'selected' : '' }}>AI Diagnostic</option>
                    <option value="other" {{ request('topic') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="md:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-gunmetal/40">
                <a href="{{ route('admin.contact-messages.index') }}" class="px-3 py-1.5 bg-carbon border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                    Reset
                </a>
                <button type="submit" class="px-4 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button transition-premium">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Messages --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Kontak (WhatsApp / Email)</th>
                        <th class="p-3">Topik Keperluan</th>
                        <th class="p-3">Cuplikan Pesan</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Waktu Masuk</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-steel/20 transition-colors {{ $msg->status === 'unread' ? 'bg-giallo/5' : '' }}">
                            <td class="p-3">
                                <span class="font-heading font-bold text-silver-bright block">{{ $msg->name }}</span>
                                @if($msg->repliedBy)
                                    <span class="text-[10px] text-emerald-400">Dibalas oleh: {{ $msg->repliedBy->name }}</span>
                                @endif
                            </td>
                            <td class="p-3 font-mono text-[11px]">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $msg->phone) }}" target="_blank" class="text-emerald-400 hover:underline block flex items-center gap-1">
                                    <span>💬</span> {{ $msg->phone }}
                                </a>
                                <span class="text-silver-dim text-[10px] block mt-0.5">{{ $msg->email }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider rounded
                                    @if($msg->topic === 'sales') bg-giallo/20 text-giallo border border-giallo/40
                                    @elseif($msg->topic === 'workshop') bg-blue-950/40 text-blue-300 border border-blue-800/40
                                    @elseif($msg->topic === 'parts') bg-purple-950/40 text-purple-300 border border-purple-800/40
                                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                                    {{ strtoupper($msg->topic) }}
                                </span>
                            </td>
                            <td class="p-3 text-silver max-w-xs truncate">
                                {{ Str::limit($msg->message ?? '-', 75) }}
                            </td>
                            <td class="p-3 text-center">
                                @if($msg->status === 'unread')
                                    <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider bg-giallo text-carbon font-bold rounded">
                                        Baru
                                    </span>
                                @elseif($msg->status === 'replied')
                                    <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider bg-emerald-950/60 text-emerald-400 border border-emerald-800 font-bold rounded">
                                        ✓ Dibalas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider bg-steel text-silver-dim rounded">
                                        Dibaca
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center font-mono text-[10px] text-silver-dim whitespace-nowrap">
                                {{ $msg->created_at->diffForHumans() }}
                                <span class="block text-[9px]">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-3 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.contact-messages.show', $msg) }}" class="px-3 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button transition-premium inline-block">
                                    Lihat & Jawab →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-silver-dim">
                                Tidak ada pesan atau pertanyaan customer yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($messages->hasPages())
        <div>{{ $messages->links() }}</div>
    @endif
</div>
@endsection
