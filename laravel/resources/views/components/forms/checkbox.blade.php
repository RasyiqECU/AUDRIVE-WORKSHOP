{{--
  x-forms.checkbox — AUDrive Form Checkbox
  Usage: <x-forms.checkbox name="agree" label="Saya setuju" />
--}}
@props([
    'name' => '',
    'label' => null,
    'checked' => false,
    'value' => '1',
])


<label class="inline-flex items-center gap-2 cursor-pointer group">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'w-4 h-4 rounded border-gunmetal bg-nero text-giallo focus:ring-giallo focus:ring-offset-0 focus:ring-offset-nero']) }}
    />
    @if($label)
        <span class="font-body text-sm text-silver-dim group-hover:text-silver-bright transition-premium">{{ $label }}</span>
    @endif
</label>
@error($name)<p class="text-[11px] text-danger mt-1">{{ $message }}</p>@enderror



