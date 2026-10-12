{{--
    AUDrive Customer Sidebar Component
--}}
<div class="fixed top-0 left-0 h-full w-72 bg-carbon border-r border-gunmetal z-50 hidden lg:block">
    {{-- Logo --}}
    <div class="p-5 border-b border-gunmetal">
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/AUDrive-logo.png') }}" alt="AUDrive Logo" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
            <div>
                <h1 class="font-display text-giallo text-sm tracking-wider font-bold">AUDRIVE</h1>
                <p class="font-mono text-[9px] text-silver-dim tracking-widest uppercase">Customer Portal</p>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="p-3 space-y-0.5 overflow-y-auto h-[calc(100%-160px)]">
        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-1">Portal</p>

        <a href="{{ route('customer.dashboard') }}" class="sidebar-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <span class="text-sm">⬡</span>
            <span class="font-heading text-xs font-medium tracking-wide">Dashboard</span>
        </a>

        <a href="{{ route('customer.vehicles.index') }}" class="sidebar-item {{ request()->routeIs('customer.vehicles.*') ? 'active' : '' }}">
            <span class="text-sm">🚗</span>
            <span class="font-heading text-xs font-medium tracking-wide">Kendaraan Saya</span>
        </a>

        <a href="{{ route('customer.reminders.index') }}" class="sidebar-item {{ request()->routeIs('customer.reminders.*') ? 'active' : '' }}">
            <span class="text-sm">🔔</span>
            <span class="font-heading text-xs font-medium tracking-wide">Reminder</span>
        </a>

        <a href="{{ route('customer.loyalty.index') }}" class="sidebar-item {{ request()->routeIs('customer.loyalty.*') ? 'active' : '' }}">
            <span class="text-sm">⭐</span>
            <span class="font-heading text-xs font-medium tracking-wide">Loyalty Point</span>
        </a>

        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-4">Layanan</p>

        <a href="{{ route('customer.ai-scan.index') }}" class="sidebar-item {{ request()->routeIs('customer.ai-scan.*') ? 'active' : '' }}">
            <span class="text-sm">🔊</span>
            <span class="font-heading text-xs font-medium tracking-wide">AI Sound Scan</span>
        </a>

        <a href="{{ route('workshop.booking.index') }}" class="sidebar-item {{ request()->routeIs('workshop.*') ? 'active' : '' }}">
            <span class="text-sm">🔧</span>
            <span class="font-heading text-xs font-medium tracking-wide">Servis & Work Order</span>
        </a>

        <a href="{{ route('marketplace.index') }}" class="sidebar-item {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
            <span class="text-sm">🏎</span>
            <span class="font-heading text-xs font-medium tracking-wide">Marketplace</span>
        </a>

        <a href="{{ route('parts.index') }}" class="sidebar-item {{ request()->routeIs('parts.*') ? 'active' : '' }}">
            <span class="text-sm">⚙️</span>
            <span class="font-heading text-xs font-medium tracking-wide">Spare Part</span>
        </a>

        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-4">Bantuan</p>

        <a href="{{ route('customer.tickets.index') }}" class="sidebar-item {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}">
            <span class="text-sm">🎫</span>
            <span class="font-heading text-xs font-medium tracking-wide">Tiket Support</span>
        </a>

        <a href="{{ route('customer.vouchers.index') }}" class="sidebar-item {{ request()->routeIs('customer.vouchers.*') ? 'active' : '' }}">
            <span class="text-sm">🎁</span>
            <span class="font-heading text-xs font-medium tracking-wide">Voucher & Promo</span>
        </a>

        <a href="{{ route('customer.notifications.index') }}" class="sidebar-item {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}">
            <span class="text-sm">📬</span>
            <span class="font-heading text-xs font-medium tracking-wide">Notifikasi</span>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gunmetal">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center">
                    <span class="font-heading text-[9px] text-giallo font-bold">
                        {{ substr(auth()->user()->name ?? 'C', 0, 1) }}
                    </span>
                </div>
                <span class="font-heading text-[10px] text-silver-dim">{{ auth()->user()->name ?? 'Customer' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="font-heading text-[10px] text-silver-dim hover:text-danger uppercase tracking-wider transition-premium">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>