{{--
  x-forms.input — AUDrive Form Input
  Usage: <x-forms.input name="email" label="Email" type="email" required />
--}}
@props([
    'name' => '',
    'label' => null,
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'help' => null,
    'icon' => null,
])


<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block font-heading text-xs uppercase tracking-wider text-silver-dim">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif
    <div class="relative">
        @if($icon)
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-silver-dim text-sm">{{ $icon }}</span>
        @endif
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full bg-nero border border-gunmetal ' . ($icon ? 'pl-9 ' : '') . 'px-4 py-2.5 text-silver-bright font-body text-sm placeholder:text-silver-dim/40 focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium']) }}
        />
    </div>
    @if($help)<p class="text-[11px] text-silver-dim">{{ $help }}</p>@endif
    @error($name)<p class="text-[11px] text-danger">{{ $message }}</p>@enderror
</div>



