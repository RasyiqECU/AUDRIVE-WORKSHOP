{{--
  x-marketplace.offer-form — Make Offer Form
  Usage: <x-marketplace.offer-form :car="$car" />
--}}
@props(['car'])


<form method="POST" action="{{ route('marketplace.offer.store', $car->slug) }}" {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @csrf
    <x-forms.input name="amount_idr" label="Jumlah Penawaran (Rp)" type="number" required placeholder="500000000" />
    <x-forms.textarea name="note" label="Catatan" placeholder="Tulis pesan Anda..." rows="3" />
    <x-ui.button type="submit" variant="primary" fullWidth>Kirim Penawaran</x-ui.button>
</form>



