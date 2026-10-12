{{-- x-ui.table.th — Table Header Cell --}}
@props(['sortable' => false, 'direction' => null])


<th {{ $attributes->merge(['class' => 'text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim']) }}>
    @if($sortable)
        <button class="flex items-center gap-1 hover:text-giallo transition-premium">
            {{ $slot }}
            @if($direction === 'asc')
                <span class="text-giallo">↑</span>
            @elseif($direction === 'desc')
                <span class="text-giallo">↓</span>
            @else
                <span class="opacity-30">↕</span>
            @endif
        </button>
    @else
        {{ $slot }}
    @endif
</th>



