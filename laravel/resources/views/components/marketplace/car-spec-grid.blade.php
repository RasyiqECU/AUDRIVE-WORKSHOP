{{--
  x-marketplace.car-spec-grid — Car Specification Grid
  Usage: <x-marketplace.car-spec-grid :car="$car" />
--}}
@props(['car'])


<div {{ $attributes->merge(['class' => 'grid grid-cols-2 md:grid-cols-4 gap-3']) }}>
    @foreach([
        ['Merek', $car->brand],
        ['Model', $car->model],
        ['Tahun', $car->year],
        ['Transmisi', ucfirst($car->transmission)],
        ['Warna', $car->color ?? '-'],
        ['Kilometer', number_format($car->mileage_km, 0, ',', '.') . ' km'],
        ['Lokasi', $car->location ?? '-'],
        ['Bahan Bakar', ucfirst($car->fuel_type ?? '-')],
    ] as [$label, $value])
        <div class="bg-steel/20 border border-gunmetal/30 p-3">
            <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mb-1">{{ $label }}</p>
            <p class="font-heading text-sm text-silver-bright">{{ $value }}</p>
        </div>
    @endforeach
</div>



