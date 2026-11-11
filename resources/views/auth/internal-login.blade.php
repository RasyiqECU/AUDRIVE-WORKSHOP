@extends('layouts.guest')

@section('title', 'Login Internal & Staf')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-6">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded border text-[9px] font-heading font-bold uppercase tracking-widest text-amber-400 border-amber-400/40 bg-amber-400/10 mb-3">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            INTERNAL CONSOLE
        </span>
        <h2 class="font-display text-xl text-silver-bright tracking-wider">LOGIN INTERNAL</h2>
        <p class="font-heading text-xs text-silver-dim tracking-wider mt-1">
            Pintu masuk staf operasional, kasir, mekanik, manajemen &amp; business owner
        </p>
    </div>

    {{-- Info Alert Box --}}
    <div class="bg-carbon border border-gunmetal/80 p-3 rounded text-[11px] text-silver-dim flex items-start gap-2.5">
        <span class="text-amber-400 text-sm leading-none mt-0.5">🔒</span>
        <div>
            <strong class="text-silver-bright font-heading uppercase text-[10px] tracking-wide block">Akses Khusus Terbatas</strong>
            Halaman ini dipantau secara berkala. Hanya pengguna dengan akun internal yang dapat mengakses sistem ini.
        </div>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="bg-verde/10 border border-verde/30 p-3 text-sm text-verde">
            {{ session('status') }}
        </div>
    @endif

    {{-- Internal Login Form --}}
    <form method="POST" action="{{ route('internal.login.store') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Email Internal
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400/30 transition-premium"
                placeholder="staff@audrive.local"
            />
            @error('email')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Kata Sandi
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400/30 transition-premium"
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
                    class="rounded border-gunmetal bg-nero text-amber-400 focus:ring-amber-400 focus:ring-offset-0"
                />
                <span class="ms-2 text-xs text-silver-dim font-heading uppercase tracking-wider">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-silver-dim hover:text-amber-400 font-heading uppercase tracking-wider transition-premium">
                    Lupa Password?
                </a>
            @endif
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-amber-400 hover:bg-amber-500 text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 transition-premium clip-angular-button shadow-lg shadow-amber-400/10"
        >
            Masuk ke Portal Internal &rarr;
        </button>
    </form>

    {{-- Switch to Customer Portal Link --}}
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <p class="text-[11px] text-silver-dim">
            Bukan staf internal?
            <a href="{{ route('login') }}" class="text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium ml-1">
                Portal Pelanggan
            </a>
        </p>
    </div>
</div>
@endsection

