<?php
// File: application/views/apoteker/dashboard.php

// Pastikan variabel $title, $user_nama, $user_role,
// $total_jenis_obat, $obat_menipis_apoteker, $stok_masuk_hari_ini, $obat_kadaluarsa_mendekat
// sudah dilewatkan dari controller Apoteker::index() atau Dashboard::index()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Dashboard Apoteker'; ?> Apotek</title>
    <meta name="description" content="Dashboard untuk peran Apoteker di Aplikasi Apotek." />

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
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php $this->load->view('templates/admin/sidebar'); ?>
            <div class="layout-page">
                <?php $this->load->view('templates/admin/navbar'); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4">Selamat Datang, <?php echo html_escape(isset($user_nama) ? $user_nama : ''); ?> (<?php echo html_escape(isset($user_role) ? ucwords($user_role) : ''); ?>)</h4>

                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Total Jenis Obat</h6>
                                                <h4 class="mb-0"><?php echo html_escape(isset($total_jenis_obat) ? $total_jenis_obat : '0'); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-pill ti-md"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Obat Menipis</h6>
                                                <h4 class="mb-0"><?php echo html_escape(isset($obat_menipis_apoteker) ? count($obat_menipis_apoteker) : '0'); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-alert-triangle ti-md"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Stok Masuk Hari Ini</h6>
                                                <h4 class="mb-0"><?php echo html_escape(isset($stok_masuk_hari_ini) ? $stok_masuk_hari_ini : '0'); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-info"><i class="ti ti-truck-delivery ti-md"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Obat Kadaluarsa/Mendekat</h6>
                                                <h4 class="mb-0"><?php echo html_escape(isset($obat_kadaluarsa_mendekat) ? count($obat_kadaluarsa_mendekat) : '0'); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-calendar-x ti-md"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Obat Dengan Stok Menipis</h5>
                                <ul class="list-group">
                                    <?php if (!empty($obat_menipis_apoteker)): ?>
                                        <?php foreach ($obat_menipis_apoteker as $obat): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo html_escape($obat->nama_obat); ?>
                                                <span class="badge bg-warning"><?php echo html_escape($obat->stok); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">Semua stok obat aman.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Obat Akan/Sudah Kadaluarsa</h5>
                                <ul class="list-group">
                                    <?php if (!empty($obat_kadaluarsa_mendekat)): ?>
                                        <?php foreach ($obat_kadaluarsa_mendekat as $obat): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo html_escape($obat->nama_obat); ?>
                                                <span class="badge bg-danger"><?php echo html_escape($obat->tanggal_kadaluarsa); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">Tidak ada obat yang akan/sudah kadaluarsa dalam 30 hari.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        </div>
                    <footer class="content-footer footer bg-footer-theme">
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