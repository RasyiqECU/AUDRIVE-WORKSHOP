@extends('layouts.guest')


@section('title', 'Login')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">LOGIN</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Masuk ke akun AUDrive Anda</p>
    </div>


    {{-- Session Status --}}
    @if (session('status'))
        <div class="bg-verde/10 border border-verde/30 p-3 text-sm text-verde">
            {{ session('status') }}
        </div>
    @endif


    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                autocomplete="username"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="email@audrive.local"
            />
            @error('email')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


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


        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gunmetal bg-nero text-giallo focus:ring-giallo focus:ring-offset-0"
                />
                <span class="ms-2 text-xs text-silver-dim font-heading uppercase tracking-wider">Ingat Saya</span>
            </label>


            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                    Lupa Password?
                </a>
            @endif
        </div>


        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button"
        >
            Masuk
        </button>
    </form>


    {{-- Register Link --}}
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <p class="text-xs text-silver-dim">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
@endsection



