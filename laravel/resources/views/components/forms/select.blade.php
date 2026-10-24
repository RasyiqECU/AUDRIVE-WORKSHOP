{{--
  x-forms.select — AUDrive Form Select
  Usage: <x-forms.select name="brand" label="Merek" :options="$brands" />
--}}
@props([
    'name' => '',
    'label' => null,
    'options' => [],
    'required' => false,
    'placeholder' => 'Pilih...',
    'value' => '',
])


<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block font-heading text-xs uppercase tracking-wider text-silver-dim">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright font-body text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium appearance-none']) }}
    >
        @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" {{ old($name, $value) == $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)<p class="text-[11px] text-danger">{{ $message }}</p>@enderror
</div>



