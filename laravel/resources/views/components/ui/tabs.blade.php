{{--
  x-ui.tabs — AUDrive Tabs Component (Alpine.js)
  Usage: <x-ui.tabs :tabs="['Tab 1', 'Tab 2']">
            <x-slot name="tab1">Content 1</x-slot>
            <x-slot name="tab2">Content 2</x-slot>
         </x-ui.tabs>
--}}
@props(['tabs' => [], 'defaultTab' => 0])


<div x-data="{ activeTab: {{ $defaultTab }} }" {{ $attributes }}>
    {{-- Tab Headers --}}
    <div class="flex border-b border-gunmetal">
        @foreach($tabs as $index => $tab)
            <button
                @click="activeTab = {{ $index }}"
                :class="activeTab === {{ $index }} ? 'border-giallo text-giallo' : 'border-transparent text-silver-dim hover:text-silver'"
                class="px-4 py-3 font-heading text-xs uppercase tracking-wider border-b-2 transition-premium">
                {{ $tab }}
            </button>
        @endforeach
    </div>


    {{-- Tab Content --}}
    <div class="pt-4">
        @foreach($tabs as $index => $tab)
            <div x-show="activeTab === {{ $index }}" x-cloak>
                {{ ${'tab' . $index} ?? '' }}
            </div>
        @endforeach
    </div>
</div>



