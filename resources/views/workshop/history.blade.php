@extends(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'))

@section('title', 'Riwayat Servis Kendaraan — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('workshop.index') }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Workshop
        </a>
        <h1 class="font-display text-2xl font-bold text-silver-bright mt-2">RIWAYAT SERVIS</h1>
        <p class="text-xs text-silver-dim mt-1">Daftar servis kendaraan Anda yang telah selesai atau dibatalkan.</p>
    </div>

    @if($bookings->isEmpty())
        <div class="text-center py-16 bg-nero-dark border border-white/10 rounded-lg">
            <p class="text-silver-dim text-sm">Belum ada riwayat servis kendaraan.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="p-4 bg-nero-dark border border-white/10 rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-bold text-silver-bright text-sm uppercase">{{ $booking->service_type }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase tracking-wider {{ $booking->status->value === 'completed' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                {{ $booking->status->value ?? $booking->status }}
                            </span>
                        </div>
                        <p class="text-xs text-silver-dim mt-1">
                            Kendaraan: <span class="text-silver-bright">{{ $booking->vehicle?->license_plate ?? 'N/A' }}</span> — {{ $booking->vehicle?->brand }} {{ $booking->vehicle?->model }}
                        </p>
                        <p class="text-xs text-silver-dim">
                            Tanggal: {{ $booking->preferred_date?->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('workshop.booking.show', $booking) }}" class="px-3 py-1.5 border border-white/20 text-xs text-silver-bright rounded hover:border-giallo transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
