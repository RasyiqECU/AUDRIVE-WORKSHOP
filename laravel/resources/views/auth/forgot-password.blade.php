@extends('layouts.guest')


@section('title', 'Lupa Password')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">LUPA PASSWORD</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">
            Masukkan email Anda untuk menerima link reset password
        </p>
    </div>


    {{-- Session Status --}}
    @if (session('status'))
        <div class="bg-verde/10 border border-verde/30 p-3 text-sm text-verde">
            {{ session('status') }}
        </div>
    @endif


    {{-- Forgot Password Form --}}
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf


        {{-- Email --}}
        <div>
            <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="email@audrive.local"
            />
            @error('email')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button"
        >
            Kirim Link Reset
        </button>
    </form>


    {{-- Back to Login --}}
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <a href="{{ route('login') }}" class="text-xs text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
            ← Kembali ke Login
        </a>
    </div>
</div>
@endsection



