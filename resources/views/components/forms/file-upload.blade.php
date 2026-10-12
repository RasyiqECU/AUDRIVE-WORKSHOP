{{--
  x-forms.file-upload — AUDrive File Upload with Preview
  Usage: <x-forms.file-upload name="photo" label="Upload Foto" accept="image/*" />
--}}
@props([
    'name' => '',
    'label' => null,
    'accept' => '*',
    'multiple' => false,
    'required' => false,
])


<div x-data="{ files: [], preview: null }" class="space-y-1.5">
    @if($label)
        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif


    <div class="relative border-2 border-dashed border-gunmetal hover:border-giallo/40 transition-premium p-6 text-center cursor-pointer group"
         @click="$refs.fileInput.click()"
         @dragover.prevent="$el.classList.add('border-giallo')"
         @dragleave.prevent="$el.classList.remove('border-giallo')"
         @drop.prevent="files = $event.dataTransfer.files; if(files[0] && files[0].type.startsWith('image/')) { preview = URL.createObjectURL(files[0]); }">


        <input type="file" name="{{ $name }}" {{ $multiple ? 'multiple' : '' }} accept="{{ $accept }}"
               x-ref="fileInput" class="hidden"
               @change="files = $event.target.files; if(files[0] && files[0].type.startsWith('image/')) { preview = URL.createObjectURL(files[0]); }"
               {{ $required ? 'required' : '' }} />


        <template x-if="!preview">
            <div>
                <span class="text-3xl text-gunmetal group-hover:text-giallo/50 transition-premium block mb-2">⬆</span>
                <p class="font-heading text-xs text-silver-dim uppercase tracking-wider">Klik atau drag file ke sini</p>
                <p class="font-mono text-[10px] text-silver-dim/50 mt-1">Maks 5MB</p>
            </div>
        </template>


        <template x-if="preview">
            <img :src="preview" class="max-h-32 mx-auto border border-gunmetal" />
        </template>
    </div>


    <template x-if="files.length > 0">
        <p class="font-mono text-[10px] text-verde" x-text="files.length + ' file dipilih'"></p>
    </template>


    @error($name)<p class="text-[11px] text-danger">{{ $message }}</p>@enderror
</div>



