<?php
// application/views/admin/users/form.php

// Pastikan $title, $user, $user_role, $username, $nama_user sudah dilewatkan dari controller
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
    <title><?php echo $title; ?> Apotek </title>
    <meta name="description" content="Form Tambah/Edit Pengguna Aplikasi Apotek." />

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
                        <h4 class="py-3 mb-4"><?php echo $title; ?></h4>

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
                            <h5 class="card-header"><?php echo $title; ?></h5>
                            <div class="card-body">
                                <?php
                                $action_url = isset($user) ? 'users/edit/' . $user->id_user : 'users/add';
                                echo form_open(site_url($action_url));
                                ?>

                                <div class="mb-3">
                                    <label for="nama_user" class="form-label">Nama Pengguna:</label>
                                    <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?php echo set_value('nama_user', isset($user) ? $user->nama_user : ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo set_value('username', isset($user) ? $user->username : ''); ?>" required <?php echo isset($user) ? 'readonly' : ''; ?>>
                                    <?php if(isset($user)): ?>
                                        <small class="text-muted">Username tidak bisa diubah.</small>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email', isset($user) ? $user->email : ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role:</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" <?php echo set_select('role', 'admin', (isset($user) && $user->role == 'admin')); ?>>Admin</option>
                                        <option value="kasir" <?php echo set_select('role', 'kasir', (isset($user) && $user->role == 'kasir')); ?>>Kasir</option>
                                        <option value="apoteker" <?php echo set_select('role', 'apoteker', (isset($user) && $user->role == 'apoteker')); ?>>Apoteker</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <?php echo isset($user) ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?>:</label>
                                    <input type="password" class="form-control" id="password" name="password" <?php echo isset($user) ? '' : 'required'; ?>>
                                </div>
                                <div class="mb-3">
                                    <label for="passconf" class="form-label">Konfirmasi Password <?php echo isset($user) ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?>:</label>
                                    <input type="password" class="form-control" id="passconf" name="passconf" <?php echo isset($user) ? '' : 'required'; ?>>
                                </div>
                                
                                <button type="submit" class="btn btn-primary me-2">Simpan</button>
                                <a href="<?php echo site_url('users'); ?>" class="btn btn-secondary">Batal</a>
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