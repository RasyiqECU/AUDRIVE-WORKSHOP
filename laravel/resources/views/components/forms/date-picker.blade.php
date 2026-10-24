{{--
  x-forms.date-picker — AUDrive Date Picker
  Usage: <x-forms.date-picker name="date" label="Tanggal" />
--}}
@props([
    'name' => '',
    'label' => null,
    'required' => false,
    'value' => '',
    'min' => null,
    'max' => null,
])


<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block font-heading text-xs uppercase tracking-wider text-silver-dim">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <input
        type="date"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($min) min="{{ $min }}" @endif
        @if($max) max="{{ $max }}" @endif
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright font-body text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium']) }}
    />
    @error($name)<p class="text-[11px] text-danger">{{ $message }}</p>@enderror
</div>



