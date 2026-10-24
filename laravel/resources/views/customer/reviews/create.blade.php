@extends('layouts.customer')

@section('title', 'Tulis Ulasan')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('customer.reviews.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Ulasan
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Formulir Kepuasan</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            TULIS ULASAN & RATING
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Pengalaman Anda sangat berharga bagi kami dan komunitas AUDrive.
        </p>
    </div>

    {{-- Form --}}
    <form action="{{ route('customer.reviews.store') }}" method="POST" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-5">
        @csrf

        {{-- Rating Selection --}}
        <div>
            <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                Rating Layanan <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-4">
                @for($i = 5; $i >= 1; $i--)
                    <label class="flex items-center gap-1.5 cursor-pointer text-sm text-silver-muted hover:text-giallo">
                        <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }} class="text-giallo focus:ring-giallo bg-steel border-gunmetal">
                        <span>{{ $i }} Bintang</span>
                    </label>
                @endfor
            </div>
            @error('rating') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Review Comment --}}
        <div>
            <label for="comment" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Komentar & Ulasan <span class="text-red-500">*</span>
            </label>
            <textarea name="comment" id="comment" rows="5" required placeholder="Ceritakan bagaimana kualitas mekanik, kecepatan pengerjaan, atau kenyamanan ruang tunggu..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">{{ old('comment') }}</textarea>
            @error('comment') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2 flex justify-end gap-3">
            <a href="{{ route('customer.reviews.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button">
                Kirim Ulasan
            </button>
        </div>
    </form>
</div>
@endsection
