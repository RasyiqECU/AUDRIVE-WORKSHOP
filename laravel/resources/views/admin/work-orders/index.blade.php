@extends('layouts.admin')

@section('title', 'Manajemen Work Order Workshop')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN WORK ORDER (WO)
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Pantau progres pengerjaan servis mekanik, pergantian suku cadang, dan rincian biaya
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="{{ route('admin.work-orders.index') }}" class="flex flex-wrap items-center gap-3">
            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan (In Progress)</option>
                <option value="waiting_parts" {{ request('status') === 'waiting_parts' ? 'selected' : '' }}>Menunggu Part</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Diserahkan (Delivered)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
            @if(request('status'))
                <a href="{{ route('admin.work-orders.index') }}" class="text-xs text-silver-dim hover:text-giallo uppercase tracking-wider">Reset</a>
            @endif
        </form>
    </div>

    {{-- Work Orders Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Kode WO</th>
                        <th class="py-3 px-4">Customer & Kendaraan</th>
                        <th class="py-3 px-4">Mekanik</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    @forelse($workOrders as $wo)
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4 font-mono">
                                <span class="font-bold text-giallo">{{ $wo->code }}</span>
                                <span class="block text-[10px] text-silver-dim">{{ $wo->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright">{{ $wo->vehicle?->customer?->user?->name ?? 'Customer' }}</p>
                                <p class="font-mono text-[10px] text-silver-dim">
                                    {{ $wo->vehicle ? "{$wo->vehicle->brand} {$wo->vehicle->model} ({$wo->vehicle->plate_number})" : '-' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($wo->mechanic)
                                    <span class="text-silver-bright font-semibold">👨‍🔧 {{ $wo->mechanic->name }}</span>
                                @else
                                    <span class="text-silver-dim italic">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                Rp {{ number_format($wo->total_idr, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-ui.status-pill :status="$wo->status->value ?? $wo->status" />
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="px-3 py-1.5 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail / Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Belum ada data Work Order
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($workOrders->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                {{ $workOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
