{{--
  x-forms.textarea — AUDrive Form Textarea
  Usage: <x-forms.textarea name="notes" label="Catatan" rows="4" />
--}}
@props([
    'name' => '',
    'label' => null,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'rows' => 4,
])


<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block font-heading text-xs uppercase tracking-wider text-silver-dim">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium resize-y']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)<p class="text-[11px] text-danger">{{ $message }}</p>@enderror
</div>



