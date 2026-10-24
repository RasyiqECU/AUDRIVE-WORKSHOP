@extends('layouts.admin')

@section('title', 'Tambah Voucher Baru')

@section('breadcrumb')
    <a href="{{ route('admin.vouchers.index') }}" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Voucher</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Tambah Baru</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.vouchers.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
            ← Kembali ke Daftar Voucher
        </a>
        <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide">
            BUAT VOUCHER / KUPON BARU
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Tentukan kode kupon, besaran potongan harga, kuota penggunaan, dan apakah dapat ditukarkan dengan loyalty poin.
        </p>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-6">
        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Kode Kupon --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Kode Voucher <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Contoh: SUPERCAR10, DISC50K" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono uppercase text-sm rounded p-3 focus:border-giallo focus:outline-none placeholder-silver-dim">
                    <p class="text-[10px] text-silver-dim mt-1">Huruf besar tanpa spasi, otomatis dikonversi ke uppercase.</p>
                </div>

                {{-- Tipe Diskon --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tipe Potongan <span class="text-danger">*</span>
                    </label>
                    <select name="type" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-sm rounded p-3 focus:border-giallo focus:outline-none">
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>

                {{-- Nilai Potongan --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Besar Potongan <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="value" value="{{ old('value') }}" required min="1" placeholder="Contoh: 50000 atau 10" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                    <p class="text-[10px] text-silver-dim mt-1">Jika persentase, isi angka 1-100. Jika nominal, isi rupiah (misal: 100000).</p>
                </div>

                {{-- Biaya Poin Loyalty --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Biaya Tukar Poin Loyalty
                    </label>
                    <input type="number" name="points_cost" value="{{ old('points_cost', 0) }}" min="0" placeholder="0 jika bukan reward poin" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                    <p class="text-[10px] text-silver-dim mt-1">Isi > 0 agar kupon ini otomatis tampil di menu Tukar Poin Loyalty customer.</p>
                </div>

                {{-- Minimum Pembelian --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Minimal Belanja / Servis (IDR)
                    </label>
                    <input type="number" name="min_purchase_idr" value="{{ old('min_purchase_idr', 0) }}" min="0" placeholder="0 = tanpa minimum" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                {{-- Kuota Penggunaan --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Batas Kuota Penggunaan (Maksimal) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="max_usage" value="{{ old('max_usage', 100) }}" required min="1" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                {{-- Tanggal Kadaluarsa --}}
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tanggal Kadaluarsa
                    </label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                    <p class="text-[10px] text-silver-dim mt-1">Kosongkan jika voucher berlaku selamanya tanpa batas waktu.</p>
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-center pt-6">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-gunmetal bg-steel text-giallo focus:ring-0">
                        <div>
                            <span class="font-heading text-xs uppercase text-silver-bright font-bold">Voucher Langsung Aktif</span>
                            <p class="text-[10px] text-silver-dim">Jika dicentang, voucher langsung bisa digunakan di kasir/customer.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                    Deskripsi & Syarat Ketentuan
                </label>
                <textarea name="description" rows="3" placeholder="Contoh: Potongan khusus servis berkala supercar Ferrari & Porsche..." 
                          class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none placeholder-silver-dim">{{ old('description') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gunmetal/60">
                <a href="{{ route('admin.vouchers.index') }}" class="px-5 py-2.5 bg-steel hover:bg-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors">
                    Simpan Voucher Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
