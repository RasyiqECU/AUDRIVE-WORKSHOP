<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>POS Receipt - {{ $salesOrder->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 10px; color: #000; line-height: 1.4; width: 80mm; }
        .container { padding: 5mm; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 10px; }
        .logo { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .logo span { text-decoration: underline; }
        .header p { font-size: 9px; margin-top: 2px; }
        .info { margin-bottom: 10px; font-size: 9px; }
        .info p { margin-bottom: 2px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .items { margin-bottom: 10px; }
        .item { margin-bottom: 5px; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; font-size: 9px; margin-left: 10px; }
        .totals { margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 9px; }
        .total-row.grand { font-size: 12px; font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .payment-info { margin-top: 10px; font-size: 9px; }
        .payment-info p { margin-bottom: 2px; }
        .footer { text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #000; font-size: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AU<span>DRIVE</span></div>
            <p>Jl. Sudirman No. 123</p>
            <p>Jakarta Selatan</p>
            <p>Telp: +62 21 1234 5678</p>
        </div>


        <div class="info">
            <p>No: {{ $salesOrder->code }}</p>
            <p>Tanggal: {{ $salesOrder->created_at->format('d/m/Y H:i') }}</p>
            @if($customer)
            <p>Customer: {{ $customer->user->name }}</p>
            @endif
            <p>Kasir: {{ $salesOrder->createdBy->name ?? '-' }}</p>
        </div>


        <div class="divider"></div>


        <div class="items">
            @foreach($items as $item)
            <div class="item">
                <div class="item-name">{{ $item->description }}</div>
                <div class="item-detail">
                    <span>{{ $item->qty }} x Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>


        <div class="divider"></div>


        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</span>
            </div>
            @if($salesOrder->discount_idr > 0)
            <div class="total-row">
                <span>Diskon:</span>
                <span>-Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($salesOrder->tax_idr > 0)
            <div class="total-row">
                <span>Pajak:</span>
                <span>Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row grand">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</span>
            </div>
        </div>


        @if($payments->count() > 0)
        <div class="payment-info">
            <p>Pembayaran:</p>
            @foreach($payments as $payment)
            <p>{{ ucfirst($payment->method) }}: Rp {{ number_format($payment->amount_idr, 0, ',', '.') }}</p>
            @endforeach
        </div>
        @endif


        <div class="footer">
            <p>Terima kasih atas kunjungan Anda</p>
            <p>Barang yang sudah dibeli</p>
            <p>tidak dapat dikembalikan</p>
            <p style="margin-top: 5px;">*** AUDrive ***</p>
        </div>
    </div>
</body>
</html>



