{{-- x-ui.table.td — Table Data Cell --}}
@props([])


<td {{ $attributes->merge(['class' => 'px-4 py-3 text-silver-bright']) }}>
    {{ $slot }}
</td>



