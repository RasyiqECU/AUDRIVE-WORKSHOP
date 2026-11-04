@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    {{-- Screen Header --}}
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN PENGGUNA
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Kelola akun staf, teknisi, kasir, admin, dan pelanggan sistem AUDrive
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2 shadow-sm">
                <span>🖨️</span> Cetak Laporan Pengguna
            </button>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
                <span>+</span> Tambah Pengguna
            </a>
        </div>
    </div>

    {{-- Official Print Letterhead (Only visible in Print) --}}
    <div class="hidden print:block border-b-2 border-black pb-4 mb-6">
        <table class="w-full text-black">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h2 class="text-2xl font-bold uppercase tracking-wider">PT AUDRIVE SUPERCAR INDONESIA</h2>
                    <p class="text-xs font-semibold uppercase text-gray-600">Database Pengguna & Hak Akses Sistem Portal Internal</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Kav. 88, SCBD, Jakarta Selatan &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN DATA PENGGUNA</h3>
                    <p class="text-xs font-mono">Dokumen: USR-RPT-{{ date('Ymd-His') }}</p>
                    <p class="text-[10px] text-gray-500">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB oleh {{ auth()->user()->name }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Filter (Screen only) --}}
    <div class="no-print panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none min-w-[220px]">
            
            <select name="role" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Role</option>
                <option value="super-admin" {{ request('role') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="sales" {{ request('role') === 'sales' ? 'selected' : '' }}>Sales</option>
                <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                <option value="inventory" {{ request('role') === 'inventory' ? 'selected' : '' }}>Inventory</option>
                <option value="mechanic" {{ request('role') === 'mechanic' ? 'selected' : '' }}>Mechanic / Teknisi</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>

            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Bergabung</th>
                        <th class="py-3 px-4 text-right no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    @forelse($users as $user)
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center font-bold text-giallo no-print">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-silver-bright">{{ $user->name }}</p>
                                        <p class="font-mono text-[10px] text-silver-dim">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                {{ $user->phone ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-heading font-semibold uppercase tracking-wider rounded border {{ $user->role === 'super-admin' ? 'bg-giallo/20 border-giallo text-giallo' : ($user->role === 'customer' ? 'bg-steel border-gunmetal text-silver-dim' : 'bg-verde/20 border-verde text-verde') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->is_active)
                                    <span class="text-verde font-bold text-xs">● Aktif</span>
                                @else
                                    <span class="text-danger font-bold text-xs">● Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-[10px]">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2 no-print">
                                <a href="{{ route('admin.users.show', $user) }}" class="px-2.5 py-1 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-1 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Tidak ada data pengguna yang cocok
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20 no-print">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Official Signatures for Print Mode --}}
    <div class="hidden print:block pt-8">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 50%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dicetak & Diperiksa Oleh (Admin HR/IT):</p>
                    <p class="font-bold underline">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-gray-500">Administrator Sistem</p>
                </td>
                <td style="width: 50%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Disetujui Oleh (Direktur/General Manager):</p>
                    <p class="font-bold underline">Management AUDrive</p>
                    <p class="text-[9px] text-gray-500">Pimpinan Operasional</p>
                </td>
            </tr>
        </table>
    </div>
    </div>
</div>
@endsection
