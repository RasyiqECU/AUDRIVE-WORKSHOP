{{--
  x-ui.speed-line — Decorative Speed Lines
  Usage: <x-ui.speed-line />
--}}
<div {{ $attributes->merge(['class' => 'relative h-px w-full overflow-hidden']) }}>
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-giallo/20 to-transparent"></div>
    <div class="absolute inset-0 bg-speed-lines opacity-50"></div>
</div>



