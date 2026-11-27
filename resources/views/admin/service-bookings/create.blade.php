@extends('layouts.admin')

@section('title', 'Booking Walk-in')

@section('content')
@php $input = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none'; @endphp
<div class="space-y-6 max-w-3xl mx-auto"
     x-data="{ customerId: '{{ old('customer_id') }}', vehicleId: '{{ old('customer_vehicle_id') }}', vehicles: {{ Js::from($customers->mapWithKeys(fn ($c) => [$c->id => $c->vehicles->map(fn ($v) => ['id' => $v->id, 'label' => trim("{$v->brand} {$v->model} {$v->plate_number}")])->values()])) }} }">
    <div>
        <a href="{{ route('admin.service-bookings.index') }}" class="font-heading text-xs text-giallo uppercase tracking-wider">&larr; Daftar Booking</a>
        <h1 class="font-display text-2xl font-bold text-silver-bright mt-2">BOOKING WALK-IN</h1>
    </div>

    <form method="POST" action="{{ route('admin.service-bookings.store') }}" class="panel border border-gunmetal bg-carbon p-6 space-y-4 text-xs text-silver-dim">
        @csrf
        <label class="block">Customer
            <select name="customer_id" x-model="customerId" @change="vehicleId = ''" class="{{ $input }}">
                <option value="">+ Customer baru</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->user->name }} — {{ $customer->user->phone }}</option>
                @endforeach
            </select>
        </label>

        <div x-show="!customerId" class="grid md:grid-cols-3 gap-3">
            <label>Nama<input type="text" name="name" value="{{ old('name') }}" class="{{ $input }}"></label>
            <label>Email<input type="email" name="email" value="{{ old('email') }}" class="{{ $input }}"></label>
            <label>No. HP / WA<input type="text" name="phone" value="{{ old('phone') }}" class="{{ $input }}"></label>
        </div>

        <label x-show="customerId" class="block">Kendaraan
            <select name="customer_vehicle_id" x-model="vehicleId" class="{{ $input }}">
                <option value="">+ Kendaraan baru</option>
                <template x-for="vehicle in (vehicles[customerId] || [])" :key="vehicle.id">
                    <option :value="vehicle.id" x-text="vehicle.label" :selected="String(vehicle.id) === vehicleId"></option>
                </template>
            </select>
        </label>

        <div x-show="!customerId || !vehicleId" class="grid md:grid-cols-4 gap-3">
            <label>Merek<input type="text" name="brand" value="{{ old('brand') }}" class="{{ $input }}"></label>
            <label>Model<input type="text" name="model" value="{{ old('model') }}" class="{{ $input }}"></label>
            <label>Tahun<input type="number" name="year" value="{{ old('year') }}" class="{{ $input }}"></label>
            <label>Plat Nomor<input type="text" name="plate_number" value="{{ old('plate_number') }}" class="{{ $input }}"></label>
        </div>

        <label class="block">Jenis Layanan
            <select name="service_type" required class="{{ $input }}">
                @foreach($serviceTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('service_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">Keluhan<textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes') }}</textarea></label>

        <button type="submit" class="px-5 py-2.5 bg-giallo text-nero font-heading font-bold uppercase rounded">Simpan Booking</button>
    </form>
</div>
@endsection
