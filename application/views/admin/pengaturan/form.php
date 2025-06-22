<?php
// File: application/views/admin/pengaturan/form.php

// Pastikan variabel $title, $user_nama, $user_role, dan $pengaturan_array sudah dilewatkan dari controller Admin::pengaturan()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Pengaturan Sistem'; ?> Apotek</title>
    <meta name="description" content="Mengatur pengaturan umum sistem aplikasi Apotek." />

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
                        <?php if ($this->session->flashdata('info')): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('info'); ?>
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
                            <h5 class="card-header">Form Pengaturan Sistem</h5>
                            <div class="card-body">
                                <?php echo form_open_multipart(site_url('pengaturan')); ?>
                                    <div class="mb-3">
                                        <label for="nama_klinik" class="form-label">Nama Klinik/Apotek:</label>
                                        <input type="text" class="form-control" id="nama_klinik" name="nama_klinik" value="<?php echo set_value('nama_klinik', isset($pengaturan_array['nama_klinik']) ? html_escape($pengaturan_array['nama_klinik']) : ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat_klinik" class="form-label">Alamat Klinik/Apotek:</label>
                                        <textarea class="form-control" id="alamat_klinik" name="alamat_klinik" rows="3" required><?php echo set_value('alamat_klinik', isset($pengaturan_array['alamat_klinik']) ? html_escape($pengaturan_array['alamat_klinik']) : ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telepon_klinik" class="form-label">Nomor Telepon Klinik/Apotek:</label>
                                        <input type="text" class="form-control" id="telepon_klinik" name="telepon_klinik" value="<?php echo set_value('telepon_klinik', isset($pengaturan_array['telepon_klinik']) ? html_escape($pengaturan_array['telepon_klinik']) : ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="min_stok_threshold" class="form-label">Batas Minimal Stok Obat:</label>
                                        <input type="number" class="form-control" id="min_stok_threshold" name="min_stok_threshold" value="<?php echo set_value('min_stok_threshold', isset($pengaturan_array['min_stok_threshold']) ? html_escape($pengaturan_array['min_stok_threshold']) : '10'); ?>" required>
                                        <small class="text-muted">Notifikasi akan muncul jika stok obat di bawah batas ini.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo Klinik/Apotek:</label>
                                        <?php if (isset($pengaturan_array['logo_url']) && !empty($pengaturan_array['logo_url'])): ?>
                                            <div class="mb-2">
                                                <img src="<?php echo base_url($pengaturan_array['logo_url']); ?>" alt="Logo Saat Ini" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                                                <p><small class="text-muted">Logo saat ini.</small></p>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" id="logo" name="logo">
                                        <small class="text-muted">Upload file gambar (JPG, PNG, GIF) maksimal 2MB. Kosongkan jika tidak ingin mengubah logo.</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
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