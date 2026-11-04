<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Servis {{ $workOrder->code }} - AUDrive</title>
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
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.45;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #0284C7;
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
            color: #0284C7;
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
            font-size: 18px;
            font-weight: 800;
            color: #0B0C0E;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .doc-badge {
            display: inline-block;
            background: #F0F9FF;
            border: 1px solid #BAE6FD;
            padding: 3px 8px;
            font-size: 9px;
            font-family: monospace;
            font-weight: bold;
            color: #0369A1;
            border-radius: 3px;
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
            border: 1px solid #BAE6FD;
            background: #F0F9FF;
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
            color: #0369A1;
            width: 15%;
            font-weight: 600;
        }
        .vehicle-box .v-val {
            color: #0B0C0E;
            font-weight: bold;
            width: 35%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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
            border-top: 2px solid #0284C7;
            padding: 8px 10px;
        }
        .totals-table .grand-total .amount {
            color: #38BDF8;
            font-size: 13px;
        }

        .checklist-box {
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 8.5px;
            color: #334155;
            line-height: 1.4;
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
        $customer = $customer ?? $workOrder->vehicle?->customer;
        $vehicle = $vehicle ?? $workOrder->vehicle;
        $items = $items ?? $workOrder->items;
        $mechanic = $workOrder->mechanic ?? null;
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="company-name">AU<span>DRIVE</span> WORKSHOP</div>
                <div class="company-subtitle">Tanda Terima & Kwitansi Resmi Pengerjaan Servis</div>
                <div class="company-address">
                    Graha AUDrive Workshop Wing, Kav. 88, SCBD, Jakarta Selatan 12190<br>
                    Workshop Emergency Hotline: +62 21 8088 9911 | Email: service@audrive.id
                </div>
            </td>
            <td class="doc-title-box">
                <div class="doc-title">TANDA TERIMA SERVIS</div>
                <div class="doc-badge">WO: {{ $workOrder->code }}</div>
            </td>
        </tr>
    </table>

    {{-- Metadata Section --}}
    <table class="meta-table">
        <tr>
            <td class="info-card">
                <div class="info-card-title">Data Pemilik Kendaraan</div>
                <p><strong>{{ $customer?->user?->name ?? 'Pelanggan Workshop' }}</strong></p>
                <p>Telepon: {{ $customer?->user?->phone ?? '-' }}</p>
                <p>Email: {{ $customer?->user?->email ?? '-' }}</p>
                <p>Alamat: {{ $customer?->address ?? '-' }}{{ $customer?->city ? ', ' . $customer->city : '' }}</p>
            </td>
            <td class="info-card">
                <div class="info-card-title">Data Administrasi Work Order</div>
                <p>No. Work Order: <strong>#{{ $workOrder->code }}</strong></p>
                <p>Tanggal Servis: <strong>{{ $workOrder->created_at->format('d F Y, H:i') }} WIB</strong></p>
                <p>Mekanik Bertugas: <strong>{{ $mechanic?->name ?? 'Tim Teknisi AUDrive' }}</strong></p>
                <p>Status Pengerjaan: <strong style="text-transform: uppercase;">{{ $workOrder->status->value ?? (string)$workOrder->status }}</strong></p>
            </td>
        </tr>
    </table>

    {{-- Vehicle Box --}}
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
                    <td class="v-label">Odometer:</td>
                    <td class="v-val">{{ number_format($vehicle->mileage ?? 0, 0, ',', '.') }} KM</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Details Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">No</th>
                <th style="width: 16%;" class="text-center">Tipe</th>
                <th style="width: 48%;" class="text-left">Deskripsi Pengerjaan / Komponen Diganti</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 20%;" class="text-right">Subtotal Biaya (IDR)</th>
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
                        <span style="font-size: 7.5px; font-weight: bold; text-transform: uppercase; padding: 2px 4px; border-radius: 2px; {{ $isPart ? 'background: #FED7AA; color: #9A3412;' : 'background: #E0F2FE; color: #0369A1;' }}">
                            {{ $isPart ? 'Suku Cadang' : 'Jasa Servis' }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $item->description ?: ($item->sparePart?->name ?? 'Pengerjaan Servis') }}</strong>
                        @if($item->sparePart)
                            <br><span style="font-size: 8px; color: #64748B;">SKU: {{ $item->sparePart->part_number ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ $item->qty }}</td>
                    <td class="text-right font-mono" style="font-weight: bold;">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #64748B;">
                        Detail pekerjaan belum terinput.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary Box --}}
    <table class="summary-container">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <div class="checklist-box">
                    <strong>Pernyataan Serah Terima:</strong><br>
                    Dengan ini pelanggan menyatakan telah menerima kendaraan dalam kondisi baik sesuai dengan lembar inspeksi akhir dan rincian pekerjaan yang tercantum di atas.<br>
                    Garansi servis resmi AUDrive berlaku selama 30 hari kalender.
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Total Jasa Pengerjaan</td>
                        <td class="amount">Rp {{ number_format($workOrder->total_jasa_idr ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Suku Cadang</td>
                        <td class="amount">Rp {{ number_format($workOrder->total_part_idr ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL BIAYA AKHIR</td>
                        <td class="amount">Rp {{ number_format($workOrder->total_idr ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Sign-off --}}
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Pelanggan Penerima Kendaraan,</div>
                <div class="sig-name">{{ $customer?->user?->name ?? 'Pelanggan' }}</div>
                <div class="sig-sub">Tanda Tangan Serah Terima</div>
            </td>
            <td class="sig-space"></td>
            <td class="sig-cell">
                <div class="sig-title">Chief Mechanic / Workshop Head,</div>
                <div class="sig-name">{{ $mechanic?->name ?? 'Workshop Head' }}</div>
                <div class="sig-sub">Quality Check Verified</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Bukti tanda terima ini sah dan dicetak otomatis melalui AUDrive Workshop Hub.
    </div>
</body>
</html>
