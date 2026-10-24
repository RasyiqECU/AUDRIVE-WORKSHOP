@extends('layouts.public')


@section('title', '500 - Server Error')


@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-md">
        <div class="mb-8">
            <span class="font-display text-8xl font-bold text-arancio">500</span>
        </div>
        <h1 class="font-display text-2xl text-silver-bright mb-4">Server Error</h1>
        <p class="font-body text-sm text-silver-dim mb-8">
            Maaf, terjadi kesalahan pada server. Tim kami telah diberitahu dan sedang memperbaiki masalah ini.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Kembali ke Beranda
            </a>
            <button onclick="location.reload()" class="px-6 py-3 border border-giallo text-giallo font-heading font-bold text-xs uppercase tracking-wider hover:bg-giallo hover:text-nero transition-premium clip-angular-button">
                Coba Lagi
            </button>
        </div>
        @if(app()->environment('local', 'staging'))
            <div class="mt-8 p-4 bg-steel/20 border border-gunmetal/30 text-left">
                <p class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Error Details:</p>
                <pre class="font-mono text-xs text-silver-dim overflow-x-auto">{{ $exception->getMessage() }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection



