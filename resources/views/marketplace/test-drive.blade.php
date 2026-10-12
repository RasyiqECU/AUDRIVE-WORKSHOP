@extends('layouts.public')

@section('title', 'Jadwalkan Test Drive: ' . $car->full_name . ' — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('marketplace.show', $car->slug) }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Detail Unit
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">
            BOOKING TEST DRIVE
        </h1>
        <p class="text-xs text-silver-dim mt-1">Rasakan langsung performa kenyamanan unit {{ $car->full_name }} didampingi konsultan ahli kami.</p>
    </div>

    <div class="bg-carbon border border-gunmetal rounded-lg p-6 md:p-8 shadow-xl">
        <x-marketplace.test-drive-form :car="$car" />
    </div>
</div>
@endsection
