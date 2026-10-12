@extends('layouts.customer')


@section('title', 'Riwayat AI Scan')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Riwayat AI Scan
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Daftar semua analisis suara mesin yang telah Anda lakukan
            </p>
        </div>
        <a href="{{ route('customer.ai-scan.create') }}" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
            + Scan Baru
        </a>
    </div>


    {{-- Scans List --}}
    @if($scans->count() > 0)
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gunmetal bg-steel/20">
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tanggal</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kendaraan</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Prediksi</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Confidence</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        @foreach($scans as $scan)
                            <tr class="hover:bg-steel/20 transition-premium">
                                <td class="px-4 py-3">
                                    <p class="font-mono text-xs text-silver-bright">
                                        {{ $scan->created_at->format('d M Y') }}
                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        {{ $scan->created_at->format('H:i') }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($scan->vehicle)
                                        <p class="font-heading text-xs text-silver-bright">
                                            {{ $scan->vehicle->brand }} {{ $scan->vehicle->model }}
                                        </p>
                                        <p class="font-mono text-[10px] text-silver-dim">
                                            {{ $scan->vehicle->plate_number }}
                                        </p>
                                    @else
                                        <p class="font-heading text-xs text-silver-dim">-</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($scan->predicted_class)
                                        <p class="font-heading text-xs text-silver-bright">
                                            {{ str_replace('_', ' ', ucfirst($scan->predicted_class)) }}
                                        </p>
                                    @else
                                        <p class="font-heading text-xs text-silver-dim">-</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($scan->confidence)
                                        <span class="font-mono text-xs {{ scan->confidence>=0.8?'text-verde':(scan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}">
                                            {{ round($scan->confidence * 100) }}%
                                        </span>
                                    @else
                                        <span class="font-mono text-xs text-silver-dim">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border {{ $scan->getStatusColor() }} bg-steel/30">
                                        {{ $scan->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('customer.ai-scan.show', $scan) }}" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        {{-- Pagination --}}
        {{ $scans->links() }}
    @else
        <div class="panel border border-gunmetal p-12 text-center">
            <span class="text-5xl text-gunmetal block mb-4">🔊</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">
                Belum Ada Scan
            </h3>
            <p class="font-body text-xs text-silver-dim mb-4">
                Anda belum melakukan analisis suara mesin
            </p>
            <a href="{{ route('customer.ai-scan.create') }}" class="inline-block px-5 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Mulai Scan Pertama
            </a>
        </div>
    @endif
</div>
@endsection



