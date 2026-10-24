@extends('layouts.guest')


@section('title', 'Konfirmasi Password')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">KONFIRMASI PASSWORD</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">
            Ini adalah area aman dari aplikasi. Silakan konfirmasi password Anda untuk melanjutkan.
        </p>
    </div>


    {{-- Confirm Password Form --}}
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf


        {{-- Password --}}
        <div>
            <label for="password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="••••••••"
            />
            @error('password')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button"
        >
            Konfirmasi
        </button>
    </form>
</div>
@endsection



