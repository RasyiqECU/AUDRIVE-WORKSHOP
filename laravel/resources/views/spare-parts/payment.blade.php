@extends('layouts.public')

@section('title', 'Pembayaran Pesanan #' . $salesOrder->code . ' — AUDrive')
@section('meta_description', 'Instruksi pembayaran pesanan suku cadang AUDrive.')

@section('content')
<section class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-carbon border border-gunmetal rounded-lg overflow-hidden shadow-2xl">
            {{-- Header Status --}}
            <div class="bg-steel/30 p-6 border-b border-gunmetal text-center">
                <div class="w-12 h-12 bg-giallo/20 border border-giallo/40 rounded-full flex items-center justify-center text-giallo text-xl mx-auto mb-3">
                    💳
                </div>
                <h1 class="font-display text-xl md:text-2xl font-bold text-silver-bright">
                    INSTRUKSI PEMBAYARAN
                </h1>
                <p class="font-mono text-xs text-silver-dim mt-1">No. Pesanan: <span class="text-giallo font-bold">{{ $salesOrder->code }}</span></p>
            </div>

            {{-- Body --}}
            <div class="p-6 md:p-8 space-y-6">
                {{-- Amount Box --}}
                <div class="bg-nero border border-gunmetal/60 p-5 rounded text-center">
                    <span class="font-mono text-xs text-silver-dim uppercase block mb-1">Total Yang Harus Dibayar</span>
                    <span class="font-mono text-3xl font-bold text-giallo">
                        Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}
                    </span>
                    <p class="font-mono text-[10px] text-silver-dim mt-1">Status: <span class="text-warning font-semibold">{{ strtoupper($salesOrder->status->value ?? 'PENDING') }}</span></p>
                </div>

                {{-- Bank Accounts --}}
                <div class="space-y-3">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">Pilih Rekening Transfer Resmi:</h3>

                    <div class="p-4 bg-steel/20 border border-gunmetal/40 rounded flex items-center justify-between">
                        <div>
                            <span class="font-heading text-xs font-bold text-silver-bright uppercase">BCA Virtual Account / Rekening</span>
                            <p class="font-mono text-xs text-giallo font-bold mt-0.5">8830 1928 3829 001</p>
                            <span class="font-body text-[10px] text-silver-dim">a.n. PT AUDRIVE INDONESIA UTAMA</span>
                        </div>
                        <button type="button" onclick="navigator.clipboard.writeText('883019283829001'); alert('Nomor rekening disalin!')"
                                class="px-3 py-1 bg-steel/40 border border-gunmetal hover:border-giallo text-silver-dim hover:text-silver-bright font-mono text-xs rounded transition">
                            Salin
                        </button>
                    </div>

                    <div class="p-4 bg-steel/20 border border-gunmetal/40 rounded flex items-center justify-between">
                        <div>
                            <span class="font-heading text-xs font-bold text-silver-bright uppercase">Bank Mandiri</span>
                            <p class="font-mono text-xs text-giallo font-bold mt-0.5">168 00 9988 7712</p>
                            <span class="font-body text-[10px] text-silver-dim">a.n. PT AUDRIVE INDONESIA UTAMA</span>
                        </div>
                        <button type="button" onclick="navigator.clipboard.writeText('1680099887712'); alert('Nomor rekening disalin!')"
                                class="px-3 py-1 bg-steel/40 border border-gunmetal hover:border-giallo text-silver-dim hover:text-silver-bright font-mono text-xs rounded transition">
                            Salin
                        </button>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="p-4 bg-steel/10 border border-gunmetal/30 rounded font-body text-xs text-silver-dim space-y-1.5">
                    <p class="font-heading font-semibold text-silver-bright uppercase text-[10px]">Panduan Konfirmasi:</p>
                    <p>1. Lakukan transfer tepat hingga digit terakhir.</p>
                    <p>2. Simpan bukti transfer atau struk transaksi Anda.</p>
                    <p>3. Konfirmasi pembayaran ke CS WhatsApp dengan melampirkan no. pesanan <strong>{{ $salesOrder->code }}</strong>.</p>
                </div>

                {{-- Buttons --}}
                <div class="pt-4 border-t border-gunmetal/40 flex flex-wrap gap-3">
                    <a href="https://wa.me/6281122334455?text=Halo%20AUDrive,%20saya%20sudah%20melakukan%20pembayaran%20untuk%20pesanan%20nomor%20{{ $salesOrder->code }}" target="_blank"
                       class="flex-1 py-3 bg-verde hover:bg-verde/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded text-center transition flex items-center justify-center gap-2">
                        <span>💬 Konfirmasi via WhatsApp</span>
                    </a>
                    <a href="{{ route('parts.index') }}" class="px-5 py-3 border border-gunmetal hover:border-silver-dim text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition">
                        Selesai
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
