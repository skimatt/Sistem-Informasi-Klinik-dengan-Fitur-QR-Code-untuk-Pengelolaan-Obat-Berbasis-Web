<?php
// File: application/views/admin/obat/qrcode.php

// Pastikan variabel $title, $user_nama, $user_role, $obat, dan $qr_image_url sudah dilewatkan dari controller
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
    <title><?php echo isset($title) ? html_escape($title) : 'QR Code Obat'; ?> Apotek</title>
    <meta name="description" content="Tampilan QR Code untuk Obat." />

    <link rel="icon" type="image/x-xicon" href="<?php echo base_url('assets/img/favicon/favicon.ico'); ?>" />

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
        @media print {
            body * {
                visibility: hidden;
            }
            .qrcode-print-area, .qrcode-print-area * {
                visibility: visible;
            }
            .qrcode-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            .no-print {
                display: none;
            }
        }

        .qrcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            background-color: #fff;
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }
        .qrcode-container img {
            max-width: 250px;
            height: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar no-print"> <div class="layout-container">
            <?php $this->load->view('templates/admin/sidebar'); ?>
            <div class="layout-page">
                <?php $this->load->view('templates/admin/navbar'); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4"><?php echo html_escape($title); ?></h4>

                        <div class="card">
                            <h5 class="card-header">Detail QR Code Obat</h5>
                            <div class="card-body">
                                <div class="qrcode-container qrcode-print-area" id="qrcodePrintArea">
                                    <p class="mb-3">Scan QR Code ini untuk melihat detail atau mengelola obat:</p>
                                    <?php if (isset($qr_image_url) && file_exists(FCPATH . str_replace(base_url(), '', $qr_image_url))): ?>
                                        <img src="<?php echo html_escape($qr_image_url); ?>" alt="QR Code <?php echo html_escape($obat->nama_obat); ?>">
                                    <?php else: ?>
                                        <div class="alert alert-warning">QR Code belum dihasilkan atau gambar tidak ditemukan.</div>
                                    <?php endif; ?>
                                    <h5 class="mt-3"><?php echo html_escape($obat->nama_obat); ?> (ID: <?php echo html_escape($obat->id_obat); ?>)</h5>
                                    <p>UUID: <code><?php echo html_escape($obat->uuid_obat); ?></code></p>
                                    <button class="btn btn-primary mt-3" onclick="window.print();">
                                        <i class="ti ti-printer me-1"></i> Cetak QR Code
                                    </button>
                                </div>
                                <hr class="my-4 no-print">
                                <a href="<?php echo site_url('obat'); ?>" class="btn btn-secondary no-print"><i class="ti ti-arrow-left me-1"></i> Kembali ke Data Obat</a>
                            </div>
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
</body>
</html>