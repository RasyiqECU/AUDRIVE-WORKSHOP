@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Kembali ke Daftar Pengguna</a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">TAMBAH PENGGUNA BARU</h1>
        <p class="text-xs text-silver-dim mt-1">Daftarkan akun staf, teknisi, sales, manager, atau admin baru</p>
    </div>

    <div class="panel border border-gunmetal bg-carbon p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Masukkan nama lengkap" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="contoh@audrive.local" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Role / Peran *</label>
                    <select name="role" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="sales" {{ old('role') === 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="inventory" {{ old('role') === 'inventory' ? 'selected' : '' }}>Inventory</option>
                        <option value="mechanic" {{ old('role') === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="super-admin" {{ old('role') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Password * (Min. 8 karakter)</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    @error('password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2 border border-gunmetal text-silver text-xs uppercase tracking-wider">Batal</a>
                <button type="submit" class="px-7 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition clip-angular-button">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
