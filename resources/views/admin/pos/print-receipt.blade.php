<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $salesOrder->code }} - AUDrive</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 8px 6px;
            color: #000;
            font-size: 11px;
            line-height: 1.35;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-double { border-top: 1px double #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 1.5px 0; }

        @media print {
            body { width: 100%; margin: 0; padding: 4px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h2 style="font-size: 15px; font-weight: bold; letter-spacing: 1px;">AUDRIVE MOTORS</h2>
        <p style="font-size: 9px; margin-top: 2px;">Luxury Automotive & Performance Workshop</p>
        <p style="font-size: 9px;">Graha AUDrive Kav. 88, SCBD, Jakarta Selatan</p>
        <p style="font-size: 9px;">Telp: +62 21 8088 9900</p>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left" style="font-size: 10px;">No. Trx:</td>
            <td class="text-right" style="font-size: 10px; font-weight: bold;">#{{ $salesOrder->code }}</td>
        </tr>
        <tr>
            <td class="text-left" style="font-size: 10px;">Waktu:</td>
            <td class="text-right" style="font-size: 10px;">{{ $salesOrder->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="text-left" style="font-size: 10px;">Kasir:</td>
            <td class="text-right" style="font-size: 10px;">{{ $salesOrder->creator?->name ?? 'Kasir POS' }}</td>
        </tr>
        @if($salesOrder->customer)
            <tr>
                <td class="text-left" style="font-size: 10px;">Customer:</td>
                <td class="text-right" style="font-size: 10px; font-weight: bold;">{{ $salesOrder->customer->user?->name }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table>
        @foreach($salesOrder->items as $item)
            <tr>
                <td colspan="2" style="font-weight: bold; font-size: 10px;">
                    {{ $item->description ?: ($item->sparePart?->name ?? 'Item Transaksi') }}
                </td>
            </tr>
            <tr>
                <td class="text-left" style="font-size: 9.5px; color: #444;">
                    {{ $item->qty }} x Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}
                </td>
                <td class="text-right" style="font-size: 10px; font-weight: bold;">
                    Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left">Subtotal:</td>
            <td class="text-right">Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</td>
        </tr>
        @if($salesOrder->discount_idr > 0)
            <tr>
                <td class="text-left">Diskon Promo:</td>
                <td class="text-right">- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if($salesOrder->tax_idr > 0)
            <tr>
                <td class="text-left">PPN (11%):</td>
                <td class="text-right">Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr style="font-weight: bold; font-size: 12px;">
            <td class="text-left" style="padding-top: 4px; border-top: 1px dashed #000;">TOTAL:</td>
            <td class="text-right" style="padding-top: 4px; border-top: 1px dashed #000;">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left">Metode Bayar:</td>
            <td class="text-right" style="text-transform: uppercase; font-weight: bold;">
                {{ $salesOrder->payments?->first()?->method ?? 'CASH' }}
            </td>
        </tr>
        @if($salesOrder->payments?->first()?->reference)
            <tr>
                <td class="text-left">Ref:</td>
                <td class="text-right">{{ $salesOrder->payments->first()->reference }}</td>
            </tr>
        @endif
        <tr>
            <td class="text-left">Status:</td>
            <td class="text-right" style="font-weight: bold;">LUNAS</td>
        </tr>
    </table>

    <div class="divider-double"></div>

    <div class="text-center" style="font-size: 9px; margin-top: 8px;">
        <p><strong>TERIMA KASIH ATAS KEPERCAYAAN ANDA</strong></p>
        <p style="margin-top: 3px;">Barang yang sudah dibeli tidak dapat ditukar kecuali terdapat perjanjian tertulis.</p>
        <p style="margin-top: 4px; font-size: 8px;">www.audrive.id</p>
    </div>
</body>
</html>
