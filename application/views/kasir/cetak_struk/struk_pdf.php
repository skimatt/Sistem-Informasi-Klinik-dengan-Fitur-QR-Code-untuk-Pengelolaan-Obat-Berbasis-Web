<?php
// File: application/views/kasir/cetak_struk/struk_pdf.php

// Pastikan variabel $penjualan, $details, $pengaturan_klinik sudah dilewatkan dari controller Kasir::cetak_struk()
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Penjualan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font monospace untuk kesan struk */
            font-size: 8pt;
            margin: 0; /* Margin nol agar pas di kertas kecil */
            padding: 5mm; /* Padding untuk konten */
            width: 58mm; /* Lebar umum printer thermal 58mm */
            box-sizing: border-box; /* Agar padding tidak menambah lebar */
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 5px;
        }
        .header h3, .header p {
            margin: 0;
            padding: 0;
        }
        .info-transaksi {
            margin-bottom: 5px;
            border-bottom: 1px dashed black;
            padding-bottom: 5px;
        }
        .info-transaksi p {
            margin: 0;
            padding: 0;
        }
        .produk-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .produk-list th, .produk-list td {
            padding: 2px 0;
            vertical-align: top;
            text-align: left;
        }
        .produk-list .qty, .produk-list .harga {
            text-align: right;
        }
        .produk-list .nama {
            width: 50%;
        }
        .produk-list .total {
            font-weight: bold;
            border-top: 1px dashed black;
            padding-top: 5px;
            text-align: right;
        }
        .total-section {
            border-top: 1px dashed black;
            padding-top: 5px;
            margin-top: 5px;
            text-align: right;
        }
        .total-section p {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .dashed-line {
            border-top: 1px dashed black;
            margin: 5px 0;
        }
        .thank-you {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3><?php echo isset($pengaturan_klinik['nama_klinik']) ? html_escape($pengaturan_klinik['nama_klinik']) : 'APOTEK'; ?></h3>
        <p><?php echo isset($pengaturan_klinik['alamat_klinik']) ? html_escape($pengaturan_klinik['alamat_klinik']) : ''; ?></p>
        <p>Telp: <?php echo isset($pengaturan_klinik['telepon_klinik']) ? html_escape($pengaturan_klinik['telepon_klinik']) : ''; ?></p>
    </div>

    <div class="dashed-line"></div>

    <div class="info-transaksi">
        <p>No. Transaksi: <?php echo html_escape($penjualan->uuid_penjualan); ?></p>
        <p>Tanggal: <?php echo html_escape(date('d/m/Y H:i', strtotime($penjualan->tgl_penjualan))); ?></p>
        <p>Kasir: <?php echo html_escape($penjualan->nama_kasir); ?></p>
        <p>Metode: <?php echo html_escape(ucwords($penjualan->metode_bayar)); ?></p>
    </div>

    <table class="produk-list">
        <thead>
            <tr>
                <th class="nama">Produk</th>
                <th class="qty">Qty</th>
                <th class="harga">Harga</th>
                <th class="qty">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $total_item_harga = 0; ?>
            <?php foreach ($details as $item): ?>
                <tr>
                    <td><?php echo html_escape($item->nama_obat); ?></td>
                    <td class="qty"><?php echo html_escape($item->jumlah); ?></td>
                    <td class="harga"><?php echo html_escape(number_format($item->harga_satuan, 0, ',', '.')); ?></td>
                    <td class="qty"><?php echo html_escape(number_format($item->subtotal, 0, ',', '.')); ?></td>
                </tr>
                <?php $total_item_harga += $item->subtotal; ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3">Total Item:</td>
                <td class="qty">Rp. <?php echo html_escape(number_format($total_item_harga, 0, ',', '.')); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="total-section">
        <p>Total Bayar: Rp. <?php echo html_escape(number_format($penjualan->total_harga, 0, ',', '.')); ?></p>
        </div>

    <div class="dashed-line"></div>

    <div class="thank-you">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
        <p>Semoga Lekas Sembuh</p>
    </div>

    <div style="font-size: 6pt; text-align: center; margin-top: 10mm;">
        <p>Powered by Apotek App v1.0</p>
    </div>
</body>
</html>