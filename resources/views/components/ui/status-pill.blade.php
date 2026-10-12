{{--
  x-ui.status-pill — Status Pill Badge (Dashboard Style)
  Usage: <x-ui.status-pill status="available" />
--}}
@props(['status' => ''])


@php
    $statuses = [
        'available' => ['label' => 'Tersedia', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'reserved' => ['label' => 'Dipesan', 'class' => 'border-arancio/40 text-arancio bg-arancio/5'],
        'sold' => ['label' => 'Terjual', 'class' => 'border-giallo/40 text-giallo bg-giallo/5'],
        'draft' => ['label' => 'Draft', 'class' => 'border-silver/20 text-silver-dim bg-steel/30'],
        'pending' => ['label' => 'Menunggu', 'class' => 'border-giallo/40 text-giallo bg-giallo/5'],
        'in_progress' => ['label' => 'Diproses', 'class' => 'border-arancio/40 text-arancio bg-arancio/5'],
        'completed' => ['label' => 'Selesai', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'border-danger/40 text-danger bg-danger/5'],
        'active' => ['label' => 'Aktif', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'inactive' => ['label' => 'Nonaktif', 'class' => 'border-silver/20 text-silver-dim bg-steel/30'],
    ];
    $style = $statuses[$status] ?? ['label' => ucfirst($status), 'class' => 'border-silver/20 text-silver-dim bg-steel/30'];
@endphp


<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 border font-heading font-semibold text-[10px] uppercase tracking-wider {$style['class']}"]) }}
      style="clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 6px, 100% 100%, 6px 100%, 0 calc(100% - 6px));">
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    {{ $style['label'] }}
</span>



