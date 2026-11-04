<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk POS {{ $salesOrder->code }} - AUDrive</title>
    <style>
        @page {
            margin: 3mm;
            size: 80mm auto;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5px;
            color: #000000;
            line-height: 1.35;
            width: 74mm;
            margin: 0 auto;
            padding: 4mm 2mm;
            background: #ffffff;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 8px;
            line-height: 1.25;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 6px;
        }
        .meta-table td {
            padding: 1px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .divider-double {
            border-top: 1px double #000;
            margin: 6px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
            font-size: 9px;
        }
        .item-meta {
            font-size: 8px;
            color: #333;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .totals-table td {
            padding: 1.5px 0;
        }
        .totals-table .grand {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 2px;
        }

        .payment-box {
            font-size: 8.5px;
            margin-top: 4px;
        }
        .payment-box table {
            width: 100%;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    @php
        $customer = $customer ?? $salesOrder->customer;
        $items = $items ?? $salesOrder->items;
        $payments = $payments ?? $salesOrder->payments;
        $primaryPayment = $payments->first();
    @endphp

    <div class="header">
        <h1>AUDRIVE MOTORS</h1>
        <p>Luxury Automotive & Workshop</p>
        <p>SCBD Sudirman Kav. 88, Jakarta Selatan</p>
        <p>Telp: +62 21 8088 9900</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="text-left">No. Trx:</td>
            <td class="text-right"><strong>#{{ $salesOrder->code }}</strong></td>
        </tr>
        <tr>
            <td class="text-left">Tanggal:</td>
            <td class="text-right">{{ $salesOrder->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="text-left">Kasir:</td>
            <td class="text-right">{{ $salesOrder->creator?->name ?? 'Kasir POS' }}</td>
        </tr>
        @if($customer)
            <tr>
                <td class="text-left">Customer:</td>
                <td class="text-right"><strong>{{ $customer->user?->name ?? 'Member' }}</strong></td>
            </tr>
        @else
            <tr>
                <td class="text-left">Customer:</td>
                <td class="text-right">Walk-in Customer</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table class="items-table">
        @foreach($items as $item)
            <tr>
                <td colspan="2" class="item-name">
                    {{ $item->description ?: ($item->sparePart?->name ?? 'Item') }}
                </td>
            </tr>
            <tr>
                <td class="item-meta text-left">
                    {{ $item->qty }} x Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}
                </td>
                <td class="text-right" style="font-weight: bold;">
                    Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td class="text-left">Subtotal</td>
            <td class="text-right">Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</td>
        </tr>
        @if($salesOrder->discount_idr > 0)
            <tr>
                <td class="text-left">Diskon Promo</td>
                <td class="text-right">- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if($salesOrder->tax_idr > 0)
            <tr>
                <td class="text-left">PPN (11%)</td>
                <td class="text-right">Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td class="text-left">TOTAL AKHIR</td>
            <td class="text-right">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="payment-box">
        <table>
            <tr>
                <td class="text-left">Metode Bayar:</td>
                <td class="text-right" style="text-transform: uppercase; font-weight: bold;">
                    {{ $primaryPayment?->method ?? ($salesOrder->payments?->first()?->method ?? 'CASH') }}
                </td>
            </tr>
            @if($primaryPayment?->reference)
                <tr>
                    <td class="text-left">Ref No:</td>
                    <td class="text-right" style="font-family: monospace;">{{ $primaryPayment->reference }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-left">Status:</td>
                <td class="text-right" style="font-weight: bold;">LUNAS (SETTLED)</td>
            </tr>
        </table>
    </div>

    <div class="divider-double"></div>

    <div class="footer">
        <p><strong>TERIMA KASIH ATAS KUNJUNGAN ANDA</strong></p>
        <p>Barang yang sudah dibeli tidak dapat ditukar kecuali terdapat perjanjian garansi resmi.</p>
        <p style="margin-top: 4px;">www.audrive.id</p>
    </div>
</body>
</html>
