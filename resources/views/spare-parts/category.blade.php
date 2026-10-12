@extends('layouts.public')

@section('title', 'Kategori: ' . $category->name . ' — Spare Part AUDrive')
@section('meta_description', 'Katalog suku cadang kategori ' . $category->name . ' di AUDrive. Suku cadang orisinal berkualitas tinggi untuk kendaraan mewah & sportscar.')

@section('content')
{{-- Hero Section --}}
<section class="relative bg-carbon-fiber overflow-hidden border-b border-gunmetal/40 py-10 md:py-14">
    <div class="absolute inset-0 bg-gradient-to-r from-nero via-nero/90 to-transparent z-10"></div>
    <div class="absolute top-0 right-0 w-80 h-80 bg-giallo/10 rounded-full blur-3xl"></div>

    <div class="relative z-20 container mx-auto px-4">
        <div class="max-w-3xl">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('parts.index') }}" class="font-heading text-xs uppercase tracking-wider text-silver-dim hover:text-giallo">← Semua Kategori</a>
                <span class="text-silver-dim">/</span>
                <span class="font-heading text-xs uppercase tracking-[0.25em] text-giallo font-semibold">Kategori Part</span>
            </div>
            <h1 class="font-display text-3xl md:text-5xl font-bold text-silver-bright tracking-wide uppercase">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="font-body text-silver-dim text-sm md:text-base mt-3 max-w-xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <p class="font-mono text-xs text-silver-dim">
            Menampilkan <span class="text-silver-bright font-bold">{{ $parts->total() }}</span> produk dalam kategori ini
        </p>
        <a href="{{ route('parts.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
            Lihat Semua Kategori →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($parts as $part)
            <x-parts.part-card :part="$part" />
        @empty
            <div class="col-span-full py-12">
                <x-ui.empty-state
                    icon="⚙️"
                    title="Belum Ada Part di Kategori Ini"
                    description="Katalog pada kategori ini sedang dalam proses pembaruan oleh tim inventory."
                >
                    <div class="mt-4">
                        <a href="{{ route('parts.index') }}" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase font-semibold rounded">
                            Kembali ke Katalog Lengkap
                        </a>
                    </div>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    @if($parts->hasPages())
        <div class="mt-10">
            {{ $parts->links() }}
        </div>
    @endif
</section>
@endsection
