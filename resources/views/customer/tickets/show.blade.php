@extends('layouts.customer')

@section('title', 'Detail Tiket #' . $ticket->id)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('customer.tickets.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Tiket
        </a>
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs text-giallo font-mono">#TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide mt-1">
                    {{ $ticket->subject }}
                </h1>
            </div>
            <div>
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    @if($ticket->status === 'open') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    @elseif($ticket->status === 'in_progress') bg-amber-950/60 text-amber-400 border border-amber-800
                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Main Issue --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gunmetal/40 pb-3 text-xs text-silver-dim">
            <span>Dibuat: <strong class="text-silver-bright">{{ $ticket->created_at->format('d M Y, H:i') }}</strong></span>
            <span>Prioritas: <strong class="text-giallo uppercase">{{ $ticket->priority }}</strong></span>
        </div>
        <div class="text-xs text-silver-bright leading-relaxed whitespace-pre-line">
            {{ $ticket->description }}
        </div>
    </div>

    {{-- Reply Form --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Kirim Pesan Tambahan</h3>
        <form action="{{ route('customer.tickets.reply', $ticket->id) }}" method="POST" class="space-y-3">
            @csrf
            <textarea name="message" rows="4" required placeholder="Tulis balasan atau informasi tambahan untuk teknisi support..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none"></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button">
                    Kirim Balasan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
