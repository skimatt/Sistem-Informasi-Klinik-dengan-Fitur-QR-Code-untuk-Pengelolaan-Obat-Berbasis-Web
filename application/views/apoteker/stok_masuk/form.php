<?php
// File: application/views/apoteker/stok_masuk/form.php

// Pastikan variabel $title, $user_nama, $user_role,
// $obat_list, dan $suplier_list sudah dilewatkan dari controller Apoteker::input_stok_masuk()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Input Stok Masuk'; ?> Apotek</title>
    <meta name="description" content="Form untuk menginput kedatangan obat baru dari suplier." />

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


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
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?php echo validation_errors(); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <h5 class="card-header"><?php echo html_escape($title); ?></h5>
                            <div class="card-body">
                                <?php echo form_open(site_url('input_stok_masuk')); ?>

                                <div class="mb-3">
                                    <label for="id_obat" class="form-label">Nama Obat:</label>
                                    <select class="form-select select2-enabled" id="id_obat" name="id_obat" required>
                                        <option value="">Pilih Obat</option>
                                        <?php if (!empty($obat_list)): ?>
                                            <?php foreach ($obat_list as $obat): ?>
                                                <option value="<?php echo html_escape($obat->id_obat); ?>" <?php echo set_select('id_obat', $obat->id_obat); ?>>
                                                    <?php echo html_escape($obat->nama_obat); ?> (Stok: <?php echo html_escape($obat->stok); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_suplier" class="form-label">Suplier:</label>
                                    <select class="form-select select2-enabled" id="id_suplier" name="id_suplier" required>
                                        <option value="">Pilih Suplier</option>
                                        <?php if (!empty($suplier_list)): ?>
                                            <?php foreach ($suplier_list as $suplier): ?>
                                                <option value="<?php echo html_escape($suplier->id_suplier); ?>" <?php echo set_select('id_suplier', $suplier->id_suplier); ?>>
                                                    <?php echo html_escape($suplier->nama_suplier); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">Jumlah Masuk:</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo set_value('jumlah'); ?>" required min="1">
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk:</label>
                                    <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?php echo set_value('tanggal_masuk', date('Y-m-d')); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan (Opsional):</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo set_value('keterangan'); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary me-2">Simpan Stok Masuk</button>
                                <a href="<?php echo site_url('apoteker'); ?>" class="btn btn-secondary">Batal</a>
                                <?php echo form_close(); ?>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
            // Inisialisasi Select2 pada dropdown
            $('.select2-enabled').select2({
                placeholder: "Pilih...",
                allowClear: true // Tambahkan tombol silang untuk clear pilihan
            });

            // Set tanggal_masuk ke tanggal hari ini secara default
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = yyyy + '-' + mm + '-' + dd;
            $('#tanggal_masuk').val(today);
        });
    </script>
</body>
</html>