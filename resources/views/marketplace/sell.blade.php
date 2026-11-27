@extends('layouts.public')

@section('title', 'Titip Jual Mobil Anda — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl space-y-6">
    <div>
        <a href="{{ route('marketplace.index') }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Marketplace
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">TITIP JUAL MOBIL BEKAS</h1>
        <p class="text-xs text-silver-dim mt-1">
            Mobil tetap milik Anda sampai laku. Sales kami menginspeksi unit, menyepakati harga minimum dan komisi, lalu menayangkannya di marketplace.
        </p>
    </div>

    @if(session('success'))
        <div class="bg-verde/10 border border-verde/30 p-4 text-sm text-verde">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger/10 border border-danger/30 p-4 text-sm text-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <ul class="bg-danger/10 border border-danger/30 p-4 text-xs text-danger list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif

    @if($submissions->isNotEmpty())
        <div class="bg-carbon border border-gunmetal rounded-lg divide-y divide-gunmetal/40">
            @foreach($submissions as $car)
                <div class="p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="text-silver-bright font-semibold">{{ $car->full_name }}</p>
                        <p class="text-silver-dim">
                            {{ $car->status->label() }} &bull; Rp {{ number_format($car->price_sell_idr, 0, ',', '.') }}
                            @if($car->commission_idr !== null) &bull; Komisi Rp {{ number_format($car->commission_idr, 0, ',', '.') }} @endif
                            @if($car->seller_paid_at) &bull; Dibayarkan Rp {{ number_format($car->seller_payout_idr, 0, ',', '.') }} @endif
                        </p>
                    </div>
                    @if(in_array($car->status->value, ['in_inspection', 'available'], true))
                        <form method="POST" action="{{ route('marketplace.sell.withdraw', $car) }}" onsubmit="return confirm('Tarik unit ini dari titip jual?')">
                            @csrf
                            <button type="submit" class="text-danger uppercase font-heading">Tarik Unit</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-carbon border border-gunmetal rounded-lg p-6 md:p-8 shadow-xl">
        <form action="{{ route('marketplace.sell.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @php $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none'; @endphp

            <div class="grid sm:grid-cols-2 gap-4 text-xs text-silver-dim">
                <label>Merek<input type="text" name="brand" required value="{{ old('brand') }}" class="{{ $field }}"></label>
                <label>Model / Tipe<input type="text" name="model" required value="{{ old('model') }}" class="{{ $field }}"></label>
                <label>Tahun<input type="number" name="year" required min="1990" max="{{ date('Y') + 1 }}" value="{{ old('year') }}" class="{{ $field }}"></label>
                <label>Transmisi
                    <select name="transmission" required class="{{ $field }}">
                        <option value="automatic">Automatic</option>
                        <option value="manual" @selected(old('transmission') === 'manual')>Manual</option>
                        <option value="cvt" @selected(old('transmission') === 'cvt')>CVT</option>
                    </select>
                </label>
                <label>Kilometer<input type="number" name="mileage_km" required min="0" value="{{ old('mileage_km') }}" class="{{ $field }}"></label>
                <label>Warna<input type="text" name="color" value="{{ old('color') }}" class="{{ $field }}"></label>
                <label class="sm:col-span-2">Harga yang Anda minta (Rp)<input type="number" name="asking_price_idr" required min="1000000" value="{{ old('asking_price_idr') }}" class="{{ $field }}"></label>
            </div>

            <div class="space-y-4 text-xs text-silver-dim">
                <label class="block">Kekurangan kondisi (baret, penyok, kerusakan)<textarea name="condition_defects" rows="2" required class="{{ $field }}">{{ old('condition_defects') }}</textarea></label>
                <label class="block">Penambahan / modifikasi<textarea name="condition_additions" rows="2" class="{{ $field }}">{{ old('condition_additions') }}</textarea></label>
                <label class="block">Sisa garansi (mis. garansi pabrik s/d Des 2027, atau "tidak ada")<input type="text" name="seller_warranty_info" required value="{{ old('seller_warranty_info') }}" class="{{ $field }}"></label>
                <label class="block">Catatan lain<textarea name="description" rows="2" class="{{ $field }}">{{ old('description') }}</textarea></label>
                <label class="block">Foto (maks. 5, masing-masing 2 MB)<input type="file" name="photos[]" accept="image/*" multiple class="{{ $field }}"></label>
            </div>

            <div class="pt-4 border-t border-gunmetal/40 flex justify-end">
                <x-ui.button type="submit" variant="primary">Kirim Pengajuan Titip Jual</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
