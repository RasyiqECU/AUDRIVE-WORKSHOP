{{--
  x-ui.empty-state — Empty State Placeholder
  Usage: <x-ui.empty-state icon="📦" title="No Data" description="No items found" />
--}}
@props([
    'icon' => '📦',
    'title' => 'Tidak ada data',
    'description' => null,
])


<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4 text-center']) }}>
    <span class="text-5xl mb-4 opacity-40">{{ $icon }}</span>
    <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">{{ $title }}</h3>
    @if($description)
        <p class="font-body text-xs text-silver-dim max-w-md">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>



