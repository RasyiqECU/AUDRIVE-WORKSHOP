@extends('layouts.customer')

@section('title', 'Klaim Garansi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-silver-bright tracking-wide">Klaim Garansi</h1>
        <p class="text-xs text-silver-dim mt-2">
            Garansi servis {{ \App\Services\WarrantyClaimService::SERVICE_DAYS }} hari / {{ number_format(\App\Services\WarrantyClaimService::SERVICE_KM, 0, ',', '.') }} km,
            garansi mobil {{ \App\Services\WarrantyClaimService::CAR_MONTHS }} bulan / {{ number_format(\App\Services\WarrantyClaimService::CAR_KM, 0, ',', '.') }} km sejak serah terima.
            Untuk mengajukan, temui Sales kami dengan membawa nomor nota dan mobil Anda.
        </p>
    </div>

    @forelse($claims as $claim)
        <div class="panel border border-gunmetal bg-carbon p-5 text-xs space-y-1">
            <div class="flex flex-wrap justify-between gap-2">
                <span class="font-mono font-bold text-giallo">{{ $claim->code }}</span>
                <span class="text-silver-bright">{{ $claim->statusLabel() }}</span>
            </div>
            <p class="text-silver-dim">{{ $claim->vehicle?->brand }} {{ $claim->vehicle?->model }} &bull; {{ $claim->created_at->format('d M Y') }}</p>
            <p class="text-silver">{{ $claim->complaint }}</p>
            @if($claim->rejection_reason)
                <p class="text-danger">Alasan: {{ $claim->rejection_reason }}</p>
            @endif
        </div>
    @empty
        <div class="panel border border-gunmetal bg-carbon p-8 text-center text-xs text-silver-dim">Belum ada klaim garansi.</div>
    @endforelse

    {{ $claims->links() }}
</div>
@endsection
