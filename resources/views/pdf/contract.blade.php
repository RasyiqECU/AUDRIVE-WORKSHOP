<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Jual Beli {{ $salesOrder->code }} - AUDrive</title>
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
            line-height: 1.5;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #D97706;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #0B0C0E;
            text-transform: uppercase;
        }
        .company-name span { color: #D97706; }
        .company-address {
            font-size: 8.5px;
            color: #475569;
            margin-top: 3px;
        }
        .doc-title-box {
            text-align: right;
            vertical-align: top;
        }
        .doc-title {
            font-size: 16px;
            font-weight: 800;
            color: #0B0C0E;
            letter-spacing: 0.5px;
        }
        .doc-badge {
            font-size: 9px;
            font-family: monospace;
            font-weight: bold;
            color: #D97706;
            margin-top: 2px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0B0C0E;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 3px;
            margin: 12px 0 6px 0;
        }

        .parties-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 10px;
        }
        .party-box {
            width: 50%;
            border: 1px solid #E2E8F0;
            background: #F8FAFC;
            padding: 8px 10px;
            border-radius: 4px;
            vertical-align: top;
            font-size: 9px;
        }
        .party-box strong { color: #0B0C0E; }
        .party-box p { margin-bottom: 2px; }

        .vehicle-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E2E8F0;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .vehicle-table td {
            padding: 5px 8px;
            border: 1px solid #E2E8F0;
        }
        .vehicle-table .label {
            width: 25%;
            background: #F8FAFC;
            color: #475569;
            font-weight: 600;
        }
        .vehicle-table .val {
            width: 25%;
            font-weight: bold;
            color: #0B0C0E;
        }

        .price-card {
            border: 1px solid #0B0C0E;
            background: #0B0C0E;
            color: #ffffff;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin: 8px 0;
        }
        .price-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; }
        .price-val { font-size: 18px; font-weight: 800; color: #FBBF24; font-family: monospace; margin: 3px 0; }
        .price-terbilang { font-size: 9px; font-style: italic; color: #E2E8F0; }

        .terms-list {
            margin-left: 18px;
            font-size: 8.5px;
            color: #334155;
            line-height: 1.45;
        }
        .terms-list li { margin-bottom: 4px; text-align: justify; }

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
        .sig-space { width: 20%; }
        .sig-title { font-size: 9px; font-weight: 700; color: #1E293B; margin-bottom: 50px; }
        .sig-name { font-size: 10px; font-weight: 700; color: #0B0C0E; border-top: 1px solid #1E293B; padding-top: 4px; display: inline-block; min-width: 140px; }
        .sig-role { font-size: 8px; color: #64748B; }

        .footer {
            margin-top: 15px;
            border-top: 1px solid #E2E8F0;
            padding-top: 6px;
            text-align: center;
            font-size: 7.5px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    @php
        $carUnit = $carUnit ?? ($salesOrder->items->firstWhere('car_unit_id')?->carUnit ?? null);
        $customer = $customer ?? $salesOrder->customer;
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="company-name">AU<span>DRIVE</span> MOTORS</div>
                <div class="company-address">
                    Graha AUDrive Supercar Dealership, Kav. 88, SCBD, Jakarta Selatan 12190<br>
                    Hotline: +62 21 8088 9900 | Email: legal@audrive.id
                </div>
            </td>
            <td class="doc-title-box">
                <div class="doc-title">SURAT PERJANJIAN JUAL BELI</div>
                <div class="doc-badge">NO: SPK/{{ $salesOrder->code }}</div>
                <div style="font-size: 8.5px; color: #64748B; margin-top: 2px;">
                    Tanggal: {{ $salesOrder->created_at->format('d F Y') }}
                </div>
            </td>
        </tr>
    </table>

    <p style="font-size: 9px; margin-bottom: 8px;">
        Pada hari ini, <strong>{{ $salesOrder->created_at->isoFormat('dddd, D MMMM Y') }}</strong>, telah disepakati dan ditandatangani Perjanjian Pengikatan Jual Beli Kendaraan Bermotor (SPK) antara pihak-pihak di bawah ini:
    </p>

    {{-- Parties --}}
    <table class="parties-table">
        <tr>
            <td class="party-box">
                <div style="font-weight: 800; color: #D97706; margin-bottom: 4px;">PIHAK PERTAMA (PENJUAL)</div>
                <p>Nama Perusahaan: <strong>PT AUDRIVE SUPERCAR INDONESIA</strong></p>
                <p>Alamat: Graha AUDrive Kav. 88, SCBD, Jakarta Selatan</p>
                <p>Telepon: +62 21 8088 9900</p>
                <p>Diwakili Oleh: <strong>{{ $salesOrder->creator?->name ?? 'Authorized Sales Executive' }}</strong></p>
            </td>
            <td class="party-box">
                <div style="font-weight: 800; color: #D97706; margin-bottom: 4px;">PIHAK KEDUA (PEMBELI)</div>
                <p>Nama Lengkap: <strong>{{ $customer?->user?->name ?? '-' }}</strong></p>
                <p>Alamat: {{ $customer?->address ?? '-' }}{{ $customer?->city ? ', ' . $customer->city : '' }}</p>
                <p>Telepon / WA: {{ $customer?->user?->phone ?? '-' }}</p>
                <p>Email: {{ $customer?->user?->email ?? '-' }}</p>
            </td>
        </tr>
    </table>

    {{-- Object --}}
    <div class="section-title">PASAL 1: OBJEK JUAL BELI KENDARAAN</div>
    <table class="vehicle-table">
        <tr>
            <td class="label">Merek & Model</td>
            <td class="val">{{ $carUnit->brand ?? 'Supercar' }} {{ $carUnit->model ?? '-' }}</td>
            <td class="label">Tahun Pembuatan</td>
            <td class="val">{{ $carUnit->year ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Warna Eksterior</td>
            <td class="val">{{ $carUnit->color ?? '-' }}</td>
            <td class="label">Nomor Polisi (Plat)</td>
            <td class="val">{{ $carUnit->plate_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Rangka (VIN)</td>
            <td class="val" style="font-family: monospace;">{{ $carUnit->vin ?? '-' }}</td>
            <td class="label">Jarak Tempuh (KM)</td>
            <td class="val">{{ number_format($carUnit->mileage_km ?? 0, 0, ',', '.') }} KM</td>
        </tr>
        <tr>
            <td class="label">Transmisi</td>
            <td class="val">{{ ucfirst($carUnit->transmission ?? 'Dual Clutch') }}</td>
            <td class="label">Status Dokumen</td>
            <td class="val">Lengkap (BPKB, STNK & Faktur)</td>
        </tr>
    </table>

    {{-- Price & Payment --}}
    <div class="section-title">PASAL 2: HARGA DAN KETENTUAN PEMBAYARAN</div>
    <div class="price-card">
        <div class="price-label">NILAI KONTRAK TRANSAKSI JUAL BELI (NETT)</div>
        <div class="price-val">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</div>
        <div class="price-terbilang">"{{ \App\Support\Terbilang::make($salesOrder->total_idr) }}"</div>
    </div>

    {{-- Terms --}}
    <div class="section-title">PASAL 3: KETENTUAN SERAH TERIMA & GARANSI</div>
    <ol class="terms-list">
        <li><strong>Keaslian Dokumen:</strong> PIHAK PERTAMA menjamin unit kendaraan bebas sengketa, tidak terikat fidusia, dan memiliki legalitas resmi.</li>
        <li><strong>Pelunasan & Serah Terima:</strong> Penyerahan unit kendaraan beserta BPKB, STNK asli, kunci cadangan, dan buku servis dilakukan setelah pembayaran dinyatakan Lunas / Efektif di rekening resmi AUDrive.</li>
        <li><strong>Garansi Mesin & Transmisi:</strong> AUDrive memberikan garansi perlindungan mesin dan transmisi selama 6 (enam) bulan atau 5.000 KM sejak tanggal serah terima.</li>
        <li><strong>Penyelesaian Perselisihan:</strong> Segala perselisihan yang timbul akan diselesaikan secara musyawarah dan mufakat melalui Pengadilan Negeri Jakarta Selatan.</li>
    </ol>

    {{-- Signatures --}}
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">PIHAK PERTAMA (PENJUAL)<br>PT AUDRIVE SUPERCAR INDONESIA</div>
                <div class="sig-name">{{ $salesOrder->creator?->name ?? 'Sales & Showroom Executive' }}</div>
                <div class="sig-role">Authorized Dealership Signature</div>
            </td>
            <td class="sig-space"></td>
            <td class="sig-cell">
                <div class="sig-title">PIHAK KEDUA (PEMBELI)<br>KONSUMEN SUPERCAR</div>
                <div class="sig-name">{{ $customer?->user?->name ?? 'Pembeli' }}</div>
                <div class="sig-role">Tanda Tangan & Nama Terang</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen perjanjian ini dibuat dalam 2 (dua) rangkap dan mengikat secara hukum bagi kedua belah pihak.
    </div>
</body>
</html>
