{{--
  x-workshop.work-order-card — Work Order Card
  Usage: <x-workshop.work-order-card :workOrder="$workOrder" />
--}}
@props(['workOrder'])


<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal p-4 dashboard-card']) }}>
    <div class="flex items-start justify-between mb-3">
        <div>
            <p class="font-mono text-xs text-giallo font-bold">{{ $workOrder->code }}</p>
            <p class="font-heading text-sm text-silver-bright mt-1">{{ $workOrder->vehicle->full_name ?? '-' }}</p>
        </div>
        <x-ui.status-pill :status="$workOrder->status->value" />
    </div>


    <div class="grid grid-cols-2 gap-2 mb-3">
        <div class="bg-steel/20 p-2 border border-gunmetal/30">
            <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim">Mekanik</p>
            <p class="font-heading text-xs text-silver-bright">{{ $workOrder->mechanic->name ?? 'Belum ditugaskan' }}</p>
        </div>
        <div class="bg-steel/20 p-2 border border-gunmetal/30">
            <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim">Total</p>
            <p class="font-mono text-xs text-giallo">Rp {{ number_format($workOrder->total_idr, 0, ',', '.') }}</p>
        </div>
    </div>


    <div class="flex items-center justify-between pt-3 border-t border-gunmetal/30">
        <span class="font-mono text-[10px] text-silver-dim">{{ $workOrder->created_at->format('d M Y') }}</span>
        <a href="#" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">Detail →</a>
    </div>
</div>



