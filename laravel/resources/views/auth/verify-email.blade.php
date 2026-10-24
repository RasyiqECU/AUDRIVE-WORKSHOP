@extends('layouts.guest')


@section('title', 'Verifikasi Email')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">VERIFIKASI EMAIL</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">
            Terima kasih telah mendaftar!
        </p>
    </div>


    {{-- Info --}}
    <div class="text-center">
        <p class="text-sm text-silver mb-4">
            Sebelum melanjutkan, silakan verifikasi email Anda dengan mengklik link yang telah kami kirimkan ke email Anda.
        </p>


        @if (session('message'))
            <div class="bg-verde/10 border border-verde/30 p-3 text-sm text-verde mb-4">
                {{ session('message') }}
            </div>
        @endif


        <p class="text-xs text-silver-dim mb-6">
            Jika Anda tidak menerima email, kami dapat mengirimkan ulang link verifikasi.
        </p>
    </div>


    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Resend Verification Email --}}
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <button
                type="submit"
                class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button"
            >
                Kirim Ulang Email Verifikasi
            </button>
        </form>


        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="flex-1">
            @csrf
            <button
                type="submit"
                class="w-full border border-gunmetal text-silver-dim font-heading font-bold text-sm uppercase tracking-wider py-3 hover:border-silver-dim hover:text-silver transition-premium clip-angular-button"
            >
                Logout
            </button>
        </form>
    </div>
</div>
@endsection



