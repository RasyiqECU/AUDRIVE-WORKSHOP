<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima Servis - {{ $workOrder->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.6; }
        .container { padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #FF6A00; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; letter-spacing: 2px; margin-bottom: 10px; }
        .logo span { color: #FF6A00; }
        .header h1 { font-size: 20px; color: #FF6A00; margin-bottom: 5px; }
        .header p { font-size: 10px; color: #666; }
        .section { margin-bottom: 25px; }
        .section h2 { font-size: 14px; color: #FF6A00; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-box { background: #f9f9f9; padding: 15px; border-left: 3px solid #FF6A00; }
        .info-box p { margin-bottom: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #101114; color: #FF6A00; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .totals { float: right; width: 300px; margin-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .totals-row.total { font-size: 14px; font-weight: bold; color: #FF6A00; border-top: 2px solid #FF6A00; border-bottom: none; margin-top: 10px; padding-top: 15px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AU<span>DRIVE</span></div>
            <h1>TANDA TERIMA SERVIS</h1>
            <p>No: {{ $workOrder->code }}</p>
            <p>Tanggal: {{ $workOrder->created_at->format('d F Y') }}</p>
        </div>


        <div class="section">
            <h2>Informasi Pelanggan & Kendaraan</h2>
            <div class="info-grid">
                <div class="info-box">
                    <p><strong>Pelanggan:</strong></p>
                    <p>{{ $customer->user->name }}</p>
                    <p>{{ $customer->address ?? '-' }}</p>
                    <p>{{ $customer->city ?? '-' }}, {{ $customer->province ?? '-' }}</p>
                    <p>Telp: {{ $customer->user->phone ?? '-' }}</p>
                </div>
                <div class="info-box">
                    <p><strong>Kendaraan:</strong></p>
                    <p>{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
                    <p>No. Polisi: {{ $vehicle->plate_number ?? '-' }}</p>
                    <p>Warna: {{ $vehicle->color ?? '-' }}</p>
                    <p>KM: {{ number_format($vehicle->mileage ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>


        <div class="section">
            <h2>Detail Pekerjaan</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="50%">Deskripsi</th>
                        <th width="10%" class="text-center">Qty</th>
                        <th width="15%" class="text-right">Harga</th>
                        <th width="20%" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $item->description }}
                            @if($item->type === 'part' && $item->sparePart)
                                <br><small style="color: #999;">({{ $item->sparePart->name }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="totals">
            <div class="totals-row">
                <span>Total Jasa:</span>
                <span>Rp {{ number_format($workOrder->total_jasa_idr, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>Total Spare Part:</span>
                <span>Rp {{ number_format($workOrder->total_part_idr, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($workOrder->total_idr, 0, ',', '.') }}</span>
            </div>
        </div>


        <div class="clear"></div>


        @if($workOrder->notes)
        <div class="section">
            <h2>Catatan</h2>
            <p>{{ $workOrder->notes }}</p>
        </div>
        @endif


        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Mekanik</strong></p>
                <div class="signature-line">
                    <p>{{ $mechanic->name ?? '-' }}</p>
                </div>
            </div>
            <div class="signature-box">
                <p><strong>Pelanggan</strong></p>
                <div class="signature-line">
                    <p>{{ $customer->user->name }}</p>
                </div>
            </div>
        </div>


        <div class="footer">
            <p>Terima kasih telah menggunakan layanan servis AUDrive.</p>
            <p>Dokumen ini dibuat secara elektronik dan sah tanpa tanda tangan basah.</p>
            <p>&copy; {{ date('Y') }} AUDrive. All rights reserved.</p>
        </div>
    </div>
</body>
</html>



