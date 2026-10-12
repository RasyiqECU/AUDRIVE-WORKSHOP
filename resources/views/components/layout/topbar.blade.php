{{--
    AUDrive Layout Topbar Component
--}}
<header class="topbar flex items-center justify-between">
    {{-- Mobile menu button --}}
    <button class="lg:hidden text-giallo hover:text-arancio transition-premium" x-data="{ open: false }" @click="open = !open">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Page Title --}}
    <div class="hidden lg:block">
        <h2 class="font-heading text-sm uppercase tracking-[0.15em] text-silver-bright">
            @yield('title', 'Dashboard')
        </h2>
    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-4">
        {{-- Search --}}
        <div class="hidden md:flex items-center">
            <input type="text" placeholder="Cari..." class="bg-nero border border-gunmetal px-3 py-1.5 text-xs text-silver-bright placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none w-48 transition-premium">
        </div>

        {{-- Notifications --}}
        <button class="relative text-silver-dim hover:text-giallo transition-premium">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-danger rounded-full text-[8px] flex items-center justify-center text-white font-bold">3</span>
        </button>

        {{-- User --}}
        @auth
            <div class="flex items-center gap-2" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 text-sm hover:text-giallo transition-premium">
                    <div class="w-7 h-7 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center">
                        <span class="font-heading text-[10px] text-giallo font-bold">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <span class="hidden md:inline font-heading text-xs tracking-wide">{{ auth()->user()->name ?? 'User' }}</span>
                </button>
            </div>
        @endauth
    </div>
</header>