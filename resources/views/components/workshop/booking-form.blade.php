{{--
  x-workshop.booking-form — Service Booking Form
  Usage: <x-workshop.booking-form :vehicles="$vehicles" />
--}}
@props(['vehicles' => collect()])


<form method="POST" action="{{ route('workshop.booking.store') }}" {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @csrf
    <x-forms.select name="customer_vehicle_id" label="Pilih Kendaraan" required :options="$vehicles->pluck('full_name', 'id')->toArray()" />
    <x-forms.select name="service_type" label="Jenis Servis" required :options="[
        'regular_service' => 'Servis Rutin',
        'heavy_repair' => 'Perbaikan Berat',
        'body_repair' => 'Perbaikan Body',
        'ac_service' => 'Servis AC',
        'inspection' => 'Inspeksi',
    ]" />
    <div class="grid grid-cols-2 gap-4">
        <x-forms.date-picker name="preferred_date" label="Tanggal" required min="{{ now()->addDay()->format('Y-m-d') }}" />
        <x-forms.select name="preferred_time" label="Waktu" required :options="[
            '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00',
            '11:00' => '11:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
        ]" />
    </div>
    <x-forms.textarea name="notes" label="Keluhan / Catatan" rows="3" placeholder="Jelaskan keluhan kendaraan Anda..." />
    <x-ui.button type="submit" variant="primary" fullWidth>Booking Servis</x-ui.button>
</form>



