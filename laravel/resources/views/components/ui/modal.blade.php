{{--
  x-ui.modal — AUDrive Modal Component (Alpine.js)
  Usage: <x-ui.modal name="myModal" title="Title">Content</x-ui.modal>
  Open: @click="$dispatch('open-modal', 'myModal')"
--}}
@props([
    'name',
    'title' => null,
    'maxWidth' => 'md', // sm, md, lg, xl, 2xl
    'closeable' => true,
])


@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $widthClass = $widths[$maxWidth] ?? $widths['md'];
@endphp


<div x-data="{ show: false }"
     @open-modal.window="if ($event.detail === '{{ $name }}') show = true"
     @close-modal.window="if ($event.detail === '{{ $name }}') show = false"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="{{ $name }}-title"
     role="dialog"
     aria-modal="true">


    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"
         @click="@if($closeable) show = false @endif"></div>


    {{-- Modal Content --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             {{ $attributes->merge(['class' => "relative w-full {$widthClass} bg-carbon border border-gunmetal shadow-panel transform transition-all"]) }}>


            {{-- Header --}}
            @if($title)
                <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal flex items-center justify-between">
                    <h3 id="{{ $name }}-title" class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">
                        {{ $title }}
                    </h3>
                    @if($closeable)
                        <button @click="show = false" class="text-silver-dim hover:text-silver-bright transition-premium">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            @endif


            {{-- Body --}}
            <div class="p-6">
                {{ $slot }}
            </div>


            {{-- Footer --}}
            @isset($footer)
                <div class="px-6 py-4 border-t border-gunmetal bg-steel/20">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>



