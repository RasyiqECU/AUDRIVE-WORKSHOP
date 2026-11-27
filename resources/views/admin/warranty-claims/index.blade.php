@extends('layouts.admin')

@section('title', 'Klaim Garansi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">KLAIM GARANSI</h1>
        @if(in_array(auth()->user()->role, ['super-admin', 'sales'], true))
            <a href="{{ route('admin.warranty-claims.create') }}" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase rounded">+ Buat Klaim</a>
        @endif
    </div>

    <div class="panel border border-gunmetal bg-carbon divide-y divide-gunmetal/40">
        @forelse($claims as $claim)
            <a href="{{ route('admin.warranty-claims.show', $claim) }}" class="flex flex-wrap justify-between gap-2 p-4 text-xs hover:bg-steel/20">
                <span class="font-mono font-bold text-giallo">{{ $claim->code }}</span>
                <span class="text-silver-bright">{{ $claim->customer?->user?->name }} &bull; {{ $claim->vehicle?->brand }} {{ $claim->vehicle?->model }}</span>
                <span class="text-silver-dim">{{ $claim->source_type === 'car' ? 'Pembelian mobil' : 'Servis' }}</span>
                <span class="text-silver">{{ $claim->statusLabel() }}</span>
            </a>
        @empty
            <p class="p-8 text-center text-xs text-silver-dim">Belum ada klaim garansi.</p>
        @endforelse
    </div>

    {{ $claims->links() }}
</div>
@endsection
