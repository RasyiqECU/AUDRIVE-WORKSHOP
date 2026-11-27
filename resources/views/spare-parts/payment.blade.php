@extends('layouts.public')

@section('title', 'Pembayaran Pesanan #' . $salesOrder->code . ' — AUDrive')
@section('meta_description', 'Pembayaran pesanan suku cadang AUDrive.')

@section('content')
@php
    $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none';
    $accounts = \App\Services\PaymentSettings::accounts();
    $lastPayment = $salesOrder->payments()->whereNotNull('proof_path')->latest('id')->first();
@endphp
<section class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-carbon border border-gunmetal rounded-lg p-6 md:p-8 space-y-6 text-xs">
        <div class="text-center space-y-1">
            <h1 class="font-display text-xl font-bold text-silver-bright">PEMBAYARAN #{{ $salesOrder->code }}</h1>
            <p class="font-mono text-3xl font-bold text-giallo">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</p>
            <p class="text-silver-dim">Status: <span class="text-silver-bright">{{ $salesOrder->progressLabel() }}</span></p>
            @if($salesOrder->reserved_until && $salesOrder->status->value === 'pending')
                <p class="text-giallo">Bayar sebelum {{ $salesOrder->reserved_until->format('d M Y H:i') }}, setelah itu pesanan dibatalkan.</p>
            @endif
        </div>

        @if($errors->any())
            <ul class="border border-danger/40 text-danger p-3 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif

        @if($salesOrder->awaitsProof())
            @if($lastPayment?->rejection_reason)
                <p class="border border-danger/40 text-danger p-3">Bukti sebelumnya ditolak: {{ $lastPayment->rejection_reason }}</p>
            @endif

            <div class="space-y-2">
                <h2 class="font-heading uppercase text-silver-bright">1. Transfer ke salah satu rekening</h2>
                @foreach($accounts as $account)
                    <div class="p-3 bg-steel/20 border border-gunmetal/40 rounded">
                        <span class="text-silver-bright font-bold">{{ $account['bank'] }}</span>
                        <p class="font-mono text-giallo font-bold">{{ $account['number'] }}</p>
                        <span class="text-silver-dim">a.n. {{ $account['holder'] }}</span>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('parts.orders.proof', $salesOrder) }}" enctype="multipart/form-data" class="space-y-3 text-silver-dim">
                @csrf
                <h2 class="font-heading uppercase text-silver-bright">2. Unggah bukti transfer</h2>
                <label class="block">Nama pengirim<input type="text" name="sender_name" required maxlength="100" value="{{ old('sender_name', auth()->user()->name) }}" class="{{ $field }}"></label>
                <label class="block">Bank tujuan
                    <select name="destination_bank" required class="{{ $field }}">
                        @foreach($accounts as $account)
                            <option value="{{ $account['bank'] }}" @selected(old('destination_bank') === $account['bank'])>{{ $account['bank'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">Nominal yang ditransfer<input type="number" name="amount_idr" required min="1" value="{{ old('amount_idr', $salesOrder->total_idr) }}" class="{{ $field }}"></label>
                <label class="block">Foto bukti (JPG/PNG/WEBP, maks. 2 MB)<input type="file" name="proof" required accept="image/jpeg,image/png,image/webp" class="{{ $field }}"></label>
                <button type="submit" class="w-full py-3 bg-giallo text-nero font-heading font-bold uppercase rounded">Kirim Bukti Transfer</button>
            </form>
        @elseif($salesOrder->status->value === 'pending' && $salesOrder->isOnlineTransfer())
            <p class="text-silver text-center">Bukti Anda sedang diperiksa Kasir.</p>
        @elseif($salesOrder->status->value === 'pending')
            <p class="text-silver text-center">Datang ke kasir AUDrive dan sebutkan nomor pesanan <strong>{{ $salesOrder->code }}</strong>.</p>
        @else
            <p class="text-silver text-center">Pesanan ini tidak menunggu pembayaran.</p>
        @endif

        @if($lastPayment)
            <p class="text-center"><a href="{{ route('payments.proof', $lastPayment) }}" target="_blank" rel="noopener" class="text-giallo underline">Lihat bukti yang diunggah</a></p>
        @endif

        <div class="text-center pt-3 border-t border-gunmetal/40">
            <a href="{{ $salesOrder->customerUrl() }}" class="text-giallo underline">Detail pesanan</a>
        </div>
    </div>
</section>
@endsection
