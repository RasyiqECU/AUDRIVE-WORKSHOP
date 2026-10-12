{{--
    AUDrive Admin Topbar Component
--}}
<header class="topbar flex items-center justify-between">
    {{-- Mobile menu --}}
    <button class="lg:hidden text-giallo hover:text-arancio transition-premium">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Title --}}
    <div class="hidden lg:flex items-center gap-3">
        <div class="h-px w-8 bg-giallo"></div>
        <h2 class="font-heading text-sm uppercase tracking-[0.15em] text-silver-bright">
            @yield('title', 'Admin Dashboard')
        </h2>
    </div>

    {{-- Right --}}
    <div class="flex items-center gap-3">
        {{-- Quick Stats --}}
        <div class="hidden md:flex items-center gap-4 mr-4">
            <div class="text-center">
                <p class="font-mono text-xs text-giallo">23</p>
                <p class="font-heading text-[8px] text-silver-dim uppercase">WO Active</p>
            </div>
            <div class="text-center">
                <p class="font-mono text-xs text-verde">127</p>
                <p class="font-heading text-[8px] text-silver-dim uppercase">Unit Ready</p>
            </div>
        </div>

        {{-- Notifications --}}
        @php
            $adminUnreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
        @endphp
        <a href="{{ route('admin.notifications.index') }}" class="relative text-silver-dim hover:text-giallo transition-premium" title="Notifikasi Masuk">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if($adminUnreadCount > 0)
                <span class="absolute -top-1 -right-1 min-w-3.5 h-3.5 px-1 bg-red-600 rounded-full text-[9px] flex items-center justify-center text-white font-bold">
                    {{ $adminUnreadCount > 9 ? '9+' : $adminUnreadCount }}
                </span>
            @endif
        </a>

        {{-- User --}}
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-giallo/10 border border-giallo/30 clip-hexagon flex items-center justify-center">
                <span class="font-heading text-[10px] text-giallo font-bold">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </span>
            </div>
            <div class="hidden md:block">
                <p class="font-heading text-xs text-silver-bright">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="font-mono text-[9px] text-silver-dim uppercase">{{ auth()->user()->role ?? 'admin' }}</p>
            </div>
        </div>
    </div>
</header>