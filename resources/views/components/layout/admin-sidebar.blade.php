{{--
    AUDrive Admin Sidebar Component
    Role-tailored navigation panel for Super Admin, Owner, Manager, Sales, Cashier, Inventory, and Mechanic
--}}
@php
    $user = auth()->user();
    $role = $user->role ?? 'admin';
    $roleBadges = [
        'super-admin' => ['label' => 'SUPER ADMIN', 'color' => 'text-giallo border-giallo/40 bg-giallo/10'],
        'owner'       => ['label' => 'BUSINESS OWNER', 'color' => 'text-amber-400 border-amber-400/40 bg-amber-400/10'],
        'manager'     => ['label' => 'OPERATIONS MANAGER', 'color' => 'text-silver-bright border-silver-dim/40 bg-steel/20'],
        'sales'       => ['label' => 'SALES EXECUTIVE', 'color' => 'text-arancio border-arancio/40 bg-arancio/10'],
        'inventory'   => ['label' => 'INVENTORY OFFICER', 'color' => 'text-verde border-verde/40 bg-verde/10'],
        'cashier'     => ['label' => 'CASHIER & POS', 'color' => 'text-blue-400 border-blue-400/40 bg-blue-400/10'],
        'mechanic'    => ['label' => 'CHIEF MECHANIC', 'color' => 'text-giallo border-giallo/40 bg-giallo/10'],
    ];
    $badge = $roleBadges[$role] ?? ['label' => strtoupper($role), 'color' => 'text-silver-dim border-gunmetal bg-steel/20'];
@endphp

