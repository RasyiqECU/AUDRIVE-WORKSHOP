@extends('layouts.admin')

@section('title', 'Detail Tiket #' . $ticket->code)

@section('breadcrumb')
    <a href="{{ route('admin.tickets.index') }}" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Tiket Layanan</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">#{{ $ticket->code }}</span>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Top Navigation & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
                ← Kembali ke Daftar Tiket
            </a>
            <div class="flex items-center gap-3">
                <span class="font-mono text-lg font-bold text-giallo">#{{ $ticket->code }}</span>
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase
                    @php $p = $ticket->priority->value ?? $ticket->priority; @endphp
                    @if($p === 'high') bg-danger/20 text-danger border border-danger/40
                    @elseif($p === 'medium') bg-amber-500/20 text-amber-400 border border-amber-500/40
                    @else bg-silver-dim/20 text-silver-dim border border-silver-dim/40 @endif">
                    Prioritas {{ $p }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                    @php $s = $ticket->status->value ?? $ticket->status; @endphp
                    @if($s === 'open') bg-amber-500/20 text-amber-300 border border-amber-500/40
                    @elseif($s === 'in_progress') bg-blue-500/20 text-blue-300 border border-blue-500/40
                    @elseif($s === 'resolved') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                    {{ str_replace('_', ' ', $s) }}
                </span>
            </div>
            <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide mt-2">
                {{ $ticket->subject }}
            </h1>
        </div>

        {{-- Quick Status & Assignee Form --}}
        <div class="panel border border-gunmetal/80 bg-carbon p-3 sm:w-80">
            <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="space-y-2">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-heading uppercase text-silver-dim mb-1">Status</label>
                        <select name="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-1.5 focus:border-giallo focus:outline-none">
                            <option value="open" {{ $s === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $s === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $s === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $s === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-heading uppercase text-silver-dim mb-1">Tugaskan Ke</label>
                        <select name="assigned_to" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-1.5 focus:border-giallo focus:outline-none">
                            <option value="">Belum Ditugaskan</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ $ticket->assigned_to == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-1.5 bg-steel hover:bg-gunmetal border border-gunmetal hover:border-giallo text-silver-bright text-[11px] font-heading font-medium rounded transition-colors">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    {{-- Customer Profile Mini Card --}}
    <div class="panel border border-gunmetal/60 bg-carbon p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-steel border border-gunmetal flex items-center justify-center font-bold text-giallo text-sm">
                {{ substr($ticket->customer->user->name ?? 'C', 0, 1) }}
            </div>
            <div>
                <p class="text-xs text-silver-dim">Customer Pelapor:</p>
                <p class="font-bold text-silver-bright text-sm">{{ $ticket->customer->user->name ?? 'Customer' }}</p>
            </div>
        </div>
        <div class="text-xs text-silver-dim space-y-0.5">
            <p>Email: <span class="text-silver-bright">{{ $ticket->customer->user->email ?? '-' }}</span></p>
            <p>Telepon: <span class="text-silver-bright">{{ $ticket->customer->phone ?? '-' }}</span></p>
        </div>
        <div class="text-xs text-silver-dim space-y-0.5">
            <p>Dibuat: <span class="text-silver-bright">{{ $ticket->created_at->format('d M Y, H:i:s') }}</span></p>
            <p>Selesai: <span class="text-silver-bright">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : '-' }}</span></p>
        </div>
    </div>

    {{-- Main Initial Issue --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-3">
        <div class="flex items-center justify-between border-b border-gunmetal/40 pb-3 text-xs text-silver-dim">
            <span class="font-heading uppercase text-giallo tracking-wider font-bold">Deskripsi Masalah Awal</span>
            <span>{{ $ticket->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-xs text-silver-bright leading-relaxed whitespace-pre-line bg-steel/20 p-4 rounded border border-gunmetal/40 font-mono">
            {{ $ticket->description }}
        </div>
    </div>

    {{-- Thread Messages --}}
    <div class="space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-dim px-1">
            Riwayat Percakapan ({{ $ticket->messages->count() }} Balasan)
        </h3>

        @forelse($ticket->messages as $msg)
            <div class="panel p-5 border {{ $msg->sender_type === 'staff' ? 'bg-steel/30 border-giallo/40 ml-4 sm:ml-8' : 'bg-carbon border-gunmetal/80 mr-4 sm:mr-8' }} rounded-lg space-y-2">
                <div class="flex items-center justify-between text-xs border-b border-gunmetal/40 pb-2">
                    <span class="font-bold flex items-center gap-2 {{ $msg->sender_type === 'staff' ? 'text-giallo' : 'text-silver-bright' }}">
                        {{ $msg->sender_type === 'staff' ? '🛡️ Tim Support AUDrive' : '👤 Customer' }}
                        @if($msg->user)
                            <span class="text-[11px] text-silver-dim font-normal font-sans">({{ $msg->user->name }})</span>
                        @endif
                    </span>
                    <span class="text-silver-dim text-[11px] font-mono">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="text-xs text-silver-bright leading-relaxed whitespace-pre-line pt-1">
                    {{ $msg->message }}
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-xs text-silver-dim border border-dashed border-gunmetal rounded">
                Belum ada percakapan lanjutan pada tiket ini. Tulis respon pertama di bawah.
            </div>
        @endforelse
    </div>

    {{-- Staff Reply Form --}}
    <div class="panel border border-giallo/40 bg-carbon p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold">Kirim Balasan Resmi Sebagai Petugas</h3>
            <span class="text-[11px] text-silver-dim">Customer akan menerima notifikasi balasan secara otomatis.</span>
        </div>

        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <textarea name="message" rows="5" required 
                          placeholder="Tuliskan respon, solusi teknis, instruksi kedatangan ke bengkel, atau klarifikasi untuk customer..." 
                          class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none placeholder-silver-dim leading-relaxed"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-gunmetal/40">
                <div class="flex items-center gap-3">
                    <label class="text-xs text-silver-dim font-heading uppercase">Ubah Status Setelah Membalas:</label>
                    <select name="status" class="bg-steel border border-gunmetal text-silver-bright text-xs rounded px-2.5 py-1.5 focus:border-giallo focus:outline-none">
                        <option value="in_progress" {{ $s !== 'resolved' ? 'selected' : '' }}>Tetap / Ubah ke 'In Progress'</option>
                        <option value="resolved">Tandai Selesai ('Resolved')</option>
                        <option value="closed">Tutup Tiket ('Closed')</option>
                    </select>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors">
                    Kirim Balasan ke Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
