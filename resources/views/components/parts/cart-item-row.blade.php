{{--
  x-parts.cart-item-row — Cart Item Row
  Usage: <x-parts.cart-item-row :item="$cartItem" />
--}}
@props(['item'])


<tr class="border-b border-gunmetal/30 hover:bg-steel/20 transition-premium">
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-steel/30 border border-gunmetal/30 flex items-center justify-center text-xl">⚙️</div>
            <div>
                <p class="font-heading text-sm text-silver-bright">{{ $item->sparePart->name ?? '-' }}</p>
                <p class="font-mono text-[10px] text-silver-dim">{{ $item->sparePart->sku ?? '-' }}</p>
            </div>
        </div>
    </td>
    <td class="px-4 py-3 text-right font-mono text-xs text-silver-bright">Rp {{ number_format($item->sparePart->price_idr ?? 0, 0, ',', '.') }}</td>
    <td class="px-4 py-3 text-center">
        <form method="POST" action="#" class="inline-flex items-center gap-1">
            @csrf
            @method('PATCH')
            <input type="number" name="qty" value="{{ $item->qty }}" min="1"
                   class="w-14 bg-nero border border-gunmetal text-center text-xs text-silver-bright py-1 focus:border-giallo focus:outline-none" />
        </form>
    </td>
    <td class="px-4 py-3 text-right font-mono text-xs text-giallo font-bold">Rp {{ number_format(($item->sparePart->price_idr ?? 0) * $item->qty, 0, ',', '.') }}</td>
    <td class="px-4 py-3 text-right">
        <form method="POST" action="#" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-danger/60 hover:text-danger transition-premium text-xs">✕</button>
        </form>
    </td>
</tr>



