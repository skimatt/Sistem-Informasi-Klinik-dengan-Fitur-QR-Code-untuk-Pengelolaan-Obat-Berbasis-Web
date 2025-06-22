<?php
// File: application/views/admin/stok_obat/list.php

// Pastikan variabel $title, $user_nama, $user_role, $obat, dan $filter_aktif sudah dilewatkan dari controller Admin::stok_obat()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Stok Obat'; ?> Apotek</title>
    <meta name="description" content="Manajemen Stok Obat Aplikasi Apotek." />

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">


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
                        <h4 class="py-3 mb-4"><?php echo html_escape($title); ?></h4>

                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('success'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card">
                            <h5 class="card-header">Daftar Stok Obat</h5>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="filter_stok" class="form-label">Filter Stok:</label>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="<?php echo site_url('stok_obat'); ?>" class="btn btn-<?php echo (empty($filter_aktif)) ? 'primary' : 'outline-primary'; ?>">Semua Stok</a>
                                        <a href="<?php echo site_url('stok_obat?filter=menipis'); ?>" class="btn btn-<?php echo (isset($filter_aktif) && $filter_aktif == 'menipis') ? 'warning' : 'outline-warning'; ?>">Stok Menipis</a>
                                        <a href="<?php echo site_url('stok_obat?filter=kadaluarsa'); ?>" class="btn btn-<?php echo (isset($filter_aktif) && $filter_aktif == 'kadaluarsa') ? 'danger' : 'outline-danger'; ?>">Obat Kadaluarsa</a>
                                    </div>
                                </div>

                                <div class="table-responsive text-nowrap">
                                    <table id="stokObatTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Obat</th>
                                                <th>Kategori</th>
                                                <th>Jenis</th>
                                                <th>Stok</th>
                                                <th>Harga</th>
                                                <th>Tanggal Kadaluarsa</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php if (!empty($obat)): ?>
                                                <?php $no = 1; foreach ($obat as $row): ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo html_escape($row->nama_obat); ?></td>
                                                        <td><?php echo html_escape($row->nama_kategori); ?></td>
                                                        <td><?php echo html_escape($row->nama_jenis); ?></td>
                                                        <td><?php echo html_escape($row->stok); ?></td>
                                                        <td>Rp. <?php echo html_escape(number_format($row->harga, 2, ',', '.')); ?></td>
                                                        <td><?php echo html_escape($row->tanggal_kadaluarsa); ?></td>
                                                        <td>
                                                            <?php
                                                            $status_label = 'secondary';
                                                            $status_text = 'Normal';
                                                            $today = strtotime(date('Y-m-d'));
                                                            $expiry_date = strtotime($row->tanggal_kadaluarsa);

                                                            // Mendapatkan nilai threshold stok minimal dari Pengaturan Sistem atau default 10
                                                            $min_stok_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('min_stok_threshold');
                                                            $threshold_value = (isset($min_stok_setting->nilai_pengaturan) && is_numeric($min_stok_setting->nilai_pengaturan)) ? $min_stok_setting->nilai_pengaturan : 10;


                                                            if ($row->stok <= $threshold_value) {
                                                                $status_label = 'warning';
                                                                $status_text = 'Menipis';
                                                            }

                                                            // Cek kadaluarsa
                                                            $days_to_expire = ceil(($expiry_date - $today) / (60 * 60 * 24));
                                                            if ($days_to_expire <= 0) {
                                                                $status_label = 'danger';
                                                                $status_text = 'Kadaluarsa';
                                                            } elseif ($days_to_expire <= 30) { // Kurang dari 30 hari lagi kadaluarsa
                                                                $status_label = 'info'; // Atau warna lain untuk "mendekati kadaluarsa"
                                                                $status_text = 'Akan Kadaluarsa (' . $days_to_expire . ' hari)';
                                                            }
                                                            ?>
                                                            <span class="badge bg-label-<?php echo $status_label; ?>"><?php echo $status_text; ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">Tidak ada data stok obat.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

    <?php
    if (isset($js_files) && is_array($js_files)) {
        foreach ($js_files as $js_file) {
            echo '<script src="' . base_url('assets/js/' . $js_file) . '"></script>' . "\n";
        }
    }
    ?>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#stokObatTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Opsional: Bahasa Indonesia
                },
                "order": [] // Nonaktifkan ordering default agar filter manual lebih mudah
            });

            // Tambahan: Tambahkan kelas 'active' pada tombol filter sesuai dengan parameter URL
            var urlParams = new URLSearchParams(window.location.search);
            var filter = urlParams.get('filter');

            if (filter === 'menipis') {
                $('.btn-group a[href*="filter=menipis"]').removeClass('btn-outline-warning').addClass('btn-warning');
                $('.btn-group a[href*="filter=menipis"]').siblings().removeClass('btn-primary btn-warning btn-danger').addClass('btn-outline-primary btn-outline-warning btn-outline-danger');
            } else if (filter === 'kadaluarsa') {
                $('.btn-group a[href*="filter=kadaluarsa"]').removeClass('btn-outline-danger').addClass('btn-danger');
                $('.btn-group a[href*="filter=kadaluarsa"]').siblings().removeClass('btn-primary btn-warning btn-danger').addClass('btn-outline-primary btn-outline-warning btn-outline-danger');
            } else {
                $('.btn-group a[href$="/stok_obat"]').removeClass('btn-outline-primary').addClass('btn-primary');
                $('.btn-group a[href$="/stok_obat"]').siblings().removeClass('btn-primary btn-warning btn-danger').addClass('btn-outline-primary btn-outline-warning btn-outline-danger');
            }
        });
    </script>
</body>
</html>