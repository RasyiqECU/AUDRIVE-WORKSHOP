@extends('layouts.admin')

@section('title', 'Database Pelanggan (CRM)')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-giallo font-mono text-sm tracking-widest uppercase">CRM & Laporan</span>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Pelanggan</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                DATABASE PELANGGAN
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Kelola profil klien supercar, kepemilikan armada kendaraan, poin loyalitas VIP, dan riwayat interaksi.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2">
                <span>🖨️</span> Cetak Daftar
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Total Klien Terdaftar</span>
                <span class="text-blue-400">👥</span>
            </div>
            <p class="font-mono text-2xl font-bold text-silver-bright mt-2">
                {{ $customers->total() }} <span class="text-xs font-normal text-silver-dim">Klien</span>
            </p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Total Kendaraan Terdaftar</span>
                <span class="text-arancio">🏎</span>
            </div>
            <p class="font-mono text-2xl font-bold text-arancio mt-2">
                {{ \App\Models\CustomerVehicle::count() }} <span class="text-xs font-normal text-silver-dim">Unit Garasi</span>
            </p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Akumulasi Loyalty VIP</span>
                <span class="text-giallo">⭐</span>
            </div>
            <p class="font-mono text-2xl font-bold text-giallo mt-2">
                {{ number_format(\App\Models\Customer::sum('loyalty_points'), 0, ',', '.') }} <span class="text-xs font-normal text-silver-dim">Pts</span>
            </p>
        </div>
    </div>

    {{-- Customers Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Klien</th>
                        <th class="py-3 px-4">Kontak & WhatsApp</th>
                        <th class="py-3 px-4">Domisili / Kota</th>
                        <th class="py-3 px-4 text-center">Unit Mobil</th>
                        <th class="py-3 px-4 text-center">Loyalty Points</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($customers as $customer)
                        @php
                            $phone = $customer->user->phone ?? '';
                            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                        @endphp
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-steel/60 border border-gunmetal flex items-center justify-center font-heading text-xs font-bold text-giallo">
                                        {{ substr($customer->user->name ?? 'C', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-silver-bright">{{ $customer->user->name ?? 'Pelanggan' }}</p>
                                        <p class="font-mono text-[10px] text-giallo tracking-wider">{{ $customer->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-silver-bright font-medium">{{ $customer->user->email ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-[10px] text-silver-dim">{{ $phone ?: '-' }}</span>
                                    @if($cleanPhone)
                                        <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($customer->user->name ?? '') }},%20salam%20dari%20AUDrive." target="_blank" class="text-emerald-400 hover:text-emerald-300 font-mono text-[9px] flex items-center gap-0.5" title="WhatsApp Customer">
                                            <span>💬</span> WA
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-silver-bright">{{ $customer->city ?: 'Kota belum diisi' }}</p>
                                <p class="text-[10px] text-silver-dim">{{ $customer->province ?: '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.customers.vehicles', $customer) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-steel/40 hover:bg-steel/60 border border-gunmetal text-silver-bright font-mono text-[11px] transition-premium" title="Lihat Garasi Mobil">
                                    <span>🏎</span>
                                    <span>{{ $customer->vehicles_count }} Unit</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-mono font-bold text-giallo text-xs">
                                    {{ number_format($customer->loyalty_points, 0, ',', '.') }}
                                </span>
                                <span class="text-[9px] text-silver-dim uppercase block">Pts</span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="px-2.5 py-1 bg-steel/50 hover:bg-giallo hover:text-carbon border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail
                                </a>
                                <a href="{{ route('admin.customers.history', $customer) }}" class="px-2.5 py-1 bg-steel/50 hover:bg-arancio hover:text-carbon border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium" title="Riwayat Servis">
                                    Riwayat
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="px-2.5 py-1 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-silver-dim">Belum ada data pelanggan terdaftar di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="p-4 border-t border-gunmetal">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
