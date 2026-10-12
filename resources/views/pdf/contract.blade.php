<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontrak Jual Beli {{ $salesOrder->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.6; }
        .container { padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #FFD100; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; letter-spacing: 2px; margin-bottom: 10px; }
        .logo span { color: #FFD100; }
        .header h1 { font-size: 20px; color: #FFD100; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .section { margin-bottom: 25px; }
        .section h2 { font-size: 14px; color: #FFD100; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .section p { margin-bottom: 8px; text-align: justify; }
        .party-info { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 3px solid #FFD100; }
        .party-info strong { color: #101114; }
        .vehicle-info { background: #f9f9f9; padding: 15px; margin: 10px 0; }
        .vehicle-info table { width: 100%; }
        .vehicle-info td { padding: 5px 0; }
        .vehicle-info td:first-child { font-weight: bold; width: 30%; }
        .price-box { background: #101114; color: #FFD100; padding: 15px; text-align: center; margin: 20px 0; font-size: 16px; font-weight: bold; }
        .terms { margin: 20px 0; }
        .terms ol { margin-left: 20px; }
        .terms li { margin-bottom: 8px; text-align: justify; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .signature-box { width: 45%; text-align: center; }
        .signature-box p { margin-bottom: 5px; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AU<span>DRIVE</span></div>
            <h1>KONTRAK JUAL BELI</h1>
            <p>No: {{ $salesOrder->code }}</p>
            <p>Tanggal: {{ $salesOrder->created_at->format('d F Y') }}</p>
        </div>


        <div class="section">
            <h2>PIHAK-PIHAK</h2>
            <p>Pada hari ini, {{ $salesOrder->created_at->isoFormat('dddd, D MMMM Y') }}, telah dibuat kontrak jual beli antara:</p>


            <div class="party-info">
                <p><strong>PIHAK PERTAMA (PENJUAL):</strong></p>
                <p>Nama: AUDrive</p>
                <p>Alamat: Jl. Sudirman No. 123, Jakarta Selatan 12190</p>
                <p>Telp: +62 21 1234 5678</p>
            </div>


            <div class="party-info">
                <p><strong>PIHAK KEDUA (PEMBELI):</strong></p>
                <p>Nama: {{ $customer->user->name }}</p>
                <p>Alamat: {{ $customer->address ?? '-' }}, {{ $customer->city ?? '-' }}</p>
                <p>Telp: {{ $customer->user->phone ?? '-' }}</p>
                <p>Email: {{ $customer->user->email }}</p>
            </div>
        </div>


        <div class="section">
            <h2>OBJEK JUAL BELI</h2>
            <p>PIHAK PERTAMA setuju untuk menjual dan PIHAK KEDUA setuju untuk membeli kendaraan dengan spesifikasi sebagai berikut:</p>


            <div class="vehicle-info">
                <table>
                    <tr><td>Merek</td><td>: {{ $carUnit->brand ?? '-' }}</td></tr>
                    <tr><td>Model</td><td>: {{ $carUnit->model ?? '-' }}</td></tr>
                    <tr><td>Tahun</td><td>: {{ $carUnit->year ?? '-' }}</td></tr>
                    <tr><td>Warna</td><td>: {{ $carUnit->color ?? '-' }}</td></tr>
                    <tr><td>No. Polisi</td><td>: {{ $carUnit->plate_number ?? '-' }}</td></tr>
                    <tr><td>No. Rangka</td><td>: {{ $carUnit->vin ?? '-' }}</td></tr>
                    <tr><td>Kilometer</td><td>: {{ number_format($carUnit->mileage_km ?? 0, 0, ',', '.') }} km</td></tr>
                    <tr><td>Transmisi</td><td>: {{ ucfirst($carUnit->transmission ?? '-') }}</td></tr>
                </table>
            </div>
        </div>


        <div class="section">
            <h2>HARGA DAN PEMBAYARAN</h2>
            <p>Harga jual beli kendaraan tersebut adalah:</p>
            <div class="price-box">
                Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}
            </div>
            <p>Pembayaran dilakukan dengan ketentuan:</p>
            <p>1. DP (Down Payment) sebesar yang disepakati kedua belah pihak.</p>
            <p>2. Pelunasan dilakukan sebelum serah terima kendaraan.</p>
            <p>3. Pembayaran ditransfer ke rekening resmi AUDrive.</p>
        </div>


        <div class="section terms">
            <h2>KETENTUAN</h2>
            <ol>
                <li>Kendaraan yang dijual adalah dalam kondisi apa adanya (as is) sesuai dengan hasil inspeksi yang telah dilakukan.</li>
                <li>PIHAK PERTAMA menjamin bahwa kendaraan bebas dari sengketa hukum dan memiliki dokumen yang lengkap.</li>
                <li>Serah terima kendaraan akan dilakukan setelah pembayaran lunas.</li>
                <li>Biaya balik nama menjadi tanggung jawab PIHAK KEDUA.</li>
                <li>Garansi mesin diberikan selama 6 bulan sejak tanggal serah terima, tidak termasuk kerusakan akibat kelalaian pengguna.</li>
                <li>Kontrak ini dibuat dalam rangkap 2 (dua) dengan kekuatan hukum yang sama.</li>
            </ol>
        </div>


        <div class="signatures">
            <div class="signature-box">
                <p><strong>PIHAK PERTAMA</strong></p>
                <p>(Penjual)</p>
                <div class="signature-line">
                    <p>AUDrive</p>
                </div>
            </div>
            <div class="signature-box">
                <p><strong>PIHAK KEDUA</strong></p>
                <p>(Pembeli)</p>
                <div class="signature-line">
                    <p>{{ $customer->user->name }}</p>
                </div>
            </div>
        </div>


        <div class="footer">
            <p>Dokumen ini dibuat secara elektronik dan sah tanpa tanda tangan basah.</p>
            <p>&copy; {{ date('Y') }} AUDrive. All rights reserved.</p>
        </div>
    </div>
</body>
</html>



