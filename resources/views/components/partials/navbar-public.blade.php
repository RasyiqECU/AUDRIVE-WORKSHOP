{{--
  x-partials.navbar-public — Public Navigation Bar
--}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-nero/60 backdrop-blur-md border-b border-white/10 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/AUDrive-logo.png') }}" alt="AUDrive Logo" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
                <div>
                    <h1 class="font-display text-giallo text-sm tracking-wider font-bold">AUDRIVE</h1>
                    <p class="font-mono text-[8px] text-silver-dim tracking-widest uppercase">Premium Dealer</p>
                </div>
            </a>


            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('marketplace.index') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium {{ request()->routeIs('marketplace.*') ? 'text-giallo' : '' }}">
                    Marketplace
                </a>
                <a href="{{ route('workshop.index') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium {{ request()->routeIs('workshop.*') ? 'text-giallo' : '' }}">
                    Bengkel
                </a>
                <a href="{{ route('parts.index') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium {{ request()->routeIs('parts.*') ? 'text-giallo' : '' }}">
                    Spare Part
                </a>
                <a href="{{ route('about') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                    Tentang
                </a>
                <a href="{{ route('contact') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                    Kontak
                </a>
            </div>


            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->role === 'customer')
                        <a href="{{ route('customer.dashboard') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Dashboard
                        </a>
                    @elseif(auth()->user()->role === 'mechanic')
                        <a href="{{ route('admin.dashboard') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Panel Bengkel
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                            Admin Panel
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="font-heading text-xs text-silver-dim hover:text-giallo uppercase tracking-wider transition-premium">
                        Login
                    </a>
                    <x-ui.button href="{{ route('register') }}" variant="primary" size="sm">
                        Daftar
                    </x-ui.button>
                @endauth
            </div>
        </div>
    </div>
</nav>



