<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Servis {{ $salesOrder->code }} - AUDrive Workshop</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: a4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.45;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #EA580C;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #0B0C0E;
            text-transform: uppercase;
        }
        .company-name span {
            color: #EA580C;
        }
        .company-subtitle {
            font-size: 9px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .company-address {
            font-size: 8.5px;
            color: #475569;
            margin-top: 4px;
            line-height: 1.35;
        }
        .doc-title-box {
            text-align: right;
            vertical-align: top;
        }
        .doc-title {
            font-size: 19px;
            font-weight: 800;
            color: #0B0C0E;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .doc-badge {
            display: inline-block;
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            padding: 3px 8px;
            font-size: 9px;
            font-family: monospace;
            font-weight: bold;
            color: #0B0C0E;
            border-radius: 3px;
        }
        .stamp-box {
            display: inline-block;
            margin-top: 6px;
            padding: 5px 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .stamp-paid {
            border: 2px solid #059669;
            color: #059669;
            background: #ECFDF5;
        }
        .stamp-unpaid {
            border: 2px solid #EA580C;
            color: #EA580C;
            background: #FFF7ED;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .info-card {
            width: 50%;
            border: 1px solid #E2E8F0;
            background: #F8FAFC;
            padding: 10px 12px;
            border-radius: 4px;
            vertical-align: top;
        }
        .info-card-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0B0C0E;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .info-card p {
            margin-bottom: 2.5px;
            font-size: 9px;
            color: #334155;
        }

        .vehicle-box {
            width: 100%;
            border: 1px solid #FED7AA;
            background: #FFF7ED;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .vehicle-box table {
            width: 100%;
            font-size: 9px;
        }
        .vehicle-box td {
            padding: 2px 4px;
        }
        .vehicle-box .v-label {
            color: #9A3412;
            width: 15%;
            font-weight: 600;
        }
        .vehicle-box .v-val {
            color: #0B0C0E;
            font-weight: bold;
            width: 35%;
        }

        .section-heading {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0B0C0E;
            margin: 10px 0 5px 0;
            border-left: 3px solid #EA580C;
            padding-left: 6px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .items-table th {
            background: #0B0C0E;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 7px 10px;
            border: 1px solid #0B0C0E;
        }
        .items-table td {
            padding: 7px 10px;
            border: 1px solid #E2E8F0;
            font-size: 9px;
            color: #1E293B;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background: #F8FAFC;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .summary-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .payment-notes-card {
            width: 55%;
            vertical-align: top;
            padding-right: 15px;
        }
        .totals-card {
            width: 45%;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E2E8F0;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 9px;
            border-bottom: 1px solid #E2E8F0;
        }
        .totals-table .label {
            color: #475569;
            font-weight: 600;
        }
        .totals-table .amount {
            text-align: right;
            font-family: monospace;
            font-size: 10px;
            font-weight: 700;
            color: #0B0C0E;
        }
        .totals-table .grand-total td {
            background: #0B0C0E;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            border-top: 2px solid #EA580C;
            padding: 8px 10px;
        }
        .totals-table .grand-total .amount {
            color: #FDBA74;
            font-size: 13px;
        }

        .terbilang-box {
            background: #FFFBEB;
            border: 1px dashed #F59E0B;
            padding: 7px 10px;
            border-radius: 4px;
            font-size: 8.5px;
            font-style: italic;
            color: #92400E;
            margin-top: 8px;
        }

        .warranty-box {
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 8px;
            color: #475569;
            margin-top: 8px;
            line-height: 1.35;
        }

        .signatures-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sig-cell {
            width: 40%;
            text-align: center;
            vertical-align: top;
        }
        .sig-space {
            width: 20%;
        }
        .sig-title {
            font-size: 9px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 45px;
        }
        .sig-name {
            font-size: 10px;
            font-weight: 700;
            color: #0B0C0E;
            border-top: 1px solid #1E293B;
            padding-top: 4px;
            display: inline-block;
            min-width: 150px;
        }
        .sig-sub {
            font-size: 8px;
            color: #64748B;
        }

        .footer {
            margin-top: 15px;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            text-align: center;
            font-size: 7.5px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    @php
        $items = $items ?? $salesOrder->items;
        $customer = $customer ?? $salesOrder->customer;
        $workOrder = $workOrder ?? ($salesOrder->workOrder ?? null);
        $vehicle = $workOrder?->vehicle ?? ($customer?->vehicles?->first() ?? null);
        $mechanic = $workOrder?->mechanic ?? null;
        $invoiceNumber = $invoice->number ?? ($salesOrder->invoice->number ?? 'INV-WO-' . $salesOrder->code);
        $isPaid = in_array(strtolower($salesOrder->status->value ?? (string)$salesOrder->status), ['paid', 'completed'], true);
        $payment = $salesOrder->payments->first();

        $serviceItems = $items->filter(fn($item) => empty($item->spare_part_id) && ($item->item_type ?? '') !== 'part');
        $partItems = $items->filter(fn($item) => !empty($item->spare_part_id) || ($item->item_type ?? '') === 'part');
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="company-name">AU<span>DRIVE</span> WORKSHOP</div>
                <div class="company-subtitle">Authorized Supercar Service, Maintenance & Diagnostic Center</div>
                <div class="company-address">
                    Graha AUDrive Workshop Wing, Kav. 88, SCBD, Jakarta Selatan 12190<br>
                    Workshop Emergency Hotline: +62 21 8088 9911 | WhatsApp: +62 811 900 8888<br>
                    Email: service@audrive.id | NPWP: 01.345.678.9-012.000
                </div>
            </td>
            <td class="doc-title-box">
                <div class="doc-title">FAKTUR SERVIS</div>
                <div class="doc-badge">NO: {{ $invoiceNumber }}</div>
                <br>
                @if($isPaid)
                    <div class="stamp-box stamp-paid">
                        ✓ LUNAS / VERIFIED PAID
                    </div>
                @else
                    <div class="stamp-box stamp-unpaid">
                        MENUNGGU PEMBAYARAN
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Metadata Section --}}
    <table class="meta-table">
        <tr>
            <td class="info-card">
                <div class="info-card-title">Pemilik Kendaraan (Customer)</div>
                <p><strong>{{ $customer?->user?->name ?? 'Walk-in Workshop Client' }}</strong></p>
                <p>Telepon: {{ $customer?->user?->phone ?? '-' }}</p>
                <p>Email: {{ $customer?->user?->email ?? '-' }}</p>
                <p>Alamat: {{ $customer?->address ?? 'Showroom Client' }}{{ $customer?->city ? ', ' . $customer->city : '' }}</p>
            </td>
            <td class="info-card">
                <div class="info-card-title">Informasi Work Order & Administrasi</div>
                <p>Kode Referensi WO: <strong>{{ $workOrder?->code ?? ($salesOrder->notes ?? 'WO-SYSTEM') }}</strong></p>
                <p>No. Transaksi POS: <strong>#{{ $salesOrder->code }}</strong></p>
                <p>Tanggal Masuk / Servis: <strong>{{ ($workOrder?->created_at ?? $salesOrder->created_at)->format('d F Y, H:i') }} WIB</strong></p>
                <p>Chief Mechanic / Advisor: <strong>{{ $mechanic?->name ?? ($salesOrder->creator?->name ?? 'Chief Mechanic') }}</strong></p>
            </td>
        </tr>
    </table>

    {{-- Vehicle Information Box --}}
    @if($vehicle)
        <div class="vehicle-box">
            <table>
                <tr>
                    <td class="v-label">Unit Kendaraan:</td>
                    <td class="v-val">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</td>
                    <td class="v-label">Nomor Polisi:</td>
                    <td class="v-val">{{ $vehicle->plate_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="v-label">Nomor Rangka (VIN):</td>
                    <td class="v-val" style="font-family: monospace;">{{ $vehicle->vin ?? '-' }}</td>
                    <td class="v-label">Odometer Masuk:</td>
                    <td class="v-val">{{ number_format($vehicle->mileage ?? 0, 0, ',', '.') }} KM</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Itemized Table: Jasa Servis & Suku Cadang --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;" class="text-center">Kategori</th>
                <th style="width: 42%;" class="text-left">Rincian Jasa Pengerjaan & Suku Cadang</th>
                <th style="width: 8%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Biaya (IDR)</th>
                <th style="width: 15%;" class="text-right">Subtotal (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                @php
                    $isPart = !empty($item->spare_part_id) || ($item->item_type ?? '') === 'part';
                @endphp
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <span style="font-size: 7.5px; font-weight: bold; text-transform: uppercase; padding: 2px 4px; border-radius: 2px; {{ $isPart ? 'background: #FED7AA; color: #9A3412;' : 'background: #E2E8F0; color: #334155;' }}">
                            {{ $isPart ? 'Suku Cadang' : 'Jasa Servis' }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $item->description ?: ($item->sparePart?->name ?? 'Jasa Perbaikan Supercar') }}</strong>
                        @if($item->sparePart)
                            <br><span style="font-size: 8px; color: #64748B;">Part Number: {{ $item->sparePart->part_number ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ $item->qty }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: bold;">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748B;">
                        Detail pekerjaan belum dicatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary & Settlement Details --}}
    <table class="summary-container">
        <tr>
            <td class="payment-notes-card">
                @if($isPaid && $payment)
                    <div style="border: 1px solid #CBD5E1; background: #F8FAFC; padding: 8px 10px; border-radius: 4px; font-size: 8.5px;">
                        <div style="font-weight: bold; color: #059669; text-transform: uppercase; margin-bottom: 3px;">
                            ✓ BUKTI PEMBAYARAN KASIR WORKSHOP
                        </div>
                        <p>Metode Pembayaran: <strong style="text-transform: uppercase;">{{ $payment->method }}</strong></p>
                        <p>Referensi Transaksi: <strong style="font-family: monospace;">{{ $payment->reference ?? '-' }}</strong></p>
                        <p>Waktu Pelunasan: <strong>{{ ($payment->paid_at ?? $salesOrder->updated_at)->format('d F Y, H:i') }} WIB</strong></p>
                        <p>Total Lunas: <strong>Rp {{ number_format($payment->amount_idr ?? $salesOrder->total_idr, 0, ',', '.') }}</strong></p>
                    </div>
                @else
                    <div style="border: 1px solid #CBD5E1; background: #FFF7ED; padding: 8px 10px; border-radius: 4px; font-size: 8.5px;">
                        <p style="color: #EA580C; font-weight: bold;">PEMBAYARAN BELUM LUNAS</p>
                        <p>Silakan selesaikan pembayaran di meja kasir AUDrive Workshop atau transfer via rekening BCA 588-099-8800 a.n PT AUDRIVE INDONESIA.</p>
                    </div>
                @endif

                <div class="terbilang-box">
                    <strong>Terbilang:</strong> {{ \App\Support\Terbilang::make($salesOrder->total_idr) }}
                </div>

                <div class="warranty-box">
                    <strong>Ketentuan Garansi Bengkel:</strong><br>
                    Semua pekerjaan servis & suku cadang resmi dilindungi garansi pengerjaan selama 30 (tiga puluh) hari atau 1.000 KM sejak tanggal faktur ini diterbitkan. Garansi tidak berlaku untuk kerusakan akibat kelalaian operasional atau kecelakaan.
                </div>
            </td>
            <td class="totals-card">
                <table class="totals-table">
                    <tr>
                        <td class="label">Total Biaya Pengerjaan & Suku Cadang</td>
                        <td class="amount">Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</td>
                    </tr>
                    @if($salesOrder->discount_idr > 0)
                        <tr>
                            <td class="label" style="color: #059669;">Diskon Servis / Voucher</td>
                            <td class="amount" style="color: #059669;">- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($salesOrder->tax_idr > 0)
                        <tr>
                            <td class="label">Pajak Pertambahan Nilai (PPN 11%)</td>
                            <td class="amount">Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td>TOTAL BIAYA SERVIS</td>
                        <td class="amount">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Sign-off Signatures --}}
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Pelanggan / Pemilik Kendaraan,</div>
                <div class="sig-name">{{ $customer?->user?->name ?? 'Pemilik Kendaraan' }}</div>
                <div class="sig-sub">Tanda Tangan Penerima Kendaraan</div>
            </td>
            <td class="sig-space"></td>
            <td class="sig-cell">
                <div class="sig-title">Kepala Bengkel / Service Manager,</div>
                <div class="sig-name">{{ $mechanic?->name ?? ($salesOrder->creator?->name ?? 'Chief Workshop Officer') }}</div>
                <div class="sig-sub">Quality Control & Service Approval</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Faktur servis ini merupakan tanda bukti pekerjaan resmi dari Bengkel Resmi AUDrive Performance Hub.<br>
        Simpan faktur ini untuk klaim garansi servis 30 hari.
    </div>
</body>
</html>
