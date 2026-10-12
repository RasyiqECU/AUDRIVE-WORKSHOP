{{--
  x-ui.table — AUDrive Table Component
  Usage: <x-ui.table>
            <x-slot name="head">
                <x-ui.table.th>Name</x-ui.table.th>
            </x-slot>
            <x-slot name="body">
                <tr>
                    <x-ui.table.td>Value</x-ui.table.td>
                </tr>
            </x-slot>
         </x-ui.table>
--}}
@props([])


<div {{ $attributes->merge(['class' => 'overflow-x-auto border border-gunmetal']) }}>
    <table class="w-full text-sm">
        @isset($head)
            <thead>
                <tr class="bg-steel/30 border-b border-gunmetal">
                    {{ $head }}
                </tr>
            </thead>
        @endisset
        @isset($body)
            <tbody class="divide-y divide-gunmetal/30">
                {{ $body }}
            </tbody>
        @endisset
    </table>
</div>


 	
