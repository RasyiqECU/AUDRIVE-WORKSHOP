{{--
    AUDrive Form Button Component
    Usage: <x-forms.button type="submit">Simpan</x-forms.button>
--}}
@props([
    'type' => 'submit',
    'variant' => 'primary',
    'loading' => false,
])

@php
    $variants = [
        'primary'   => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost'     => 'btn-ghost',
        'danger'    => 'btn-danger',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ?? false ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "{$variantClass} inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"]) }}
>
    @if($loading)
        <span class="spinner-audrive"></span>
    @endif
    {{ $slot }}
</button>
