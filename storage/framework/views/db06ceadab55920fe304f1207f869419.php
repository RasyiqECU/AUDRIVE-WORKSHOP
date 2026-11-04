<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?php echo e($invoice->number ?? $salesOrder->code); ?> - AUDrive</title>
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
            border-bottom: 3px solid #D97706;
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
            color: #D97706;
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
            font-size: 20px;
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
            border: 2px solid #D97706;
            color: #D97706;
            background: #FFFBEB;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 15px;
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
        .info-card strong {
            color: #0B0C0E;
        }

        .vehicle-box {
            width: 100%;
            border: 1px solid #E2E8F0;
            background: #F1F5F9;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .vehicle-box table {
            width: 100%;
            font-size: 9px;
        }
        .vehicle-box td {
            padding: 2px 4px;
        }
        .vehicle-box .v-label {
            color: #64748B;
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
            padding: 8px 10px;
            border: 1px solid #0B0C0E;
        }
        .items-table td {
            padding: 8px 10px;
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
            border-top: 2px solid #D97706;
            padding: 8px 10px;
        }
        .totals-table .grand-total .amount {
            color: #FBBF24;
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

        .payment-proof-box {
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            padding: 10px;
            border-radius: 4px;
            font-size: 8.5px;
        }
        .payment-proof-title {
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            font-size: 9px;
            margin-bottom: 4px;
        }

        .signatures-table {
            width: 100%;
            margin-top: 25px;
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
            margin-bottom: 50px;
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
            margin-top: 20px;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            text-align: center;
            font-size: 7.5px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <?php
        $items = $items ?? $salesOrder->items;
        $customer = $customer ?? $salesOrder->customer;
        $invoiceNumber = $invoice->number ?? ($salesOrder->invoice->number ?? 'INV-' . $salesOrder->code);
        $isPaid = in_array(strtolower($salesOrder->status->value ?? (string)$salesOrder->status), ['paid', 'completed'], true);
        $payment = $salesOrder->payments->first();
        $carItem = $items->firstWhere('car_unit_id');
        $carUnit = $carItem?->carUnit ?? ($salesOrder->carUnit ?? null);
    ?>

    
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: top;">
                <div class="company-name">AU<span>DRIVE</span> MOTORS</div>
                <div class="company-subtitle">Luxury Supercar Dealership & Performance Workshop</div>
                <div class="company-address">
                    Graha AUDrive Kav. 88, SCBD, Jl. Jend. Sudirman, Jakarta Selatan 12190<br>
                    Hotline: +62 21 8088 9900 | WhatsApp Concierge: +62 811 900 8888<br>
                    Email: billing@audrive.id | NPWP: 01.345.678.9-012.000
                </div>
            </td>
            <td class="doc-title-box">
                <div class="doc-title">OFFICIAL INVOICE</div>
                <div class="doc-badge">NO: <?php echo e($invoiceNumber); ?></div>
                <br>
                <?php if($isPaid): ?>
                    <div class="stamp-box stamp-paid">
                        ✓ LUNAS / VERIFIED PAID
                    </div>
                <?php else: ?>
                    <div class="stamp-box stamp-unpaid">
                        MENUNGGU PEMBAYARAN
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    
    <table class="meta-table">
        <tr>
            <td class="info-card">
                <div class="info-card-title">Ditujukan Kepada (Billed To)</div>
                <p><strong><?php echo e($customer?->user?->name ?? 'Walk-in Customer'); ?></strong></p>
                <p>Telepon: <?php echo e($customer?->user?->phone ?? '-'); ?></p>
                <p>Email: <?php echo e($customer?->user?->email ?? '-'); ?></p>
                <p>Alamat: <?php echo e($customer?->address ?? 'Showroom Client'); ?><?php echo e($customer?->city ? ', ' . $customer->city : ''); ?></p>
            </td>
            <td class="info-card">
                <div class="info-card-title">Informasi Tagihan & Otorisasi</div>
                <p>No. Transaksi (Order): <strong>#<?php echo e($salesOrder->code); ?></strong></p>
                <p>Tanggal Diterbitkan: <strong><?php echo e($salesOrder->created_at->format('d F Y, H:i')); ?> WIB</strong></p>
                <p>Jatuh Tempo: <strong><?php echo e($salesOrder->created_at->addDays(7)->format('d F Y')); ?></strong></p>
                <p>Tipe Layanan: <strong style="text-transform: uppercase;"><?php echo e(str_replace('_', ' ', $salesOrder->type->value ?? (string)$salesOrder->type)); ?></strong></p>
                <p>Kasir / Staff Otorisator: <strong><?php echo e($salesOrder->creator?->name ?? 'AUDrive Finance Desk'); ?></strong></p>
            </td>
        </tr>
    </table>

    
    <?php if($carUnit): ?>
        <div class="vehicle-box">
            <table>
                <tr>
                    <td class="v-label">Unit Kendaraan:</td>
                    <td class="v-val"><?php echo e($carUnit->brand); ?> <?php echo e($carUnit->model); ?> (<?php echo e($carUnit->year); ?>)</td>
                    <td class="v-label">No. Polisi / Plat:</td>
                    <td class="v-val"><?php echo e($carUnit->plate_number ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="v-label">Nomor Rangka (VIN):</td>
                    <td class="v-val" style="font-family: monospace;"><?php echo e($carUnit->vin ?? '-'); ?></td>
                    <td class="v-label">Kilometer (Odometer):</td>
                    <td class="v-val"><?php echo e(number_format($carUnit->mileage_km ?? 0, 0, ',', '.')); ?> KM</td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 47%;" class="text-left">Rincian Barang / Jasa Layanan</th>
                <th style="width: 10%;" class="text-center">Kuantitas</th>
                <th style="width: 18%;" class="text-right">Harga Satuan (IDR)</th>
                <th style="width: 20%;" class="text-right">Total Subtotal (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center font-mono"><?php echo e($index + 1); ?></td>
                    <td>
                        <strong><?php echo e($item->description ?: ($item->sparePart?->name ?? 'Item Transaksi')); ?></strong>
                        <?php if($item->sparePart): ?>
                            <br><span style="font-size: 8px; color: #64748B;">SKU: <?php echo e($item->sparePart->part_number ?? '-'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-mono"><?php echo e($item->qty); ?></td>
                    <td class="text-right font-mono">Rp <?php echo e(number_format($item->unit_price_idr, 0, ',', '.')); ?></td>
                    <td class="text-right font-mono" style="font-weight: bold;">Rp <?php echo e(number_format($item->subtotal_idr, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #64748B;">
                        Detail item transaksi tidak tersedia.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <table class="summary-container">
        <tr>
            <td class="payment-notes-card">
                <?php if($isPaid && $payment): ?>
                    <div class="payment-proof-box">
                        <div class="payment-proof-title">✓ BUKTI PEMBAYARAN TERVALIDASI (SETTLED)</div>
                        <p>Metode Pembayaran: <strong style="text-transform: uppercase;"><?php echo e($payment->method); ?></strong></p>
                        <p>Nomor Referensi Transaksi: <strong style="font-family: monospace;"><?php echo e($payment->reference ?? '-'); ?></strong></p>
                        <p>Waktu Pembayaran: <strong><?php echo e(($payment->paid_at ?? $salesOrder->updated_at)->format('d F Y, H:i')); ?> WIB</strong></p>
                        <p>Jumlah Diterima: <strong>Rp <?php echo e(number_format($payment->amount_idr ?? $salesOrder->total_idr, 0, ',', '.')); ?></strong></p>
                        <p>Status: <strong style="color: #059669;">LUNAS RESMI</strong></p>
                    </div>
                <?php else: ?>
                    <div class="payment-proof-box" style="border-left: 3px solid #D97706;">
                        <div style="font-weight: bold; color: #D97706; margin-bottom: 4px;">INFORMASI REKENING RESMI AUDrive:</div>
                        <p><strong>Bank Central Asia (BCA) Prioritas</strong></p>
                        <p style="font-family: monospace;">No. Rek: <strong>588-099-8800</strong> a.n PT AUDRIVE INDONESIA</p>
                        <p><strong>Bank Mandiri Corporate</strong></p>
                        <p style="font-family: monospace;">No. Rek: <strong>137-00-9988-7711</strong> a.n PT AUDRIVE INDONESIA</p>
                        <p style="font-size: 8px; color: #64748B; margin-top: 4px;">Harap sertakan kode referensi: <strong><?php echo e($salesOrder->code); ?></strong> pada berita transfer.</p>
                    </div>
                <?php endif; ?>

                <div class="terbilang-box">
                    <strong>Terbilang:</strong> <?php echo e(\App\Support\Terbilang::make($salesOrder->total_idr)); ?>

                </div>
            </td>
            <td class="totals-card">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal Tagihan</td>
                        <td class="amount">Rp <?php echo e(number_format($salesOrder->subtotal_idr, 0, ',', '.')); ?></td>
                    </tr>
                    <?php if($salesOrder->discount_idr > 0): ?>
                        <tr>
                            <td class="label" style="color: #059669;">Potongan / Diskon Promo</td>
                            <td class="amount" style="color: #059669;">- Rp <?php echo e(number_format($salesOrder->discount_idr, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if($salesOrder->tax_idr > 0): ?>
                        <tr>
                            <td class="label">Pajak Pertambahan Nilai (PPN 11%)</td>
                            <td class="amount">Rp <?php echo e(number_format($salesOrder->tax_idr, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="grand-total">
                        <td>TOTAL AKHIR</td>
                        <td class="amount">Rp <?php echo e(number_format($salesOrder->total_idr, 0, ',', '.')); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    
    <table class="signatures-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Pelanggan / Penerima Layanan,</div>
                <div class="sig-name"><?php echo e($customer?->user?->name ?? 'Pelanggan'); ?></div>
                <div class="sig-sub">Tanda Tangan & Nama Terang</div>
            </td>
            <td class="sig-space"></td>
            <td class="sig-cell">
                <div class="sig-title">Kasir & Bagian Keuangan AUDrive,</div>
                <div class="sig-name"><?php echo e($salesOrder->creator?->name ?? 'Finance Department'); ?></div>
                <div class="sig-sub">Authorized Signature & Digital Seal</div>
            </td>
        </tr>
    </table>

    
    <div class="footer">
        Dokumen ini diterbitkan secara resmi oleh sistem komputerisasi AUDrive Supercar Management System.<br>
        Bukti pembayaran sah dan memiliki kekuatan hukum penuh sebagai faktur transaksi komersial.
    </div>
</body>
</html>
<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/pdf/invoice.blade.php ENDPATH**/ ?>