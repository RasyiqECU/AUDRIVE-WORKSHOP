@extends('layouts.admin')


@section('title', 'AI Scan Review')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                AI Scan Review
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Review dan validasi hasil analisis AI dari customer
            </p>
        </div>
    </div>


    {{-- Filters --}}
    <div class="panel border border-gunmetal p-4">
        <form method="GET" action="{{ route('admin.ai-scans.index') }}" class="flex flex-wrap gap-3">
            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="manual_review" {{ request('status') === 'manual_review' ? 'selected' : '' }}>Manual Review</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            </select>


            <select name="predicted_class" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Prediksi</option>
                <option value="engine_knocking" {{ request('predicted_class') === 'engine_knocking' ? 'selected' : '' }}>Engine Knocking</option>
                <option value="bearing_abnormal" {{ request('predicted_class') === 'bearing_abnormal' ? 'selected' : '' }}>Bearing Abnormal</option>
                <option value="belt_roller_worn" {{ request('predicted_class') === 'belt_roller_worn' ? 'selected' : '' }}>Belt/Roller Worn</option>
                <option value="exhaust_leak" {{ request('predicted_class') === 'exhaust_leak' ? 'selected' : '' }}>Exhaust Leak</option>
                <option value="idle_unstable" {{ request('predicted_class') === 'idle_unstable' ? 'selected' : '' }}>Idle Unstable</option>
                <option value="transmission_noise" {{ request('predicted_class') === 'transmission_noise' ? 'selected' : '' }}>Transmission Noise</option>
            </select>


            <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Filter
            </button>


            <a href="{{ route('admin.ai-scans.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
                Reset
            </a>
        </form>
    </div>


    {{-- Scans Table --}}
    @if($scans->count() > 0)
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gunmetal bg-steel/20">
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">ID</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tanggal</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Customer</th>
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
                                    <p class="font-mono text-xs text-giallo">#{{ $scan->id }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-mono text-xs text-silver-bright">
                                        {{ $scan->created_at->format('d M Y') }}
                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        {{ $scan->created_at->format('H:i') }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-heading text-xs text-silver-bright">
                                        {{ $scan->customer->user->name }}
                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        {{ $scan->customer->code }}
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
                                    <a href="{{ route('admin.ai-scans.show', $scan) }}" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                                        Review →
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
            <span class="text-5xl text-gunmetal block mb-4">🔍</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">
                Tidak Ada Scan
            </h3>
            <p class="font-body text-xs text-silver-dim">
                Belum ada scan yang sesuai dengan filter
            </p>
        </div>
    @endif
</div>
@endsection



