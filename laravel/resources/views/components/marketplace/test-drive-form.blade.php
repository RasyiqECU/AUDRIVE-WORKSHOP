{{--
  x-marketplace.test-drive-form — Test Drive Booking Form
  Usage: <x-marketplace.test-drive-form :car="$car" />
--}}
@props(['car'])


<form method="POST" action="{{ route('marketplace.test-drive.store', $car->slug) }}" {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @csrf
    <x-forms.date-picker name="scheduled_at" label="Tanggal Test Drive" required min="{{ now()->addDay()->format('Y-m-d') }}" />
    <x-forms.select name="time_slot" label="Waktu" required :options="[
        '09:00' => '09:00 - 10:00',
        '10:00' => '10:00 - 11:00',
        '11:00' => '11:00 - 12:00',
        '13:00' => '13:00 - 14:00',
        '14:00' => '14:00 - 15:00',
        '15:00' => '15:00 - 16:00',
    ]" />
    <x-forms.textarea name="notes" label="Catatan Tambahan" rows="2" />
    <x-ui.button type="submit" variant="primary" fullWidth>Booking Test Drive</x-ui.button>
</form>



