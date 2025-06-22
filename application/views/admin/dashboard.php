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
    <meta name="description" content="Dashboard admin untuk Puskesmas, kelola antrian, notifikasi, riwayat kunjungan, resep, dan rekam medis dengan mudah." />

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
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

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
                        <h4 class="py-4 mb-6">Selamat Datang, <?php echo html_escape($nama_user); ?> (<?php echo html_escape(ucwords($user_role)); ?>)</h4>

                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Total Obat</h6>
                                                <h4 class="mb-0"><?php echo html_escape($total_obat); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-medicine-syrup ti-md"></i></span>
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
                                                <h4 class="mb-0"><?php echo count($obat_menipis); ?></h4>
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
                                                <h6 class="mb-2">Jumlah Pengguna</h6>
                                                <h4 class="mb-0"><?php echo html_escape($jumlah_user); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-info"><i class="ti ti-users ti-md"></i></span>
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
                                                <h6 class="mb-2">Total Transaksi Hari Ini</h6>
                                                <h4 class="mb-0">Rp. <?php echo html_escape(number_format($total_penjualan_hari_ini, 2, ',', '.')); ?></h4>
                                            </div>
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-success"><i class="ti ti-currency-dollar ti-md"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Grafik Transaksi Bulanan (Tahun <?php echo date('Y'); ?>)</h5>
                                <small class="text-muted">Total Penjualan per Bulan</small>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlySalesChart" class="chartjs" data-height="400"></canvas>
                            </div>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var ctx = document.getElementById('monthlySalesChart').getContext('2d');
                                var monthlySalesData = <?php echo json_encode($grafik_transaksi_bulanan); ?>;

                                var labels = monthlySalesData.map(function(item) {
                                    var date = new Date(item.month + '-01');
                                    return date.toLocaleString('id-ID', { month: 'long', year: 'numeric' });
                                });
                                var data = monthlySalesData.map(function(item) {
                                    return parseFloat(item.total_sales);
                                });

                                var monthlySalesChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Total Penjualan',
                                            data: data,
                                            backgroundColor: 'rgba(115, 103, 240, 0.6)',
                                            borderColor: 'rgba(115, 103, 240, 1)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: 'Total Penjualan (Rp)'
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Bulan'
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        var label = context.dataset.label || '';
                                                        if (label) {
                                                            label += ': ';
                                                        }
                                                        label += 'Rp. ' + context.parsed.y.toLocaleString('id-ID');
                                                        return label;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                        </script>


                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Log Aktivitas Terbaru</h5>
                                <ul class="list-group">
                                    <?php if (!empty($recent_logs)): ?>
                                        <?php foreach ($recent_logs as $log): ?>
                                            <li class="list-group-item">
                                                [<?php echo html_escape(date('d M Y H:i', strtotime($log->waktu))); ?>]
                                                <strong><?php echo html_escape(isset($log->nama_user) ? $log->nama_user : (isset($log->username) ? $log->username : 'Sistem')); ?></strong>: <?php echo html_escape($log->aktivitas); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item">Tidak ada log aktivitas terbaru.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Obat Dengan Stok Menipis</h5>
                                <ul class="list-group">
                                    <?php if (!empty($obat_menipis)): ?>
                                        <?php foreach ($obat_menipis as $obat): ?>
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
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

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