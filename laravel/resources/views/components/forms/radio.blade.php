{{--
  x-forms.radio — AUDrive Form Radio
  Usage: <x-forms.radio name="type" value="car" label="Mobil" />
--}}
@props([
    'name' => '',
    'label' => null,
    'value' => '',
    'checked' => false,
])


<label class="inline-flex items-center gap-2 cursor-pointer group">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'w-4 h-4 border-gunmetal bg-nero text-giallo focus:ring-giallo focus:ring-offset-0 focus:ring-offset-nero']) }}
    />
    @if($label)
        <span class="font-body text-sm text-silver-dim group-hover:text-silver-bright transition-premium">{{ $label }}</span>
    @endif
</label>



