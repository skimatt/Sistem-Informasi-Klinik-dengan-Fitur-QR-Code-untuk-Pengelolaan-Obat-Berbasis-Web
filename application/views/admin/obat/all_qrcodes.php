<?php
// File: application/views/admin/obat/all_qrcodes.php

// Pastikan variabel $title, $user_nama, $user_role, dan $obat sudah dilewatkan dari controller Admin::all_qrcodes()
// $obat diharapkan berisi daftar objek obat, dengan properti uuid_obat
?>

<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="<?php echo base_url('assets/'); ?>"
    data-template="vertical-menu-template"
    data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo isset($title) ? html_escape($title) : 'Daftar Semua QR Code Obat'; ?> Apotek</title>
    <meta name="description" content="Melihat daftar semua QR Code untuk obat-obatan." />

    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/img/favicon/favicon.ico'); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/fonts/tabler-icons.css'); ?>" />

    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/css/rtl/core.css'); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/css/rtl/theme-default.css'); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/demo.css'); ?>" />

    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/libs/node-waves/node-waves.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css'); ?>" />
    <script src="<?php echo base_url('assets/vendor/js/helpers.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/js/template-customizer.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/config.js'); ?>"></script>

    <?php
    if (isset($css_files) && is_array($css_files)) {
        foreach ($css_files as $css_file) {
            echo '<link rel="stylesheet" href="' . base_url('assets/css/' . $css_file) . '" />' . "\n";
        }
    }
    ?>
    <style>
        /* Styles untuk tampilan QR Code di halaman */
        .qrcode-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .qrcode-card img {
            max-width: 150px; /* Ukuran gambar QR */
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 10px;
        }
        .qrcode-card p {
            margin-bottom: 5px;
        }
        .qrcode-card h6 {
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .print-btn {
            margin-top: 10px;
        }

        /* Styles untuk pencetakan */
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10mm; /* Padding untuk seluruh area cetak */
                box-sizing: border-box;
            }
            .qrcode-card {
                /* Untuk memastikan kartu tidak terpotong saat cetak */
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none; /* Hapus bayangan saat cetak */
                border: 1px solid #ccc; /* Border solid saat cetak */
                margin-bottom: 15px;
            }
            .col-lg-3, .col-md-4, .col-sm-6, .col-12 {
                /* Sesuaikan lebar kolom untuk cetak agar lebih efisien */
                width: 25% !important; /* Contoh: 4 kolom per baris */
                float: left;
                padding: 5px; /* Padding antar kartu saat cetak */
                box-sizing: border-box;
            }
            .no-print {
                display: none !important; /* Sembunyikan elemen ini saat dicetak */
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php $this->load->view('templates/admin/sidebar'); ?>
            <div class="layout-page">
                <?php $this->load->view('templates/admin/navbar'); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4 no-print"><?php echo html_escape($title); ?></h4>

                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card no-print">
                            <h5 class="card-header">Opsi Cetak QR Code Massal</h5>
                            <div class="card-body">
                                <p class="mb-3">Klik "Cetak Semua QR Code" untuk mencetak semua QR Code obat yang terdaftar.</p>
                                <button type="button" class="btn btn-primary mb-3" onclick="printQRCodes();"><i class="ti ti-printer me-1"></i> Cetak Semua QR Code</button>
                                <a href="<?php echo site_url('obat'); ?>" class="btn btn-outline-secondary mb-3 ms-2"><i class="ti ti-list me-1"></i> Kembali ke Data Obat</a>
                            </div>
                        </div>

                        <div class="row mt-4 printable-area"> 
                            <?php if (!empty($obat)): ?>
                                <?php foreach ($obat as $row): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                        <div class="qrcode-card">
                                            <p class="mb-1 text-muted" style="font-size: 0.8em;">ID: <?php echo html_escape($row->id_obat); ?></p>
                                            <h6><?php echo html_escape($row->nama_obat); ?></h6>
                                            <?php
                                            // Path untuk gambar QR Code yang sudah di-generate sebelumnya
                                            // Pastikan QR code sudah di-generate via Admin::obat_qrcode() atau method lain yang menyimpannya
                                            $qr_image_filename = 'obat-' . $row->id_obat . '.png';
                                            $qr_image_full_path = FCPATH . 'assets/qr_codes/' . $qr_image_filename;
                                            $qr_image_url = base_url('assets/qr_codes/' . $qr_image_filename);

                                            if (file_exists($qr_image_full_path)) {
                                                echo '<img src="' . html_escape($qr_image_url) . '" alt="QR Code ' . html_escape($row->nama_obat) . '">';
                                            } else {
                                                // Jika QR belum ada, berikan pesan dan opsi untuk membuatnya (Admin only)
                                                echo '<p class="text-danger">QR Belum Dibuat</p>';
                                                if (is_admin()) { // Hanya Admin yang bisa generate
                                                    // Link ini akan mengarah ke halaman QR tunggal untuk generate
                                                    echo '<a href="' . site_url('obat/qrcode/' . $row->id_obat) . '" target="_blank" class="btn btn-sm btn-info print-btn no-print">Buat QR</a>';
                                                }
                                            }
                                            ?>
                                            <p class="mb-0 text-muted" style="font-size: 0.7em;">Scan untuk info & detail</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-center">Tidak ada data obat untuk menampilkan QR Code.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        </div>
                    <footer class="content-footer footer bg-footer-theme no-print">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    , made with ❤️ by <a href="https://pixinvent.com" target="_blank" class="footer-link">Pixinvent</a>
                                </div>
                                <div class="d-none d-lg-inline-block">
                                    <a
                                        href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/"
                                        target="_blank"
                                        class="footer-link me-4"
                                        >Documentation</a
                                    >
                                </div>
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
                </div>
            </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <script src="<?php echo base_url('assets/vendor/libs/jquery/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/libs/popper/popper.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/js/bootstrap.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/libs/node-waves/node-waves.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/libs/hammer/hammer.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/js/menu.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

    <?php
    if (isset($js_files) && is_array($js_files)) {
        foreach ($js_files as $js_file) {
            echo '<script src="' . base_url('assets/js/' . $js_file) . '"></script>' . "\n";
        }
    }
    ?>

    <script>
        // Fungsi untuk mencetak area QR Codes
        function printQRCodes() {
            window.print();
        }
    </script>
</body>
</html>