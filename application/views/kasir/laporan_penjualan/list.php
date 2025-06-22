<?php
// File: application/views/kasir/laporan_penjualan/list.php

// Pastikan variabel $title, $user_nama, $user_role, $penjualan,
// $start_date_filter, dan $end_date_filter sudah dilewatkan dari controller Kasir::laporan_penjualan_kasir()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Laporan Penjualan Pribadi'; ?> Apotek</title>
    <meta name="description" content="Melihat laporan transaksi penjualan pribadi oleh kasir." />

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
                            <h5 class="card-header">Laporan Transaksi Penjualan Saya</h5>
                            <div class="card-body">
                                <div class="mb-3">
                                    <?php echo form_open(site_url('laporan_penjualan_kasir'), array('method' => 'get', 'class' => 'd-flex flex-wrap align-items-center mb-3')); ?>
                                        <div class="me-2 mb-2">
                                            <label for="start_date" class="form-label visually-hidden">Dari Tanggal:</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo html_escape(isset($start_date_filter) ? $start_date_filter : ''); ?>">
                                        </div>
                                        <div class="me-2 mb-2">
                                            <label for="end_date" class="form-label visually-hidden">Sampai Tanggal:</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo html_escape(isset($end_date_filter) ? $end_date_filter : ''); ?>">
                                        </div>
                                        <button type="submit" class="btn btn-secondary me-2 mb-2"><i class="ti ti-filter me-1"></i> Filter</button>
                                        <a href="<?php echo site_url('laporan_penjualan_kasir'); ?>" class="btn btn-outline-secondary me-2 mb-2"><i class="ti ti-rotate-clockwise me-1"></i> Reset</a>
                                        <button type="submit" formaction="<?php echo site_url('laporan_penjualan_kasir/export'); ?>" class="btn btn-success mb-2"><i class="ti ti-file-export me-1"></i> Export PDF</button>
                                    <?php echo form_close(); ?>
                                </div>

                                <div class="table-responsive text-nowrap">
                                    <table id="laporanPenjualanKasirTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>UUID Penjualan</th>
                                                <th>Tanggal Penjualan</th>
                                                <th>Total Harga</th>
                                                <th>Metode Bayar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php if (!empty($penjualan)): ?>
                                                <?php $no = 1; foreach ($penjualan as $row): ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo html_escape($row->uuid_penjualan); ?></td>
                                                        <td><?php echo html_escape(date('d F Y H:i', strtotime($row->tgl_penjualan))); ?></td>
                                                        <td>Rp. <?php echo html_escape(number_format($row->total_harga, 2, ',', '.')); ?></td>
                                                        <td><?php echo html_escape(ucwords($row->metode_bayar)); ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm view-detail-transaksi" data-id="<?php echo html_escape($row->id_penjualan); ?>">
                                                                <i class="ti ti-eye me-1"></i> Detail
                                                            </button>
                                                            <a href="<?php echo site_url('cetak_struk/' . $row->id_penjualan); ?>" target="_blank" class="btn btn-light btn-sm">
                                                                <i class="ti ti-printer me-1"></i> Cetak Struk
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data penjualan pribadi.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="detailTransaksiModal" tabindex="-1" aria-labelledby="detailTransaksiModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailTransaksiModalLabel">Detail Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>UUID Penjualan:</strong> <span id="modal_uuid_penjualan"></span></p>
                                        <p><strong>Tanggal Penjualan:</strong> <span id="modal_tgl_penjualan"></span></p>
                                        <p><strong>Kasir:</strong> <span id="modal_kasir"></span></p>
                                        <p><strong>Metode Bayar:</strong> <span id="modal_metode_bayar"></span></p>
                                        <h6>Daftar Obat:</h6>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Obat</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modal_detail_obat_list">
                                                </tbody>
                                        </table>
                                        <p class="text-end"><strong>Total Harga:</strong> <span id="modal_total_harga"></span></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
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
            // --- GLOBAL CSRF HANDLING (Versi Super Explicit untuk PHP5/CI3 yang Bandel) ---
            // Fungsi untuk mendapatkan token CSRF terbaru dari DOM
            function getCsrfToken() {
                return {
                    name: $('input#csrf_token_input').attr('name'),
                    hash: $('input#csrf_token_input').val()
                };
            }

            // Fungsi untuk mengupdate token CSRF di DOM
            function updateCsrfTokenInDom(newHash) {
                if (newHash && newHash !== getCsrfToken().hash) {
                    $('input#csrf_token_input').val(newHash);
                    console.log("CSRF token updated in DOM:", newHash);
                }
            }

            // Setel AJAX Setup untuk MENAMBAHKAN token ke SEMUA request POST
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (settings.type === 'POST') {
                        var token = getCsrfToken();
                        var data = settings.data;

                        // Pastikan data adalah objek atau string
                        if (typeof data === 'string') {
                            // Jika string (misal: "key=value&..."), tambahkan token
                            settings.data += '&' + token.name + '=' + token.hash;
                        } else if (typeof data === 'object' && data !== null) {
                            // Jika objek, tambahkan properti token
                            data[token.name] = token.hash;
                        }
                        // Untuk FormData, harus ditambahkan secara manual di tempatnya, tidak di sini
                        settings.data = data; // Penting: update settings.data
                    }
                }
            });

            // Update token CSRF setelah SETIAP AJAX request yang berhasil (dari respons JSON)
            $(document).ajaxComplete(function(event, xhr, settings) {
                if (settings.type === 'POST') {
                    // Periksa header Content-Type agar hanya memproses JSON response
                    if (xhr.getResponseHeader('Content-Type') && xhr.getResponseHeader('Content-Type').indexOf('application/json') !== -1) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.csrf_hash) {
                                updateCsrfTokenInDom(response.csrf_hash);
                            }
                        } catch (e) {
                            console.warn("AJAX complete: Could not parse JSON response or no CSRF hash. Ignoring for CSRF update.", e);
                        }
                    }
                }
            });

            // Inisialisasi DataTables
            $('#laporanPenjualanKasirTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Opsional: Bahasa Indonesia
                },
                "order": [[ 2, "desc" ]] // Urutkan berdasarkan Tanggal Penjualan (kolom indeks 2) secara descending
            });

            // Handle klik tombol "Detail Transaksi"
            $('#laporanPenjualanKasirTable tbody').on('click', '.view-detail-transaksi', function() {
                var id_penjualan = $(this).data('id');
                
                // Ini GET request, jadi CSRF token tidak otomatis ditambahkan di data oleh ajaxSetup()
                // Tapi Anda bisa tambahkan jika ingin (meskipun tidak wajib untuk GET)
                $.ajax({
                    url: '<?php echo site_url("laporan_penjualan/detail_ajax"); ?>/' + id_penjualan, // Rute yang sama dengan admin
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            var penjualan = response.data;
                            var details = response.details;

                            // Isi data ke modal
                            $('#modal_uuid_penjualan').text(penjualan.uuid_penjualan);
                            $('#modal_tgl_penjualan').text(new Date(penjualan.tgl_penjualan).toLocaleString('id-ID', {
                                year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                            }));
                            $('#modal_kasir').text(penjualan.nama_kasir_lengkap || penjualan.nama_kasir); // Prefer nama_kasir_lengkap
                            $('#modal_metode_bayar').text(penjualan.metode_bayar.charAt(0).toUpperCase() + penjualan.metode_bayar.slice(1));
                            $('#modal_total_harga').text('Rp. ' + parseFloat(penjualan.total_harga).toLocaleString('id-ID', { minimumFractionDigits: 2 }));

                            var detailHtml = '';
                            $.each(details, function(i, item) {
                                detailHtml += '<tr>';
                                detailHtml += '<td>' + item.nama_obat + '</td>';
                                detailHtml += '<td>' + item.jumlah + '</td>';
                                detailHtml += '<td>Rp. ' + parseFloat(item.harga_satuan).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '</td>';
                                detailHtml += '<td>Rp. ' + parseFloat(item.subtotal).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '</td>';
                                detailHtml += '</tr>';
                            });
                            $('#modal_detail_obat_list').html(detailHtml);

                            // Tampilkan modal
                            $('#detailTransaksiModal').modal('show');
                        } else {
                            alert('Gagal mengambil detail transaksi: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan saat memuat detail transaksi. Silakan coba lagi.');
                        console.error('AJAX Error:', xhr.responseText);
                        // Untuk GET request, CSRF tidak relevan, jadi alert biasa
                        alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                    }
                });
            });
        });
    </script>
</body>
</html>