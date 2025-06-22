<?php
// File: application/views/kasir/laporan_penjualan/report_pdf.php

// Pastikan variabel $penjualan, $start_date, $end_date, $title sudah dilewatkan dari controller Kasir::export_laporan_penjualan_kasir_pdf()
// $penjualan di sini adalah daftar transaksi, bukan transaksi tunggal
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($title) ? html_escape($title) : 'Laporan Penjualan Pribadi'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20mm;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Times New Roman', Times, serif;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>Laporan Penjualan Pribadi Apotek</h2>
        <?php
        $periode_text = '';
        if (!empty($start_date) && !empty($end_date)) {
            $periode_text .= 'Periode: ' . date('d F Y', strtotime($start_date)) . ' s/d ' . date('d F Y', strtotime($end_date));
        } else {
            $periode_text .= 'Seluruh Periode';
        }
        echo '<p>' . $periode_text . '</p>';
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>UUID Penjualan</th>
                <th>Tanggal Penjualan</th>
                <th>Total Harga</th>
                <th>Metode Bayar</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            <?php $grand_total = 0; if (!empty($penjualan)): ?>
                <?php $no = 1; foreach ($penjualan as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape($row->uuid_penjualan); ?></td>
                        <td><?php echo html_escape(date('d F Y H:i', strtotime($row->tgl_penjualan))); ?></td>
                        <td class="text-right">Rp. <?php echo html_escape(number_format($row->total_harga, 2, ',', '.')); ?></td>
                        <td><?php echo html_escape(ucwords($row->metode_bayar)); ?></td>
                        <td><?php echo html_escape($row->nama_kasir); ?></td>
                    </tr>
                    <?php $grand_total += $row->total_harga; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan pribadi dalam periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Keseluruhan:</th>
                <th class="text-right">Rp. <?php echo html_escape(number_format($grand_total, 2, ',', '.')); ?></th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: <?php echo date('d F Y H:i:s'); ?> (Banda Aceh, Aceh, Indonesia)
    </div>
</body>
</html>