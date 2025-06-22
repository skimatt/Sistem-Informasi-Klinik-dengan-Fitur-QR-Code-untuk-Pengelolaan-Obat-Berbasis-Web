<?php
// File: application/views/templates/admin/sidebar.php

// Pastikan helper 'auth_helper' sudah dimuat sebelumnya di controller.
// Contoh: $this->load->helper('auth_helper');
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="<?php echo site_url('dashboard'); ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0" />
                    <path
                        opacity="0.06"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                        fill="#161616" />
                    <path
                        opacity="0.06"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                        fill="#161616" />
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0" />
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bold">Apotek App</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item <?php echo (uri_string() == 'dashboard' || (is_admin() && uri_string() == 'admin') || (is_apoteker() && uri_string() == 'apoteker') || (is_kasir() && uri_string() == 'kasir')) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('dashboard'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'users') !== FALSE) ? 'active open' : ''; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Manajemen User">Manajemen User</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo (uri_string() == 'users') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('users'); ?>" class="menu-link">
                        <div data-i18n="Daftar Pengguna">Daftar Pengguna</div>
                    </a>
                </li>
                <li class="menu-item <?php echo (uri_string() == 'users/add') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('users/add'); ?>" class="menu-link">
                        <div data-i18n="Tambah Pengguna">Tambah Pengguna</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (has_role(array('admin', 'apoteker'))): ?>
            <li class="menu-item <?php echo (strpos(uri_string(), 'obat') !== FALSE && strpos(uri_string(), 'kategori_obat') === FALSE && strpos(uri_string(), 'jenis_obat') === FALSE) ? 'active open' : ''; ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-medicine-syrup"></i>
                <div data-i18n="Data Obat">Data Obat</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item <?php echo (uri_string() == 'obat' || uri_string() == 'data_obat') ? 'active' : ''; ?>">
                    <a href="<?php echo is_admin() ? site_url('obat') : site_url('data_obat'); ?>" class="menu-link">
                        <div data-i18n="Daftar Obat">Daftar Obat</div>
                    </a>
                </li>
                <?php if (is_admin()): // Hanya Admin yang bisa menambah obat ?>
                <li class="menu-item <?php echo (uri_string() == 'obat/add') ? 'active' : ''; ?>">
                  <a href="<?php echo site_url('obat/add'); ?>" class="menu-link">
                    <div data-i18n="Tambah Obat">Tambah Obat</div>
                  </a>
                </li>
                <?php endif; ?>
                <li class="menu-item <?php echo (uri_string() == 'obat/all_qrcodes') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('obat/all_qrcodes'); ?>" class="menu-link">
                        <i class="ti ti-qrcode me-1"></i> <div data-i18n="Cetak Semua QR">Cetak Semua QR</div>
                    </a>
                </li>
              </ul>
            </li>
            <?php endif; ?>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'kategori_obat') !== FALSE) ? 'active open' : ''; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-category"></i>
                <div data-i18n="Kategori Obat">Kategori Obat</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo (uri_string() == 'kategori_obat') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('kategori_obat'); ?>" class="menu-link">
                        <div data-i18n="Daftar Kategori">Daftar Kategori</div>
                    </a>
                </li>
                <li class="menu-item <?php echo (uri_string() == 'kategori_obat/add') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('kategori_obat/add'); ?>" class="menu-link">
                        <div data-i18n="Tambah Kategori">Tambah Kategori</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'jenis_obat') !== FALSE) ? 'active open' : ''; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-vaccine"></i>
                <div data-i18n="Jenis Obat">Jenis Obat</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo (uri_string() == 'jenis_obat') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('jenis_obat'); ?>" class="menu-link">
                        <div data-i18n="Daftar Jenis">Daftar Jenis</div>
                    </a>
                </li>
                <li class="menu-item <?php echo (uri_string() == 'jenis_obat/add') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('jenis_obat/add'); ?>" class="menu-link">
                        <div data-i18n="Tambah Jenis">Tambah Jenis</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (has_role(array('admin', 'apoteker'))): ?>
        <li class="menu-item <?php echo (uri_string() == 'stok_obat') ? 'active' : ''; ?>">
            <a href="<?php echo site_url('stok_obat'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-boxes"></i>
                <div data-i18n="Stok Obat">Stok Obat</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(array('admin', 'apoteker'))): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'stok_masuk') !== FALSE && uri_string() != 'input_stok_masuk') ? 'active' : ''; ?>">
            <a href="<?php echo site_url('stok_masuk'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-truck-delivery"></i>
                <div data-i18n="Histori Stok Masuk">Histori Stok Masuk</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_apoteker()): ?>
        <li class="menu-item <?php echo (uri_string() == 'input_stok_masuk') ? 'active' : ''; ?>">
            <a href="<?php echo site_url('input_stok_masuk'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box-seam"></i>
                <div data-i18n="Input Stok Masuk">Input Stok Masuk</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'suplier') !== FALSE) ? 'active open' : ''; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building-warehouse"></i>
                <div data-i18n="Suplier">Suplier</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo (uri_string() == 'suplier') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('suplier'); ?>" class="menu-link">
                        <div data-i18n="Daftar Suplier">Daftar Suplier</div>
                    </a>
                </li>
                <li class="menu-item <?php echo (uri_string() == 'suplier/add') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('suplier/add'); ?>" class="menu-link">
                        <div data-i18n="Tambah Suplier">Tambah Suplier</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (is_apoteker()): ?>
        <li class="menu-item <?php echo (uri_string() == 'scan_qr_obat') ? 'active' : ''; ?>">
            <a href="<?php echo site_url('scan_qr_obat'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-qrcode"></i>
                <div data-i18n="Scan QR Obat">Scan QR Obat</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_apoteker()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'obat_kadaluarsa') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('obat_kadaluarsa'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calendar-x"></i>
                <div data-i18n="Obat Kadaluarsa">Obat Kadaluarsa</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_apoteker()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'laporan_obat_masuk') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('laporan_obat_masuk'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-analytics"></i>
                <div data-i18n="Laporan Obat Masuk">Laporan Obat Masuk</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_kasir()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'transaksi_penjualan') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('transaksi_penjualan'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-cash"></i>
                <div data-i18n="Transaksi Penjualan">Transaksi Penjualan</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_kasir()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'riwayat_transaksi') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('riwayat_transaksi'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-history"></i>
                <div data-i18n="Riwayat Transaksi">Riwayat Transaksi</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(array('admin', 'kasir'))): ?>
        <li class="menu-item <?php echo (uri_string() == 'laporan_penjualan' || uri_string() == 'laporan_penjualan_kasir') ? 'active' : ''; ?>">
            <a href="<?php echo is_admin() ? site_url('laporan_penjualan') : site_url('laporan_penjualan_kasir'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                <div data-i18n="Laporan Penjualan">Laporan Penjualan</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'log_aktivitas') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('log_aktivitas'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div data-i18n="Log Aktivitas">Log Aktivitas</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (is_admin()): ?>
        <li class="menu-item <?php echo (strpos(uri_string(), 'pengaturan') !== FALSE) ? 'active' : ''; ?>">
            <a href="<?php echo site_url('pengaturan'); ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Pengaturan Sistem">Pengaturan Sistem</div>
            </a>
        </li>
        <?php endif; ?>

    </ul>
</aside>