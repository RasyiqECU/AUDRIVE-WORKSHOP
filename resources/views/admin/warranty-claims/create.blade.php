@extends('layouts.admin')

@section('title', 'Buat Klaim Garansi')

@section('content')
@php $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none'; @endphp
<div class="space-y-6 max-w-3xl mx-auto">
    <a href="{{ route('admin.warranty-claims.index') }}" class="font-heading text-xs text-giallo uppercase tracking-wider">&larr; Daftar Klaim</a>
    <h1 class="font-display text-2xl font-bold text-silver-bright">BUAT KLAIM GARANSI</h1>

    <form method="GET" class="panel border border-gunmetal bg-carbon p-5 flex gap-3">
        <input type="text" name="number" value="{{ $number }}" required placeholder="Nomor nota: kode WO, kode pesanan, atau nomor invoice" class="{{ $field }}">
        <button type="submit" class="px-5 py-2 bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded">Cek</button>
    </form>

    @if($number !== '' && ! $transaction)
        <p class="text-xs text-danger">Nomor nota tidak ditemukan.</p>
    @elseif($transaction && ! $startsAt)
        <p class="text-xs text-danger">Transaksi ini belum lunas/diserahkan, atau merupakan pengerjaan garansi.</p>
    @elseif($transaction)
        @php
            $isService = $transaction['type'] === 'service';
            $workOrder = $transaction['workOrder'];
        @endphp
        <form method="POST" action="{{ route('admin.warranty-claims.store') }}" class="panel border border-gunmetal bg-carbon p-5 space-y-4 text-xs text-silver-dim">
            @csrf
            <input type="hidden" name="number" value="{{ $number }}">
            <p class="text-silver-bright">
                {{ $isService ? 'Servis ' . $workOrder->code : 'Pembelian mobil ' . $transaction['order']->code }}
                &bull; serah terima {{ $startsAt->format('d M Y') }}
            </p>

            @if($isService)
                <label class="block">Item yang diklaim
                    <select name="work_order_item_id" required class="{{ $field }}">
                        @foreach($workOrder->billableItems as $item)
                            <option value="{{ $item->id }}" @disabled($item->part_source === 'customer')>
                                {{ $item->description }}{{ $item->part_source === 'customer' ? ' (tanpa garansi)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </label>
                @unless($workOrder->serviceBooking?->odometer_km !== null)
                    <label class="block">Kilometer saat serah terima (dari nota)<input type="number" name="reference_km" min="0" required value="{{ old('reference_km') }}" class="{{ $field }}"></label>
                @endunless
            @endif

            <label class="block">Kilometer saat ini<input type="number" name="current_km" min="0" required value="{{ old('current_km') }}" class="{{ $field }}"></label>
            <label class="block">Keluhan<textarea name="complaint" rows="3" required class="{{ $field }}">{{ old('complaint') }}</textarea></label>
            <button type="submit" class="px-5 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Buat Klaim</button>
        </form>
    @endif
</div>
@endsection
