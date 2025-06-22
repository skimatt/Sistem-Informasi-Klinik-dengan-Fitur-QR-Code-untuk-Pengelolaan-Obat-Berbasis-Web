<?php
// File: application/views/apoteker/laporan_obat_masuk/report_pdf.php

// Pastikan variabel $stok_masuk, $start_date, $end_date, $nama_obat_filter, $title sudah dilewatkan dari controller Apoteker::export_laporan_obat_masuk_pdf()
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($title) ? html_escape($title) : 'Laporan Obat Masuk'; ?></title>
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
        <h2>Laporan Obat Masuk Apotek</h2>
        <?php
        $periode_text = '';
        if (!empty($start_date) && !empty($end_date)) {
            $periode_text .= 'Periode: ' . date('d F Y', strtotime($start_date)) . ' s/d ' . date('d F Y', strtotime($end_date));
        } else {
            $periode_text .= 'Seluruh Periode';
        }

        if (!empty($nama_obat_filter)) {
            $periode_text .= '<br>Filter Obat: "' . html_escape($nama_obat_filter) . '"';
        }
        echo '<p>' . $periode_text . '</p>';
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Masuk</th>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Suplier</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($stok_masuk)): ?>
                <?php $no = 1; foreach ($stok_masuk as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo html_escape(date('d F Y', strtotime($row->tanggal_masuk))); ?></td>
                        <td><?php echo html_escape($row->nama_obat); ?></td>
                        <td><?php echo html_escape($row->jumlah); ?></td>
                        <td><?php echo html_escape($row->nama_suplier); ?></td>
                        <td><?php echo html_escape($row->keterangan); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data stok masuk dalam periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?php echo date('d F Y H:i:s'); ?> (Banda Aceh, Aceh, Indonesia)
    </div>
</body>
</html>