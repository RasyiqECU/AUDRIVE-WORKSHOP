{{--
  x-forms.search — AUDrive Search Input
  Usage: <x-forms.search name="q" placeholder="Cari mobil..." />
--}}
@props([
    'name' => 'q',
    'placeholder' => 'Cari...',
    'value' => '',
    'action' => null,
])


<form method="GET" action="{{ $action }}" class="relative">
    <input
        type="search"
        name="{{ $name }}"
        value="{{ request($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full bg-nero border border-gunmetal pl-10 pr-4 py-2.5 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium']) }}
    />
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-silver-dim">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </span>
</form>



