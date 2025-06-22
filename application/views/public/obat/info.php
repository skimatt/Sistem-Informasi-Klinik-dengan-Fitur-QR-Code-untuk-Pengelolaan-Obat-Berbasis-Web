<?php
// File: application/views/public/obat/info.php

// Variabel yang diharapkan: $obat, $title, $pengaturan_klinik
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? html_escape($title) : 'Informasi Obat'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-top: 20px;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section strong {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .alert-kadaluarsa {
            background-color: #ffe0b2; /* Light orange */
            border-left: 5px solid #ff9800; /* Darker orange */
            padding: 10px;
            margin-bottom: 15px;
            font-weight: bold;
            color: #e65100; /* Even darker orange */
        }
        .footer-info {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 25px;
            font-size: 0.8em;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Informasi Obat</h1>

        <?php if (!empty($obat)): ?>
            <div class="info-section">
                <h2><?php echo html_escape($obat->nama_obat); ?></h2>
                <p><strong>Kategori:</strong> <?php echo html_escape($obat->nama_kategori); ?></p>
                <p><strong>Jenis:</strong> <?php echo html_escape($obat->nama_jenis); ?></p>
            </div>

            <div class="info-section">
                <strong>Tanggal Kedaluwarsa:</strong>
                <p><?php echo html_escape(date('d F Y', strtotime($obat->tanggal_kadaluarsa))); ?></p>
                <?php
                $today = strtotime(date('Y-m-d'));
                $expiry_date = strtotime($obat->tanggal_kadaluarsa);
                if ($expiry_date < $today) {
                    echo '<div class="alert-kadaluarsa">Obat ini sudah KADALUARSA! Mohon jangan digunakan.</div>';
                } elseif ($expiry_date < strtotime('+90 days', $today)) { // Kurang dari 90 hari lagi kadaluarsa
                    echo '<div class="alert-kadaluarsa">Perhatian: Obat ini akan segera KADALUARSA dalam ' . ceil(($expiry_date - $today) / (60 * 60 * 24)) . ' hari.</div>';
                }
                ?>
            </div>

            <?php if (isset($obat->dosis_penggunaan) && !empty($obat->dosis_penggunaan)): ?>
            <div class="info-section">
                <strong>Dosis & Penggunaan:</strong>
                <p><?php echo nl2br(html_escape($obat->dosis_penggunaan)); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($obat->indikasi_umum) && !empty($obat->indikasi_umum)): ?>
            <div class="info-section">
                <strong>Indikasi Umum:</strong>
                <p><?php echo nl2br(html_escape($obat->indikasi_umum)); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($obat->peringatan_khusus) && !empty($obat->peringatan_khusus)): ?>
            <div class="info-section">
                <strong>Peringatan Penggunaan:</strong>
                <p style="color: #d32f2f; font-weight: bold;"><?php echo nl2br(html_escape($obat->peringatan_khusus)); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($obat->cara_penyimpanan) && !empty($obat->cara_penyimpanan)): ?>
            <div class="info-section">
                <strong>Cara Penyimpanan:</strong>
                <p><?php echo nl2br(html_escape($obat->cara_penyimpanan)); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($obat->produsen) && !empty($obat->produsen)): ?>
            <div class="info-section">
                <strong>Produsen:</strong>
                <p><?php echo html_escape($obat->produsen); ?></p>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <p>Informasi obat tidak ditemukan.</p>
        <?php endif; ?>

        <div class="footer-info">
            Informasi ini disediakan oleh <?php echo isset($pengaturan_klinik['nama_klinik']) ? html_escape($pengaturan_klinik['nama_klinik']) : 'Apotek Anda'; ?>.<br>
            Untuk informasi lebih lanjut, hubungi <?php echo isset($pengaturan_klinik['telepon_klinik']) ? html_escape($pengaturan_klinik['telepon_klinik']) : ''; ?>.
        </div>
    </div>
</body>
</html>