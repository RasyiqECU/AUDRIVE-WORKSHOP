{{--
  x-workshop.service-item-row — Work Order Item Row
  Usage: <x-workshop.service-item-row :item="$item" />
--}}
@props(['item'])


<tr class="border-b border-gunmetal/30 hover:bg-steel/20 transition-premium">
    <td class="px-4 py-3">
        <x-ui.badge type="{{ item->type==='part'?'info':(item->type === 'service' ? 'success' : 'neutral') }}" :dot="false">
            {{ ucfirst($item->type) }}
        </x-ui.badge>
    </td>
    <td class="px-4 py-3 text-silver-bright text-sm">{{ $item->description }}</td>
    <td class="px-4 py-3 text-center font-mono text-xs text-silver-dim">{{ $item->qty }}</td>
    <td class="px-4 py-3 text-right font-mono text-xs text-silver-bright">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
    <td class="px-4 py-3 text-right font-mono text-xs text-giallo font-bold">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
</tr>



