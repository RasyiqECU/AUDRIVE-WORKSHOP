@extends('layouts.guest')


@section('title', 'Register')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="font-display text-xl text-silver-bright tracking-wider">DAFTAR</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Buat akun AUDrive Anda</p>
    </div>


    {{-- Register Form --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf


        {{-- Name --}}
        <div>
            <label for="name" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Nama Lengkap <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="Nama lengkap Anda"
            />
            @error('name')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Email --}}
        <div>
            <label for="email" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="email@audrive.local"
            />
            @error('email')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Phone --}}
        <div>
            <label for="phone" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Nomor Telepon <span class="text-danger">*</span>
            </label>
            <input
                id="phone"
                type="tel"
                name="phone"
                value="{{ old('phone') }}"
                required
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="08xxxxxxxxxx"
            />
            @error('phone')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Password --}}
        <div>
            <label for="password" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Password <span class="text-danger">*</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="••••••••"
            />
            @error('password')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Password Confirmation --}}
        <div>
            <label for="password_confirmation" class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                Konfirmasi Password <span class="text-danger">*</span>
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full bg-nero border border-gunmetal px-4 py-3 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium"
                placeholder="••••••••"
            />
        </div>


        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-giallo text-nero font-heading font-bold text-sm uppercase tracking-wider py-3 hover:bg-arancio transition-premium clip-angular-button mt-6"
        >
            Daftar
        </button>
    </form>


    {{-- Login Link --}}
    <div class="text-center pt-4 border-t border-gunmetal/50">
        <p class="text-xs text-silver-dim">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-giallo hover:text-arancio font-heading uppercase tracking-wider transition-premium">
                Login
            </a>
        </p>
    </div>
</div>
@endsection