<div class="no-print fixed top-0 left-0 h-full w-72 bg-carbon border-r border-gunmetal z-50 hidden lg:block">
    {{-- Logo & Role Header --}}
    <div class="p-5 border-b border-gunmetal space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
            <img src="{{ \App\Services\SettingService::getLogo() }}" alt="{{ \App\Services\SettingService::getName() }} Logo" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
            <div>
                <h1 class="font-display text-giallo text-sm tracking-wider font-bold">{{ \App\Services\SettingService::getName() }}</h1>
                <p class="font-mono text-[9px] text-silver-dim tracking-widest uppercase">Internal Portal</p>
            </div>
        </a>
        <div class="pt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded border text-[9px] font-heading font-bold uppercase tracking-widest {{ $badge['color'] }}">
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav id="admin-sidebar-nav" class="p-3 space-y-0.5 overflow-y-auto h-[calc(100%-190px)]">
        {{-- Dashboard Utama (Semua Role) --}}
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="text-sm">⬡</span>
            <span class="font-heading text-xs font-medium tracking-wide">
                {{ $role === 'owner' ? 'Executive Dashboard' : 'Dashboard ' . ucfirst($role === 'super-admin' ? 'Executive' : $role) }}
            </span>
        </a>

        {{-- Section Showroom & Penjualan (Super Admin, Sales) --}}
        @if(in_array($role, ['super-admin', 'sales'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Showroom & Penjualan</p>

            <a href="{{ route('admin.cars.index') }}" class="sidebar-item {{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
                <span class="text-sm">🏎</span>
                <span class="font-heading text-xs font-medium tracking-wide">Unit Supercar</span>
            </a>

            <a href="{{ route('admin.offers.index') }}" class="sidebar-item {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
                <span class="text-sm">📋</span>
                <span class="font-heading text-xs font-medium tracking-wide">Pesanan Masuk</span>
            </a>

            @php
                $pendingAppointmentsCount = \App\Models\Appointment::where('status', 'pending')->count();
            @endphp
            <a href="{{ route('admin.appointments.index') }}" class="sidebar-item {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }} flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm">🏁</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Jadwal Test Drive</span>
                </div>
                @if($pendingAppointmentsCount > 0)
                    <span class="px-1.5 py-0.5 bg-arancio/20 border border-arancio/40 text-arancio text-[10px] font-mono font-bold rounded">
                        {{ $pendingAppointmentsCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.vouchers.index') }}" class="sidebar-item {{ request()->routeIs('admin.vouchers.*', 'admin.promotions.*') ? 'active' : '' }}">
                <span class="text-sm">🎟️</span>
                <span class="font-heading text-xs font-medium tracking-wide">Voucher & Promo</span>
            </a>
        @endif

        {{-- Section Workshop & Servis (Super Admin, Manager, Cashier, Mechanic) --}}
        @if(in_array($role, ['super-admin', 'manager', 'cashier', 'mechanic'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Workshop & Servis</p>

            {{-- Booking Servis: Super Admin & Cashier saja (Manager tidak beroperasi) --}}
            @if(in_array($role, ['super-admin', 'cashier'], true))
                <a href="{{ route('admin.service-bookings.index') }}" class="sidebar-item {{ request()->routeIs('admin.service-bookings.*') ? 'active' : '' }}">
                    <span class="text-sm">📅</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Booking Servis</span>
                </a>
            @endif

            <a href="{{ route('admin.work-orders.index') }}" class="sidebar-item {{ request()->routeIs('admin.work-orders.*') ? 'active' : '' }}">
                <span class="text-sm">🔧</span>
                <span class="font-heading text-xs font-medium tracking-wide">{{ $role === 'mechanic' ? 'Work Order Saya' : 'Work Order & Antrean' }}</span>
            </a>

            {{-- AI Sound Scan: Super Admin & Mechanic saja --}}
            @if(in_array($role, ['super-admin', 'mechanic'], true))
                <a href="{{ route('admin.ai-scans.index') }}" class="sidebar-item {{ request()->routeIs('admin.ai-scans.*') ? 'active' : '' }}">
                    <span class="text-sm">🔊</span>
                    <span class="font-heading text-xs font-medium tracking-wide">AI Sound Scan</span>
                </a>
            @endif
        @endif

        {{-- Section Point of Sale & Invoice (Super Admin & Cashier ONLY) --}}
        @if(in_array($role, ['super-admin', 'cashier'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Point of Sale</p>

            <a href="{{ route('admin.pos.index') }}" class="sidebar-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                <span class="text-sm">💳</span>
                <span class="font-heading text-xs font-medium tracking-wide">POS Kasir</span>
            </a>

            <a href="{{ route('admin.invoices.index') }}" class="sidebar-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                <span class="text-sm">🧾</span>
                <span class="font-heading text-xs font-medium tracking-wide">Invoice & Bukti Bayar</span>
            </a>
        @endif

        {{-- Section Gudang & Inventori (Super Admin, Inventory, Mechanic: lihat stok saja) --}}
        @if(in_array($role, ['super-admin', 'inventory', 'mechanic'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Gudang & Inventori</p>

            <a href="{{ route('admin.spare-parts.index') }}" class="sidebar-item {{ request()->routeIs('admin.spare-parts.*') ? 'active' : '' }}">
                <span class="text-sm">⚙️</span>
                <span class="font-heading text-xs font-medium tracking-wide">Katalog Spare Part</span>
            </a>

            @if(in_array($role, ['super-admin', 'inventory'], true))
                <a href="{{ route('admin.inventory.index') }}" class="sidebar-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <span class="text-sm">📦</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Stok & Movement</span>
                </a>

                <a href="{{ route('admin.purchase-orders.index') }}" class="sidebar-item {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}">
                    <span class="text-sm">🛒</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Purchase Order</span>
                </a>
            @endif
        @endif

        {{-- Section CRM & Komunikasi (Super Admin & Sales saja; Manager tidak, Cashier: loyalitas/pelanggan di dalam POS) --}}
        @if(in_array($role, ['super-admin', 'sales'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">CRM & Komunikasi</p>

            @if(in_array($role, ['super-admin', 'sales'], true))
                @php
                    $unreadMessagesCount = \App\Models\ContactMessage::where('status', 'unread')->count();
                @endphp
                <a href="{{ route('admin.contact-messages.index') }}" class="sidebar-item {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }} flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">📬</span>
                        <span class="font-heading text-xs font-medium tracking-wide">Pesan Customer</span>
                    </div>
                    @if($unreadMessagesCount > 0)
                        <span class="px-1.5 py-0.5 bg-giallo/20 border border-giallo/40 text-giallo text-[10px] font-mono font-bold rounded animate-pulse">
                            {{ $unreadMessagesCount }}
                        </span>
                    @endif
                </a>

                @php
                    $openTicketsCount = \App\Models\SupportTicket::where('status', 'open')->count();
                @endphp
                <a href="{{ route('admin.tickets.index') }}" class="sidebar-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }} flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">🎫</span>
                        <span class="font-heading text-xs font-medium tracking-wide">Tiket Layanan</span>
                    </div>
                    @if($openTicketsCount > 0)
                        <span class="px-1.5 py-0.5 bg-amber-500/20 border border-amber-500/40 text-amber-400 text-[10px] font-mono font-bold rounded animate-pulse">
                            {{ $openTicketsCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- Program Loyalitas: Super Admin saja (penggunaan poin dilakukan di dalam POS) --}}
            @if($role === 'super-admin')
                <a href="{{ route('admin.loyalty.index') }}" class="sidebar-item {{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}">
                    <span class="text-sm">👑</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Program Loyalitas</span>
                </a>
            @endif

            {{-- Database Pelanggan: Super Admin saja (Sales tidak berhak atas database pelanggan) --}}
            @if($role === 'super-admin')
                <a href="{{ route('admin.customers.index') }}" class="sidebar-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <span class="text-sm">👥</span>
                    <span class="font-heading text-xs font-medium tracking-wide">Database Pelanggan</span>
                </a>
            @endif
        @endif

        {{-- Section Pengajuan Approval (Sales, Cashier, Inventory) --}}
        @if(in_array($role, ['sales', 'cashier', 'inventory'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Pengajuan & Persetujuan</p>

            <a href="{{ route('admin.approvals.create') }}" class="sidebar-item {{ request()->routeIs('admin.approvals.create', 'admin.approvals.store') ? 'active' : '' }}">
                <span class="text-sm">✍️</span>
                <span class="font-heading text-xs font-medium tracking-wide">Ajukan Approval</span>
            </a>
        @endif

        {{-- Section Laporan (Super Admin, Manager, Sales, Cashier, Inventory — Owner tidak ada) --}}
        @if(in_array($role, ['super-admin', 'manager', 'sales', 'cashier', 'inventory'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Laporan</p>

            <a href="{{ route('admin.reports.index') }}" class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="text-sm">📊</span>
                <span class="font-heading text-xs font-medium tracking-wide">
                    {{ match(true) {
                        $role === 'sales' => 'Laporan Penjualan',
                        $role === 'cashier' => 'Laporan Shift',
                        $role === 'inventory' => 'Laporan Inventori',
                        default => 'Laporan Performa',
                    } }}
                </span>
            </a>
        @endif

        {{-- Section Tata Kelola (Super Admin, Manager) --}}
        @if(in_array($role, ['super-admin', 'manager'], true))
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-3">Tata Kelola</p>

            <a href="{{ route('admin.approvals.index') }}" class="sidebar-item {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                <span class="text-sm">✅</span>
                <span class="font-heading text-xs font-medium tracking-wide">Pusat Approval</span>
            </a>

            <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <span class="text-sm">📋</span>
                <span class="font-heading text-xs font-medium tracking-wide">Audit Trail</span>
            </a>
        @endif

        {{-- Menu Khusus Super Admin Saja (HANYA Super Admin) --}}
        @if($role === 'super-admin')
            <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-giallo/80 px-3 mb-2 mt-3">Super Admin Core</p>

            <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="text-sm">🔑</span>
                <span class="font-heading text-xs font-medium tracking-wide">Manajemen Pengguna</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="text-sm">⚙</span>
                <span class="font-heading text-xs font-medium tracking-wide">Konfigurasi Sistem</span>
            </a>
        @endif
    </nav>

    {{-- Footer User Profile --}}
    <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gunmetal bg-carbon">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center">
                    <span class="font-heading text-[9px] text-giallo font-bold">
                        {{ substr($user->name ?? 'A', 0, 1) }}
                    </span>
                </div>
                <div class="overflow-hidden">
                    <p class="font-heading text-[10px] text-silver-bright truncate max-w-[120px]">{{ $user->name }}</p>
                    <p class="font-mono text-[8px] text-silver-dim capitalize">{{ $role }}</p>
                </div>
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

<script>
    (function () {
        const nav = document.getElementById('admin-sidebar-nav');
        if (!nav) return;

        const STORAGE_KEY = 'audrive_admin_sidebar_scroll';

        // Restore scroll position
        const savedScroll = sessionStorage.getItem(STORAGE_KEY);
        if (savedScroll !== null) {
            nav.scrollTop = parseInt(savedScroll, 10);
        } else {
            const activeItem = nav.querySelector('.sidebar-item.active');
            if (activeItem) {
                activeItem.scrollIntoView({ block: 'nearest' });
            }
        }

        // Save scroll position when clicking any sidebar link
        nav.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link) {
                sessionStorage.setItem(STORAGE_KEY, nav.scrollTop);
            }
        });

        // Continuously persist scroll position while scrolling
        nav.addEventListener('scroll', function () {
            sessionStorage.setItem(STORAGE_KEY, nav.scrollTop);
        }, { passive: true });
    })();
</script>
