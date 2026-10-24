@extends('layouts.admin')

@section('title', 'Edit Profil Klien - ' . ($customer->user->name ?? 'Customer'))

@section('content')
<div class="space-y-6 max-w-4xl">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.index') }}" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <a href="{{ route('admin.customers.show', $customer) }}" class="text-silver-bright font-mono text-xs hover:underline">{{ $customer->code }}</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Edit</span>
            </div>
            <h1 class="font-display text-2xl font-bold text-silver-bright tracking-wide mt-1">
                EDIT DATA PELANGGAN
            </h1>
            <p class="text-xs text-silver-dim mt-0.5 font-mono">
                Kode Pelanggan: <span class="text-giallo font-bold">{{ $customer->code }}</span>
            </p>
        </div>
        <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
            Kembali
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="panel border border-gunmetal bg-carbon p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Nama Lengkap --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Nama Lengkap Klien <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $customer->user->name ?? '') }}" required class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                @error('name')
                    <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Nomor Telepon / WhatsApp
                </label>
                <input type="text" name="phone" value="{{ old('phone', $customer->user->phone ?? '') }}" placeholder="08xxxxxxxxxx" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright font-mono focus:border-giallo focus:outline-none transition-premium" />
                @error('phone')
                    <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kota --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Kota Domisili
                </label>
                <input type="text" name="city" value="{{ old('city', $customer->city) }}" placeholder="Contoh: Jakarta Selatan" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                @error('city')
                    <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Provinsi --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Provinsi
                </label>
                <input type="text" name="province" value="{{ old('province', $customer->province) }}" placeholder="Contoh: DKI Jakarta" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                @error('province')
                    <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Loyalty Points --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Loyalty Points VIP
                </label>
                <input type="number" name="loyalty_points" value="{{ old('loyalty_points', $customer->loyalty_points) }}" min="0" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-giallo font-mono font-bold focus:border-giallo focus:outline-none transition-premium" />
                @error('loyalty_points')
                    <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email (Read-only) --}}
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-dim mb-2">
                    Email Akun (Terkunci)
                </label>
                <input type="email" value="{{ $customer->user->email ?? '-' }}" disabled class="w-full bg-steel/10 border border-gunmetal/40 rounded p-2.5 text-xs text-silver-dim cursor-not-allowed" />
            </div>
        </div>

        {{-- Alamat Lengkap --}}
        <div>
            <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                Alamat Domisili Lengkap
            </label>
            <textarea name="address" rows="3" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium">{{ old('address', $customer->address) }}</textarea>
            @error('address')
                <p class="text-danger text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-gunmetal flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.show', $customer) }}" class="px-5 py-2.5 bg-steel/30 hover:bg-steel/60 text-silver-dim hover:text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-arancio text-carbon font-heading text-xs font-bold uppercase tracking-wider rounded transition-premium">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
