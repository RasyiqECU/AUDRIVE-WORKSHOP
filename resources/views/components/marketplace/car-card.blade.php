{{--
  x-marketplace.car-card — Car Unit Card for Marketplace
  Usage: <x-marketplace.car-card :car="$car" />
--}}
@props(['car'])


<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden dashboard-card group']) }}>
    {{-- Image --}}
    <div class="relative aspect-[16/10] bg-steel/30 overflow-hidden">
        @if($car->primaryPhoto)
            <img src="{{ asset('storage/' . $car->primaryPhoto->path) }}" alt="{{ $car->full_name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-premium duration-500" />
        @else
            <div class="w-full h-full flex items-center justify-center text-gunmetal">
                <span class="text-5xl">🏎</span>
            </div>
        @endif


        {{-- Status Badge --}}
        <div class="absolute top-3 left-3">
            <x-ui.status-pill :status="$car->status->value" />
        </div>


        {{-- Featured Badge --}}
        @if($car->featured_at)
            <div class="absolute top-3 right-3">
                <span class="bg-giallo text-nero font-heading text-[9px] font-bold uppercase tracking-wider px-2 py-0.5">★ Featured</span>
            </div>
        @endif
    </div>


    {{-- Content --}}
    <div class="p-4">
        <div class="flex items-start justify-between mb-2">
            <div>
                <h3 class="font-heading text-sm font-semibold text-silver-bright group-hover:text-giallo transition-premium">
                    {{ $car->full_name }}
                </h3>
                <p class="font-mono text-[10px] text-silver-dim">{{ $car->code }}</p>
            </div>
        </div>


        {{-- Specs --}}
        <div class="grid grid-cols-3 gap-2 mb-3">
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">KM</p>
                <p class="font-heading text-xs text-silver-bright">{{ number_format($car->mileage_km, 0, ',', '.') }}</p>
            </div>
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">TRANS</p>
                <p class="font-heading text-xs text-silver-bright uppercase">{{ $car->transmission }}</p>
            </div>
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">YEAR</p>
                <p class="font-heading text-xs text-silver-bright">{{ $car->year }}</p>
            </div>
        </div>


        {{-- Price --}}
        <div class="flex items-end justify-between pt-3 border-t border-gunmetal/30">
            <x-ui.price :amount="$car->price_sell_idr" size="md" />
            <a href="{{ route('marketplace.show', $car->slug) }}" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                Detail →
            </a>
        </div>
    </div>
</div>



