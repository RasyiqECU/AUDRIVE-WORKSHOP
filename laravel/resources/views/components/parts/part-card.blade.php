{{--
  x-parts.part-card — Spare Part Card
  Usage: <x-parts.part-card :part="$part" />
--}}
@props(['part'])

<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden dashboard-card group flex flex-col justify-between hover:border-giallo/50 transition-premium rounded shadow-lg']) }}>
    <div>
        {{-- Image --}}
        <a href="{{ route('parts.show', $part->slug) }}" class="block relative aspect-square bg-steel/30 overflow-hidden">
            @if($part->image)
                <img src="{{ asset('storage/' . $part->image) }}" alt="{{ $part->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-premium duration-500" />
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gunmetal group-hover:text-giallo transition-premium">
                    <span class="text-4xl mb-1">⚙️</span>
                    <span class="font-mono text-[9px] text-silver-dim uppercase">AUDrive OEM</span>
                </div>
            @endif

            @if($part->isLowStock())
                <div class="absolute top-2 right-2">
                    <x-ui.badge type="warning">Stok Terbatas</x-ui.badge>
                </div>
            @elseif($part->stock <= 0)
                <div class="absolute top-2 right-2">
                    <x-ui.badge type="danger">Habis</x-ui.badge>
                </div>
            @endif

            @if($part->category)
                <div class="absolute bottom-2 left-2">
                    <span class="bg-nero/80 backdrop-blur-sm border border-gunmetal/60 px-2 py-0.5 text-[9px] font-mono text-silver uppercase rounded">
                        {{ $part->category->name }}
                    </span>
                </div>
            @endif
        </a>

        {{-- Content --}}
        <div class="p-4">
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="font-mono text-[10px] text-giallo/80 font-semibold">{{ $part->sku }}</p>
                @if($part->brand)
                    <span class="font-heading text-[10px] uppercase text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded border border-gunmetal/30">{{ $part->brand }}</span>
                @endif
            </div>

            <h3 class="font-heading text-sm font-semibold text-silver-bright group-hover:text-giallo transition-premium mb-2 line-clamp-2 min-h-[40px]">
                <a href="{{ route('parts.show', $part->slug) }}">
                    {{ $part->name }}
                </a>
            </h3>

            @if($part->description)
                <p class="font-body text-xs text-silver-dim line-clamp-2 mb-3">
                    {{ Str::limit($part->description, 70) }}
                </p>
            @endif
        </div>
    </div>

    <div class="p-4 pt-0">
        <div class="flex items-end justify-between pt-3 border-t border-gunmetal/30">
            <div>
                <span class="block font-mono text-[9px] text-silver-dim uppercase">Harga</span>
                <span class="font-mono text-sm font-bold text-silver-bright">
                    Rp {{ number_format($part->price_idr, 0, ',', '.') }}
                </span>
            </div>
            <div class="text-right">
                <span class="font-mono text-[10px] block {{ $part->stock > 0 ? 'text-verde' : 'text-danger' }}">
                    {{ $part->stock > 0 ? 'Stok: ' . $part->stock : 'Stok Kosong' }}
                </span>
                <a href="{{ route('parts.show', $part->slug) }}" class="inline-block mt-1 font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider font-semibold">
                    Detail →
                </a>
            </div>
        </div>
    </div>
</div>
