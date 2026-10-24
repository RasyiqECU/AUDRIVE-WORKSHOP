<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $salesOrder->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #FFD100; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; letter-spacing: 2px; }
        .logo span { color: #FFD100; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 24px; color: #FFD100; margin-bottom: 5px; }
        .invoice-title p { font-size: 10px; color: #666; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h3 { font-size: 12px; color: #FFD100; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .info-box p { margin-bottom: 3px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #101114; color: #FFD100; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .totals { float: right; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .totals-row.total { font-size: 14px; font-weight: bold; color: #FFD100; border-top: 2px solid #FFD100; border-bottom: none; margin-top: 10px; padding-top: 15px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999; }
        .payment-info { background: #f9f9f9; padding: 15px; margin-top: 20px; border-left: 3px solid #FFD100; }
        .payment-info h4 { color: #FFD100; margin-bottom: 10px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AU<span>DRIVE</span></div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p>No: {{ $salesOrder->code }}</p>
                <p>Tanggal: {{ $salesOrder->created_at->format('d F Y') }}</p>
            </div>
        </div>


        <div class="info-section">
            <div class="info-box">
                <h3>Dari:</h3>
                <p><strong>AUDrive</strong></p>
                <p>Jl. Sudirman No. 123</p>
                <p>Jakarta Selatan 12190</p>
                <p>Telp: +62 21 1234 5678</p>
                <p>Email: info@audrive.local</p>
            </div>
            <div class="info-box">
                <h3>Kepada:</h3>
                <p><strong>{{ $customer->user->name }}</strong></p>
                <p>{{ $customer->address ?? '-' }}</p>
                <p>{{ $customer->city ?? '-' }}, {{ $customer->province ?? '-' }}</p>
                <p>Telp: {{ $customer->user->phone ?? '-' }}</p>
                <p>Email: {{ $customer->user->email }}</p>
            </div>
        </div>


        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Harga Satuan</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>


        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</span>
            </div>
            @if($salesOrder->discount_idr > 0)
            <div class="totals-row">
                <span>Diskon:</span>
                <span>- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($salesOrder->tax_idr > 0)
            <div class="totals-row">
                <span>Pajak:</span>
                <span>Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="totals-row total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</span>
            </div>
        </div>


        <div class="clear"></div>


        <div class="payment-info">
            <h4>Informasi Pembayaran</h4>
            <p>Status: <strong>{{ ucfirst($salesOrder->payment_status) }}</strong></p>
            <p>Terbayar: Rp {{ number_format($salesOrder->paid_idr, 0, ',', '.') }}</p>
            <p>Sisa: Rp {{ number_format($salesOrder->total_idr - $salesOrder->paid_idr, 0, ',', '.') }}</p>
        </div>


        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda. Invoice ini dibuat secara elektronik dan sah tanpa tanda tangan.</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} AUDrive. All rights reserved.</p>
        </div>
    </div>
</body>
</html>



