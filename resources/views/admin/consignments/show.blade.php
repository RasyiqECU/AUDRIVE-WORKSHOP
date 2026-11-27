@extends('layouts.admin')

@section('title', 'Titip Jual — ' . $car->full_name)

@section('content')
@php
    $role = auth()->user()->role;
    $inspection = $car->latestInspection;
    $reviewing = $car->status === \App\Enums\CarUnitStatus::InInspection;
    $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none';
@endphp
<div class="space-y-6 max-w-3xl mx-auto text-xs">
    <a href="{{ route('admin.consignments.index') }}" class="font-heading text-giallo uppercase tracking-wider">&larr; Daftar Titip Jual</a>
    <div class="flex flex-wrap justify-between gap-2">
        <h1 class="font-display text-2xl font-bold text-silver-bright">{{ $car->full_name }}</h1>
        <span class="text-silver-bright">{{ $car->status->label() }}</span>
    </div>

    @if($errors->any())
        <ul class="border border-danger/40 text-danger p-3 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif

    <div class="panel border border-gunmetal bg-carbon p-5 text-silver space-y-1">
        <p>Penjual: {{ $car->seller?->user?->name }} ({{ $car->seller?->user?->phone }})</p>
        <p>Harga diminta: Rp {{ number_format($car->asking_price_idr, 0, ',', '.') }} &bull; {{ number_format($car->mileage_km, 0, ',', '.') }} km &bull; {{ $car->transmission }}</p>
        <p>Kekurangan: {{ $car->condition_defects }}</p>
        <p>Penambahan: {{ $car->condition_additions ?: '-' }}</p>
        <p>Garansi menurut penjual: {{ $car->seller_warranty_info }} {{ $car->seller_warranty_verified ? '(terverifikasi)' : '(belum diverifikasi)' }}</p>
        @if($car->min_price_idr)
            <p>Harga minimum: Rp {{ number_format($car->min_price_idr, 0, ',', '.') }} &bull; Komisi: Rp {{ number_format($car->commission_idr, 0, ',', '.') }} &bull; Biaya servis: Rp {{ number_format($car->repair_cost_idr, 0, ',', '.') }}</p>
        @endif
        <div class="flex flex-wrap gap-2 pt-2">
            @foreach($car->photos as $photo)
                <a href="{{ asset('storage/' . $photo->path) }}" target="_blank" rel="noopener">
                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $car->full_name }}" class="w-28 h-20 object-cover border border-gunmetal">
                </a>
            @endforeach
        </div>
    </div>

    @if($inspection)
        <div class="panel border border-gunmetal bg-carbon p-5 text-silver space-y-1">
            <h2 class="font-heading uppercase text-silver-bright">Inspeksi {{ $inspection->inspector?->name }}</h2>
            <p>Hasil: {{ ['pass' => 'Layak', 'conditional' => 'Layak bersyarat', 'fail' => 'Tidak layak'][$inspection->result] ?? $inspection->result }}</p>
            <p>{{ $inspection->notes }}</p>
            <p>Estimasi biaya perbaikan: Rp {{ number_format((int) $inspection->estimated_repair_idr, 0, ',', '.') }}</p>
        </div>
    @endif

    @if($reviewing && in_array($role, ['super-admin', 'mechanic'], true))
        <form method="POST" action="{{ route('admin.consignments.inspect', $car) }}" class="panel border border-giallo/40 bg-carbon p-5 space-y-3 text-silver-dim">
            @csrf
            <h2 class="font-heading uppercase text-giallo">Inspeksi Kelayakan</h2>
            <select name="result" required class="{{ $field }}">
                <option value="pass">Layak</option>
                <option value="conditional">Layak bersyarat</option>
                <option value="fail">Tidak layak</option>
            </select>
            <textarea name="notes" rows="3" required placeholder="Catatan kondisi" class="{{ $field }}">{{ old('notes') }}</textarea>
            <label class="block">Estimasi biaya perbaikan (Rp)<input type="number" name="estimated_repair_idr" min="0" required value="{{ old('estimated_repair_idr', 0) }}" class="{{ $field }}"></label>
            <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Simpan Inspeksi</button>
        </form>
    @endif

    @if($reviewing && in_array($role, ['super-admin', 'manager'], true))
        @if($inspection)
            <form method="POST" action="{{ route('admin.consignments.approve', $car) }}" class="panel border border-giallo/40 bg-carbon p-5 grid md:grid-cols-2 gap-3 text-silver-dim">
                @csrf
                <label>Harga minimum<input type="number" name="min_price_idr" required value="{{ old('min_price_idr') }}" class="{{ $field }}"></label>
                <label>Harga tayang<input type="number" name="price_sell_idr" required value="{{ old('price_sell_idr', $car->price_sell_idr) }}" class="{{ $field }}"></label>
                <label>Komisi PT<input type="number" name="commission_idr" min="0" required value="{{ old('commission_idr') }}" class="{{ $field }}"></label>
                <label>Biaya servis dipotong<input type="number" name="repair_cost_idr" min="0" required value="{{ old('repair_cost_idr', (int) $inspection->estimated_repair_idr) }}" class="{{ $field }}"></label>
                <label class="md:col-span-2 flex items-center gap-2"><input type="checkbox" name="seller_warranty_verified" value="1"> Garansi penjual sudah diverifikasi</label>
                <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Setujui &amp; Tayangkan</button>
            </form>
        @else
            <p class="text-silver-dim italic">Menunggu inspeksi Kepala Mekanik sebelum bisa disetujui.</p>
        @endif
        <form method="POST" action="{{ route('admin.consignments.reject', $car) }}" class="flex flex-wrap gap-2">
            @csrf
            <input type="text" name="reason" required placeholder="Alasan penolakan" class="flex-1 bg-nero border border-gunmetal px-3 py-2 text-silver-bright">
            <button type="submit" class="px-4 py-2 border border-danger/50 text-danger font-heading font-bold uppercase rounded">Tolak</button>
        </form>
    @endif
</div>
@endsection
