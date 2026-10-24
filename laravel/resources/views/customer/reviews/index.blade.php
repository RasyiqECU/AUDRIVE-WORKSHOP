@extends('layouts.customer')

@section('title', 'Ulasan & Rating')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Feedback & Testimoni</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                ULASAN & RATING ANDA
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Bantu kami meningkatkan kualitas layanan workshop & showroom AUDrive melalui penilaian Anda.
            </p>
        </div>
        <a href="{{ route('customer.reviews.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium shrink-0">
            ✍️ Tulis Ulasan Baru
        </a>
    </div>

    {{-- Reviews List --}}
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1 text-giallo text-base">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                        @endfor
                        <span class="text-xs text-silver-bright font-mono font-bold ml-2">({{ $review->rating }}/5)</span>
                    </div>
                    <span class="text-[11px] font-mono text-silver-dim">
                        {{ $review->created_at->format('d M Y, H:i') }}
                    </span>
                </div>

                <p class="text-xs text-silver-bright leading-relaxed">
                    {{ $review->comment }}
                </p>

                <div class="border-t border-gunmetal/40 pt-2 flex items-center justify-between text-[11px] text-silver-dim">
                    <span>Status: <span class="text-emerald-400 capitalize">{{ $review->status ?? 'Approved' }}</span></span>
                </div>
            </div>
        @empty
            <div class="panel border border-gunmetal/70 bg-carbon p-8 text-center text-silver-dim">
                <span class="text-4xl block mb-2">⭐</span>
                <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Belum Ada Ulasan</p>
                <p class="text-xs mt-1 mb-4">Bagikan pengalaman servis atau pembelian Anda bersama AUDrive.</p>
                <a href="{{ route('customer.reviews.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-giallo text-carbon font-heading text-xs uppercase font-bold rounded">
                    Tulis Ulasan Sekarang
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="pt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
