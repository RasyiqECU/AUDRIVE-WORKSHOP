{{--
  x-forms.filter-panel — AUDrive Filter Panel
  Usage: <x-forms.filter-panel action="/marketplace">
            <x-forms.select name="brand" label="Merek" :options="$brands" />
         </x-forms.filter-panel>
--}}
@props([
    'action' => '',
    'method' => 'GET',
    'collapsible' => true,
])


<div x-data="{ open: {{ $collapsible ? 'false' : 'true' }} }" {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal']) }}>
    <div class="px-4 py-3 border-b border-gunmetal flex items-center justify-between">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
            <span class="text-giallo">⚙</span> Filter
        </h3>
        @if($collapsible)
            <button @click="open = !open" class="text-silver-dim hover:text-giallo transition-premium text-xs font-heading uppercase tracking-wider">
                <span x-show="!open">Tampilkan</span>
                <span x-show="open" x-cloak>Sembunyikan</span>
            </button>
        @endif
    </div>


    <form method="{{ $method }}" action="{{ $action }}" x-show="open" x-collapse x-cloak>
        <div class="p-4 space-y-4">
            {{ $slot }}
        </div>
        <div class="px-4 py-3 border-t border-gunmetal bg-steel/20 flex items-center justify-end gap-2">
            <a href="{{ $action }}" class="font-heading text-xs text-silver-dim hover:text-silver uppercase tracking-wider transition-premium">Reset</a>
            <x-ui.button type="submit" size="sm">Terapkan</x-ui.button>
        </div>
    </form>
</div>



