{{--
    AUDrive Layout Sidebar Component
    Main application sidebar with navigation
--}}
<div class="fixed top-0 left-0 h-full w-72 bg-carbon border-r border-gunmetal z-50 hidden lg:block">
    {{-- Logo --}}
    <div class="p-6 border-b border-gunmetal">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/AUDrive-logo.png') }}" alt="AUDrive Logo" class="w-10 h-10 object-contain drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
            <div>
                <h1 class="font-display text-giallo text-lg tracking-wider font-bold">AUDRIVE</h1>
                <p class="font-mono text-[10px] text-silver-dim tracking-widest uppercase">Premium Dealer</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100%-180px)]">
        <p class="font-heading text-[10px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-3">Menu Utama</p>

        <a href="{{ route('home') }}" class="sidebar-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <span>⬡</span>
            <span class="font-heading text-sm font-medium tracking-wide">Dashboard</span>
        </a>

        <a href="{{ route('marketplace.index') }}" class="sidebar-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
            <span>🏎</span>
            <span class="font-heading text-sm font-medium tracking-wide">Marketplace</span>
        </a>

        <a href="{{ route('workshop.index') }}" class="sidebar-item {{ request()->routeIs('workshop.*') ? 'active' : '' }}">
            <span>🔧</span>
            <span class="font-heading text-sm font-medium tracking-wide">Bengkel</span>
        </a>

        <a href="{{ route('parts.index') }}" class="sidebar-item {{ request()->routeIs('parts.*') ? 'active' : '' }}">
            <span>⚙️</span>
            <span class="font-heading text-sm font-medium tracking-wide">Spare Part</span>
        </a>

        <p class="font-heading text-[10px] uppercase tracking-[0.2em] text-silver-dim px-3 mt-6 mb-3">Akun</p>

        @auth
            @if(auth()->user()->role === 'customer')
                <a href="{{ route('customer.dashboard') }}" class="sidebar-item {{ request()->routeIs('customer.*') ? 'active' : '' }}">
                    <span>👤</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Portal Saya</span>
                </a>
            @elseif(auth()->user()->role === 'mechanic')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <span>🔧</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Panel Bengkel</span>
                </a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <span>⚡</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Admin Panel</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-item w-full">
                    <span>⏻</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Logout</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="sidebar-item">
                <span>→</span>
                <span class="font-heading text-sm font-medium tracking-wide">Login</span>
            </a>
        @endauth
    </nav>

    {{-- Footer Status --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gunmetal">
        <div class="bg-steel/30 rounded-sm p-3 border border-gunmetal/50">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-verde animate-pulse-glow"></div>
                <span class="font-mono text-[10px] text-verde uppercase tracking-wider">System Active</span>
            </div>
            <p class="font-mono text-[10px] text-silver-dim">v{{ config('app.version', '1.0.0') }}</p>
        </div>
    </div>
</div>