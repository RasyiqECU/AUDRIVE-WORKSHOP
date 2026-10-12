@extends('layouts.public')

@section('title', 'Cek Kompatibilitas Kendaraan — Spare Part AUDrive')
@section('meta_description', 'Temukan suku cadang yang 100% kompatibel dan presisi untuk merek dan tipe kendaraan mewah Anda di AUDrive.')

@section('content')
<section class="relative bg-carbon-fiber overflow-hidden border-b border-gunmetal/40 py-10 md:py-14">
    <div class="absolute inset-0 bg-gradient-to-r from-nero via-nero/90 to-transparent z-10"></div>
    <div class="absolute top-0 right-0 w-80 h-80 bg-giallo/10 rounded-full blur-3xl"></div>

    <div class="relative z-20 container mx-auto px-4">
        <div class="max-w-3xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full"></span>
                <span class="font-heading text-xs uppercase tracking-[0.25em] text-giallo font-semibold">Vehicle Fitment Matcher</span>
            </div>
            <h1 class="font-display text-3xl md:text-5xl font-bold text-silver-bright tracking-wide">
                HASIL KOMPATIBILITAS <span class="text-giallo">KENDARAAN</span>
            </h1>
            <p class="font-body text-silver-dim text-sm md:text-base mt-3 max-w-xl leading-relaxed">
                Suku cadang yang telah diverifikasi sesuai spesifikasi teknis dan dimensi kendaraan Anda.
            </p>
        </div>
    </div>
</section>

<section class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <p class="font-mono text-xs text-silver-dim">
            Ditemukan <span class="text-silver-bright font-bold">{{ $parts->total() }}</span> komponen yang cocok
        </p>
        <a href="{{ route('parts.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
            ← Kembali ke Katalog Utama
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($parts as $part)
            <x-parts.part-card :part="$part" />
        @empty
            <div class="col-span-full py-12">
                <x-ui.empty-state
                    icon="🔍"
                    title="Tidak Ada Suku Cadang yang Cocok"
                    description="Belum ada komponen di database kami yang secara spesifik terdaftar untuk model kendaraan ini."
                >
                    <div class="mt-4 flex justify-center gap-3">
                        <a href="{{ route('parts.index') }}" class="px-4 py-2 bg-steel/30 border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded">
                            Lihat Semua Part
                        </a>
                        <a href="{{ route('contact') }}" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase font-semibold rounded">
                            Tanyakan ke Konsultan
                        </a>
                    </div>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    @if($parts->hasPages())
        <div class="mt-10">
            {{ $parts->withQueryString()->links() }}
        </div>
    @endif
</section>
@endsection
