@extends('layouts.public')


@section('title', '403 - Akses Ditolak')


@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-md">
        <div class="mb-8">
            <span class="font-display text-8xl font-bold text-danger">403</span>
        </div>
        <h1 class="font-display text-2xl text-silver-bright mb-4">Akses Ditolak</h1>
        <p class="font-body text-sm text-silver-dim mb-8">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Kembali ke Beranda
            </a>
            @auth
                @if(auth()->user()->role === 'customer')
                    <a href="{{ route('customer.dashboard') }}" class="px-6 py-3 border border-giallo text-giallo font-heading font-bold text-xs uppercase tracking-wider hover:bg-giallo hover:text-nero transition-premium clip-angular-button">
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 border border-giallo text-giallo font-heading font-bold text-xs uppercase tracking-wider hover:bg-giallo hover:text-nero transition-premium clip-angular-button">
                        Dashboard Admin
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection



