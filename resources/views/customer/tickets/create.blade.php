@extends('layouts.customer')

@section('title', 'Buat Tiket Bantuan')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('customer.tickets.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Tiket
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pusat Layanan</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            BUAT TIKET BANTUAN
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Jelaskan pertanyaan atau kendala yang Anda hadapi secara rinci.
        </p>
    </div>

    {{-- Form --}}
    <form action="{{ route('customer.tickets.store') }}" method="POST" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        @csrf

        <div>
            <label for="subject" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Subjek Masalah <span class="text-red-500">*</span>
            </label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required placeholder="Contoh: Pertanyaan Garansi Transmisi Mobil" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
            @error('subject') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="priority" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Tingkat Prioritas <span class="text-red-500">*</span>
            </label>
            <select name="priority" id="priority" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah (Pertanyaan umum)</option>
                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Sedang (Kendala servis rutin)</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi (Mogok / Kendala teknis)</option>
                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Mendesak (Emergency On-the-road)</option>
            </select>
            @error('priority') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Deskripsi Kendala <span class="text-red-500">*</span>
            </label>
            <textarea name="description" id="description" rows="5" required placeholder="Tuliskan kronologi, gejala kerusakan, atau pertanyaan selengkapnya..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2 flex justify-end gap-3">
            <a href="{{ route('customer.tickets.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button">
                Kirim Tiket
            </button>
        </div>
    </form>
</div>
@endsection
