@extends('layouts.public')

@section('title', 'Laporan Inspeksi: ' . $car->full_name . ' — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('marketplace.show', $car->slug) }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Detail {{ $car->full_name }}
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">
            LAPORAN INSPEKSI RESMI 150+ TITIK
        </h1>
        <p class="text-xs text-silver-dim mt-1">Sertifikasi kelayakan teknis unit oleh tim inspektur profesional AUDrive.</p>
    </div>

    @if($inspection)
        <div class="space-y-6">
            <x-marketplace.inspection-panel :inspection="$inspection" />
        </div>
    @else
        <div class="bg-carbon border border-gunmetal rounded-lg p-12 text-center">
            <x-ui.empty-state
                icon="📋"
                title="Laporan Belum Diunggah"
                description="Laporan inspeksi untuk kendaraan ini sedang dalam tahap finalisasi oleh tim mekanik kami."
            />
        </div>
    @endif
</div>
@endsection
