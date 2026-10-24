@extends('layouts.customer')

@section('title', 'Tiket Bantuan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Helpdesk & Support</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                TIKET BANTUAN & KELUHAN
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Butuh bantuan terkait servis, garansi, atau konsultasi teknis? Tim dukungan siap melayani Anda.
            </p>
        </div>
        <a href="{{ route('customer.tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium shrink-0">
            🎫 Buat Tiket Baru
        </a>
    </div>

    {{-- Tickets Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">ID Tiket</th>
                        <th class="p-3">Subjek</th>
                        <th class="p-3">Prioritas</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Dibuat</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($tickets as $ticket)
                        @php
                            $statusVal = $ticket->status->value ?? (string) $ticket->status;
                            $priorityVal = $ticket->priority->value ?? (string) $ticket->priority;
                        @endphp
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-silver-bright font-semibold">#TK-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-3 font-sans text-silver-bright font-medium">{{ $ticket->subject }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    @if($priorityVal === 'urgent' || $priorityVal === 'high') bg-red-950/60 text-red-400 border border-red-800
                                    @elseif($priorityVal === 'medium') bg-amber-950/60 text-amber-400 border border-amber-800
                                    @else bg-blue-950/60 text-blue-400 border border-blue-800 @endif">
                                    {{ $priorityVal }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    @if($statusVal === 'open') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @elseif($statusVal === 'in_progress') bg-amber-950/60 text-amber-400 border border-amber-800
                                    @elseif($statusVal === 'resolved') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                                    {{ str_replace('_', ' ', $statusVal) }}
                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]">{{ $ticket->created_at->format('d M Y') }}</td>
                            <td class="p-3 text-right font-sans">
                                <a href="{{ route('customer.tickets.show', $ticket->id) }}" class="text-xs text-giallo hover:underline font-heading uppercase">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-silver-dim font-sans">
                                <p class="text-sm">Belum ada tiket bantuan yang diajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tickets->hasPages())
        <div>
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
