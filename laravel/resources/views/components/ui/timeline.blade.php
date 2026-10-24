{{--
  x-ui.timeline — AUDrive Timeline Component
  Usage: <x-ui.timeline>
            <x-ui.timeline-item title="Step 1" time="10:00" status="completed">Description</x-ui.timeline-item>
         </x-ui.timeline>
--}}
@props([])


<div {{ $attributes->merge(['class' => 'relative space-y-0']) }}>
    {{ $slot }}
</div>


{{-- Timeline Item --}}
{{-- Usage: <x-ui.timeline-item title="Title" time="10:00" status="completed|active|pending">Content</x-ui.timeline-item> --}}



