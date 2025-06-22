<?php
// File: application/views/kasir/transaksi_penjualan/form.php

// Pastikan variabel $title, $user_nama, $user_role, $cart_items, $total_belanja,
// dan $obat_list_dropdown sudah dilewatkan dari controller Kasir::transaksi_penjualan()
// Juga pastikan form_helper sudah di-load di controller untuk form_open() dan form_hidden()
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
    <title><?php echo isset($title) ? html_escape($title) : 'Transaksi Penjualan'; ?> Apotek</title>
    <meta name="description" content="Melakukan transaksi penjualan obat." />

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
    <style>
        .summary-box {
            background-color: #f8f9fa;
            border-left: 5px solid #007bff;
            padding: 15px;
            margin-bottom: 20px;
        }
        .total-amount {
            font-size: 2em;
            font-weight: bold;
            color: #28a745;
        }
        #preview_transaksi {
            width: 100%;
            max-height: 240px;
            border: 1px solid #ddd;
            background-color: #000; /* Agar terlihat hitam jika kamera belum aktif */
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
                        <?php // Pastikan form_helper di-load di controller untuk ini ?>
                        <input type="hidden" id="csrf_token_input" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <audio id="scannerBeepSound" src="<?php echo base_url('assets/sounds/beep.mp3'); ?>" preload="auto"></audio>
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
                        <?php if ($this->session->flashdata('form_error')): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?php echo $this->session->flashdata('form_error'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="card mb-4">
                                    <h5 class="card-header">Tambah Obat ke Keranjang</h5>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="qr_obat_data" class="form-label">Scan QR Obat (atau input manual):</label>
                                            <input type="text" class="form-control" id="qr_obat_data" placeholder="Scan QR Code di sini..." autofocus>
                                        </div>
                                        <div class="mb-3">
                                            <video id="preview_transaksi" autoplay="true" muted="true" style="width: 100%; max-height: 240px; border: 1px solid #ddd;"></video>
                                            <small class="text-muted">Pastikan kamera aktif dan mengarah ke QR Code.</small>
                                            <div class="mt-2">
                                                <button type="button" id="startScannerTransaksiBtn" class="btn btn-sm btn-info me-2">Mulai Scan Kamera</button>
                                                <button type="button" id="stopScannerTransaksiBtn" class="btn btn-sm btn-outline-info">Stop Scan Kamera</button>
                                            </div>
                                        </div>

                                        <div class="text-center mb-3">
                                            <hr> ATAU <hr>
                                        </div>
                                        <div class="mb-3">
                                            <label for="id_obat_manual" class="form-label">Pilih Obat dari Daftar:</label>
                                            <select class="form-select select2-enabled" id="id_obat_manual">
                                                <option value="">-- Pilih Obat --</option>
                                                <?php if (!empty($obat_list_dropdown)): ?>
                                                    <?php foreach ($obat_list_dropdown as $obat): ?>
                                                        <option value="<?php echo html_escape($obat->id_obat); ?>" data-stok="<?php echo html_escape($obat->stok); ?>" data-harga="<?php echo html_escape($obat->harga); ?>" <?php if($obat->tanggal_kadaluarsa < date('Y-m-d')): ?> disabled title="Obat ini sudah kadaluarsa"<?php endif; ?> >
                                                            <?php echo html_escape($obat->nama_obat); ?> (Stok: <?php echo html_escape($obat->stok); ?>, Harga: Rp. <?php echo html_escape(number_format($obat->harga, 0, ',', '.')); ?>) <?php if($obat->tanggal_kadaluarsa < date('Y-m-d')): ?> [KADALUARSA] <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted" id="selected_obat_info"></small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jumlah_beli" class="form-label">Jumlah Beli:</label>
                                            <input type="number" class="form-control" id="jumlah_beli" value="1" min="1">
                                        </div>
                                        <button type="button" id="add_item_to_cart_btn" class="btn btn-primary" disabled><i class="ti ti-shopping-cart-plus me-1"></i> Tambah ke Keranjang</button>
                                    </div>
                                </div>

                                <div class="card">
                                    <h5 class="card-header">Keranjang Belanja</h5>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Obat</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga Satuan</th>
                                                        <th>Subtotal</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cart_items_body">
                                                    <?php if (!empty($cart_items)): ?>
                                                        <?php foreach ($cart_items as $item): ?>
                                                            <tr data-id_obat="<?php echo html_escape($item['id_obat']); ?>">
                                                                <td><?php echo html_escape($item['nama_obat']); ?></td>
                                                                <td><?php echo html_escape($item['jumlah']); ?></td>
                                                                <td>Rp. <?php echo html_escape(number_format($item['harga_satuan'], 2, ',', '.')); ?></td>
                                                                <td>Rp. <?php echo html_escape(number_format($item['subtotal'], 2, ',', '.')); ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger remove-item-from-cart">
                                                                        <i class="ti ti-trash"></i> Hapus
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr id="empty_cart_row">
                                                            <td colspan="5" class="text-center">Keranjang belanja kosong.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card">
                                    <h5 class="card-header">Ringkasan Pembayaran</h5>
                                    <div class="card-body">
                                        <div class="summary-box text-center mb-4">
                                            <p class="mb-0">Total Belanja:</p>
                                            <h3 class="total-amount" id="display_total_belanja">Rp. <?php echo html_escape(number_format($total_belanja, 2, ',', '.')); ?></h3>
                                        </div>

                                        <?php echo form_open(site_url('transaksi_penjualan'), array('id' => 'process_payment_form')); ?>
                                            <input type="hidden" name="process_payment" value="1">
                                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                            
                                            <div class="mb-3">
                                                <label for="metode_bayar" class="form-label">Metode Pembayaran:</label>
                                                <select class="form-select" id="metode_bayar" name="metode_bayar" required>
                                                    <option value="tunai" <?php echo set_select('metode_bayar', 'tunai'); ?>>Tunai</option>
                                                    <option value="non-tunai" <?php echo set_select('metode_bayar', 'non-tunai'); ?>>Non-Tunai</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="bayar_amount" class="form-label">Jumlah Bayar:</label>
                                                <input type="number" class="form-control" id="bayar_amount" name="bayar_amount" value="<?php echo set_value('bayar_amount'); ?>" required min="0">
                                            </div>
                                            <div class="mb-3">
                                                <label for="kembalian" class="form-label">Kembalian:</label>
                                                <input type="text" class="form-control" id="kembalian" name="kembalian" readonly>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100" id="btn_proses_bayar">Proses Pembayaran</button>
                                        <?php echo form_close(); ?>

                                        <a href="<?php echo site_url('transaksi_penjualan'); ?>" class="btn btn-outline-secondary w-100 mt-3">Batalkan Transaksi</a>
                                        <?php if ($this->session->flashdata('last_penjualan_id')): ?>
                                            <a href="<?php echo site_url('cetak_struk/' . $this->session->flashdata('last_penjualan_id')); ?>" target="_blank" class="btn btn-info w-100 mt-2">Cetak Struk Terakhir</a>
                                        <?php endif; ?>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?php echo base_url('assets/js/vendor/instascan.min.js'); ?>"></script>

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
        // --- GLOBAL CSRF HANDLING ---
        function getCsrfToken() {
            return {
                name: $('input#csrf_token_input').attr('name'),
                hash: $('input#csrf_token_input').val()
            };
        }

        function updateCsrfTokenInDom(newHash) {
            if (newHash && newHash !== getCsrfToken().hash) {
                $('input#csrf_token_input').val(newHash);
                console.log("CSRF token updated in DOM:", newHash);
            }
        }

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (settings.type === 'POST') {
                    var token = getCsrfToken();
                    var data = settings.data;
                    if (typeof data === 'string') {
                        settings.data += '&' + token.name + '=' + token.hash;
                    } else if (typeof data === 'object' && data !== null) {
                        data[token.name] = token.hash;
                    }
                    settings.data = data;
                }
            }
        });

        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.type === 'POST') {
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


        // --- Inisialisasi Select2 ---
        $('.select2-enabled').select2({
            placeholder: "Pilih Obat",
            allowClear: true
        });

        // --- Fungsi untuk menghitung kembalian ---
        function hitungKembalian() {
            var totalBelanjaText = $('#display_total_belanja').text();
            var totalBelanja = parseFloat(totalBelanjaText.replace('Rp. ', '').replace(/\./g, '').replace(',', '.'));
            
            var jumlahBayar = parseFloat($('#bayar_amount').val());
            var kembalian = jumlahBayar - totalBelanja;
            if (!isNaN(kembalian)) {
                $('#kembalian').val('Rp. ' + kembalian.toLocaleString('id-ID', { minimumFractionDigits: 2 }));
            } else {
                $('#kembalian').val('Rp. 0,00');
            }
        }

        // Panggil fungsi kembalian saat jumlah bayar berubah
        $('#bayar_amount').on('input', hitungKembalian);
        hitungKembalian(); 

        // --- Logika Scanner QR Code ---
        var scanner_transaksi = null;
        var beepSound = document.getElementById('scannerBeepSound'); // Ambil elemen audio
        var audioEnabled = false; // Flag untuk melacak apakah audio sudah diizinkan

        // --- Fungsi untuk mengaktifkan audio setelah interaksi pertama ---
        function enableAudioOnFirstInteraction() {
            if (!audioEnabled && beepSound) {
                // Coba putar suara senyap atau suara beep singkat
                beepSound.volume = 0; // Mulai dengan volume 0
                var playPromise = beepSound.play();

                if (playPromise !== undefined) {
                    playPromise.then(function() {
                        // Autoplay diizinkan, aktifkan audio
                        audioEnabled = true;
                        beepSound.volume = 0.5; // Kembalikan volume normal
                        console.log("Audio permission granted and enabled.");
                    }).catch(function(error) {
                        // Autoplay diblokir, tetap set audioEnabled ke false
                        console.warn("Audio autoplay blocked, will try again on next user interaction: ", error);
                        audioEnabled = false;
                    });
                }
            }
        }

        // --- Event listener untuk interaksi pertama pengguna (global) ---
        // Pemicu umum untuk mengaktifkan audio
        $(document).one('click keydown', function() { // Gunakan .one() agar hanya sekali
            enableAudioOnFirstInteraction();
        });

        // Fungsi untuk memutar suara beep
        function playBeepSound() {
            if (audioEnabled && beepSound) { // Hanya putar jika audio sudah diizinkan
                beepSound.currentTime = 0; // Reset ke awal
                beepSound.play().catch(function(e) {
                    console.warn("Failed to play beep sound, might be autoplay policy: ", e);
                });
            } else if (beepSound) {
                console.log("Audio not yet enabled by user interaction. Attempting to enable.");
                // Jika belum enabled, coba enable lagi (mungkin interaksi belum terdeteksi .one())
                enableAudioOnFirstInteraction();
                // Tetap coba putar jika sudah enabled (misal, setelah enableAudioOnFirstInteraction() berhasil)
                if (audioEnabled) {
                     beepSound.currentTime = 0;
                     beepSound.play().catch(function(e){ console.warn("Retry play failed: ", e); });
                }
            } else {
                console.error("Beep sound element not found.");
            }
        }


        // Fungsi untuk memulai scanner kamera
        function startScannerTransaksi() {
            if (scanner_transaksi) {
                scanner_transaksi.stop();
                scanner_transaksi = null;
            }

            Instascan.Camera.getCameras().then(function (cameras) {
                console.log('--- Transaksi Penjualan: Daftar Kamera Ditemukan ---', cameras);

                if (cameras.length > 0) {
                    var selectedCamera = null;
                    if (cameras[1] && cameras[1].name.toLowerCase().indexOf('back') !== -1) {
                        selectedCamera = cameras[1];
                        console.log('Transaksi Penjualan: Menggunakan kamera belakang.');
                    } else if (cameras[0]) {
                        selectedCamera = cameras[0];
                        console.log('Transaksi Penjualan: Menggunakan kamera utama (indeks 0).');
                    } else {
                        console.error('Transaksi Penjualan: Tidak ada kamera yang cocok ditemukan.');
                        alert('Tidak ada kamera yang cocok ditemukan. Pastikan kamera terhubung.');
                        return;
                    }
                    
                    scanner_transaksi = new Instascan.Scanner({
                        video: document.getElementById('preview_transaksi'),
                        scanPeriod: 5,
                        mirror: false
                    });

                    scanner_transaksi.addListener('scan', function (content) {
                        $('#qr_obat_data').val(content).trigger('change'); // Pemicu 'change' agar fetchObatInfoForCart terpanggil
                        // scanner_transaksi.stop(); // Opsional: hentikan setelah scan jika hanya ingin 1 scan per trigger
                    });

                    scanner_transaksi.start(selectedCamera)
                        .then(function() {
                            console.log('Transaksi Penjualan: Kamera berhasil dimulai.');
                            $('#qr_obat_data').focus(); // Fokuskan ke input QR untuk input manual/scanner
                        })
                        .catch(function (e) {
                            console.error('Transaksi Penjualan: Error saat memulai kamera.', e);
                            alert('Gagal memulai kamera transaksi. Pastikan kamera tidak digunakan oleh aplikasi lain dan Anda telah memberikan izin. Error: ' + e.name + ' - ' + e.message);
                        });

                } else {
                    console.error('Transaksi Penjualan: Tidak ada kamera ditemukan.');
                    alert('Tidak ada kamera yang terdeteksi di perangkat Anda.');
                }
            }).catch(function (e) {
                console.error('Transaksi Penjualan: Error saat mendapatkan daftar kamera.', e);
                alert('Error saat mengakses kamera transaksi: ' + e.name + ' - ' + e.message + '. Pastikan browser Anda diakses via HTTPS.');
            });
        }

        // Fungsi untuk menghentikan scanner kamera
        function stopScannerTransaksi() {
            if (scanner_transaksi) {
                scanner_transaksi.stop();
                scanner_transaksi = null;
                console.log('Transaksi Penjualan: Kamera dihentikan.');
            }
            // Kosongkan input dan info obat saat stop
            $('#qr_obat_data').val('');
            $('#id_obat_manual').val('').trigger('change');
            $('#selected_obat_info').text('');
            $('#jumlah_beli').val(1).attr('max', 1);
            $('#add_item_to_cart_btn').prop('disabled', true); // Nonaktifkan tombol tambah
        }

        // Panggil startScannerTransaksi() secara otomatis saat halaman dimuat
        startScannerTransaksi();

        // Tombol untuk memulai/menghentikan scanner secara manual (opsional, jika Anda ingin mempertahankan tombol)
        $('#startScannerTransaksiBtn').on('click', startScannerTransaksi);
        $('#stopScannerTransaksiBtn').on('click', stopScannerTransaksi);

        // --- Logika AJAX untuk mendapatkan info obat ---
        function fetchObatInfoForCart(qrDataRaw, obatIdManual) {
            var processedQrData = null;

            // Jika qrDataRaw adalah URL, ekstrak UUID-nya
            if (qrDataRaw && qrDataRaw.indexOf('<?php echo site_url('info_obat/'); ?>') === 0) {
                processedQrData = 'APOTEK_OBAT_' + qrDataRaw.substring('<?php echo site_url('info_obat/'); ?>'.length);
            } else if (qrDataRaw) {
                processedQrData = qrDataRaw;
            }
            
            $.ajax({
                url: '<?php echo site_url("kasir/get_obat_info_for_kasir_ajax"); ?>',
                type: 'POST',
                dataType: 'json',
                data: { qr_data: processedQrData, obat_id: obatIdManual },
                success: function(response) {
                    updateCsrfTokenInDom(response.csrf_hash);
                    
                    if (response.status == 'success') {
                        var obat = response.data;
                        if(obat.is_expired) {
                            $('#selected_obat_info').html('<span class="text-danger">Obat ini sudah kadaluarsa! Tidak bisa dijual.</span>');
                            $('#jumlah_beli').val(1).attr('max', 1);
                            $('#add_item_to_cart_btn').prop('disabled', true);
                            alert('Obat ' + obat.nama_obat + ' sudah kadaluarsa dan tidak dapat dijual.');
                        } else if (obat.stok <= 0) {
                            $('#selected_obat_info').html('<span class="text-danger">Stok ' + obat.nama_obat + ' kosong!</span>');
                            $('#jumlah_beli').val(1).attr('max', 1);
                            $('#add_item_to_cart_btn').prop('disabled', true);
                            alert('Stok ' + obat.nama_obat + ' kosong!');
                        }
                        else {
                            $('#selected_obat_info').html('Stok: <strong>' + obat.stok + '</strong> | Harga: <strong>Rp. ' + parseFloat(obat.harga).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '</strong>');
                            $('#jumlah_beli').attr('max', obat.stok); 
                            $('#jumlah_beli').focus().val(1); 
                            $('#add_item_to_cart_btn').prop('disabled', false); // Aktifkan tombol tambah

                            // --- Panggil fungsi playBeepSound di sini saat berhasil mendapatkan info obat ---
                            playBeepSound(); // Panggil fungsi pemutar suara
                        }
                    } else {
                        $('#selected_obat_info').text('Obat tidak ditemukan: ' + response.message);
                        $('#jumlah_beli').val(1).attr('max', 1);
                        $('#add_item_to_cart_btn').prop('disabled', true); 
                    }
                },
                error: function(xhr, status, error) {
                    $('#selected_obat_info').text('Terjadi kesalahan saat mencari obat. (Error: ' + xhr.status + ')');
                    $('#jumlah_beli').val(1).attr('max', 1);
                    $('#add_item_to_cart_btn').prop('disabled', true);
                    console.error('AJAX Error:', xhr.responseText);
                    if (xhr.status === 403 || (xhr.responseText && xhr.responseText.indexOf("The action you have requested is not allowed.") !== -1)) {
                        alert('Sesi Anda mungkin telah kedaluwarsa atau terjadi masalah keamanan (CSRF). Silakan refresh halaman dan coba lagi.');
                    } else {
                        alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                    }
                }
            });
        }

        // Event listener untuk input QR Code manual (memicu fetchObatInfoForCart)
        $('#qr_obat_data').on('change', function() {
            var content = $(this).val();
            if (content.length > 0) {
                if(scanner_transaksi) { scanner_transaksi.stop(); }
                $('#id_obat_manual').val('').trigger('change'); 
                fetchObatInfoForCart(content, null); // Kirim content asli, biarkan fetchObatInfoForCart memproses
            } else {
                $('#selected_obat_info').text('');
                $('#jumlah_beli').val(1).attr('max', 1);
                $('#add_item_to_cart_btn').prop('disabled', true);
            }
        });

        // Event listener untuk perubahan dropdown obat manual (memicu fetchObatInfoForCart)
        $('#id_obat_manual').on('change', function() {
            var selectedId = $(this).val();
            if (selectedId) {
                if(scanner_transaksi) { scanner_transaksi.stop(); }
                $('#qr_obat_data').val(''); 
                fetchObatInfoForCart(null, selectedId);
            } else {
                $('#selected_obat_info').text('');
                $('#jumlah_beli').val(1).attr('max', 1);
                $('#add_item_to_cart_btn').prop('disabled', true);
            }
        });

        // --- Logika Tambah Item ke Keranjang via AJAX ---
        $('#add_item_to_cart_btn').on('click', function() {
            var jumlah = parseInt($('#jumlah_beli').val());

            if (isNaN(jumlah) || jumlah <= 0) {
                alert('Jumlah beli harus angka positif.');
                return;
            }
            
            var qr_data_input = $('#qr_obat_data').val();
            var id_obat_dropdown = $('#id_obat_manual').val();

            // Tentukan data yang akan dikirim untuk mendapatkan ID obat
            var data_for_id_lookup = {};
            if (qr_data_input) {
                // Jika dari QR, proses dulu URL-nya
                if (qr_data_input.indexOf('<?php echo site_url('info_obat/'); ?>') === 0) {
                    data_for_id_lookup.qr_data = 'APOTEK_OBAT_' + qr_data_input.substring('<?php echo site_url('info_obat/'); ?>'.length);
                } else {
                    data_for_id_lookup.qr_data = qr_data_input; // Jika sudah format APOTEK_OBAT_UUID atau UUID langsung
                }
            } else if (id_obat_dropdown) {
                data_for_id_lookup.obat_id = id_obat_dropdown;
            } else {
                alert('Pilih obat atau scan QR terlebih dahulu.');
                return;
            }

            // Panggil AJAX untuk mendapatkan ID obat yang pasti
            $.ajax({
                url: '<?php echo site_url("kasir/get_obat_info_for_kasir_ajax"); ?>',
                type: 'POST',
                dataType: 'json',
                data: data_for_id_lookup,
                success: function(response) {
                    updateCsrfTokenInDom(response.csrf_hash); // Update CSRF
                    if (response.status == 'success') {
                        // Sekarang kita punya id_obat yang pasti dari backend
                        addItemToCartAjax(response.data.id_obat, jumlah);
                    } else {
                        alert('Error mendapatkan ID obat dari input: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat memproses data obat untuk penambahan: ' + error + ' (Status: ' + xhr.status + ')');
                    console.error('AJAX Error:', xhr.responseText);
                    if (xhr.status === 403 || (xhr.responseText && xhr.responseText.indexOf("The action you have requested is not allowed.") !== -1)) {
                        alert('Sesi Anda mungkin telah kedaluwarsa atau terjadi masalah keamanan (CSRF). Silakan refresh halaman dan coba lagi.');
                    } else {
                        alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                    }
                }
            });
        });

        function addItemToCartAjax(id_obat, jumlah) {
            // CSRF token akan otomatis ditambahkan oleh $.ajaxSetup()
            $.ajax({
                url: '<?php echo site_url("transaksi_penjualan/add_to_cart_ajax"); ?>',
                type: 'POST',
                dataType: 'json',
                data: { id_obat: id_obat, jumlah_beli: jumlah },
                success: function(response) {
                    updateCsrfTokenInDom(response.csrf_hash); // Update CSRF token di DOM
                    if (response.status == 'success') {
                        alert('Berhasil: ' + response.message);
                        updateCartDisplay(response.cart_items, response.total_belanja);
                        // Bersihkan input setelah berhasil tambah
                        $('#qr_obat_data').val('');
                        $('#id_obat_manual').val('').trigger('change');
                        $('#selected_obat_info').text('');
                        $('#jumlah_beli').val(1).attr('max', 1);
                        $('#add_item_to_cart_btn').prop('disabled', true);
                        startScannerTransaksi(); // Mulai kembali scanner
                    } else {
                        alert('Gagal: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menambahkan item ke keranjang. (Error: ' + xhr.status + ')');
                    console.error('AJAX Error:', xhr.responseText);
                    if (xhr.status === 403 || (xhr.responseText && xhr.responseText.indexOf("The action you have requested is not allowed.") !== -1)) {
                        alert('Sesi Anda mungkin telah kedaluwarsa atau terjadi masalah keamanan (CSRF). Silakan refresh halaman dan coba lagi.');
                    } else {
                        alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                    }
                }
            });
        }

        // Logika Hapus Item dari Keranjang via AJAX
        $('#cart_items_body').on('click', '.remove-item-from-cart', function() {
            var id_obat_to_remove = $(this).closest('tr').data('id_obat');
            if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                // CSRF token akan otomatis ditambahkan oleh $.ajaxSetup()
                $.ajax({
                    url: '<?php echo site_url("transaksi_penjualan/remove_item_from_cart_ajax"); ?>', 
                    type: 'POST',
                    dataType: 'json',
                    data: { id_obat: id_obat_to_remove },
                    success: function(response) {
                        updateCsrfTokenInDom(response.csrf_hash); // Update CSRF token di DOM
                        if (response.status == 'success') {
                            alert('Berhasil: ' + response.message);
                            updateCartDisplay(response.cart_items, response.total_belanja);
                        } else {
                            alert('Gagal menghapus item: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan saat menghapus item. (Error: ' + xhr.status + ')');
                        console.error('AJAX Error:', xhr.responseText);
                        if (xhr.status === 403 || (xhr.responseText && xhr.responseText.indexOf("The action you have requested is not allowed.") !== -1)) {
                            alert('Sesi Anda mungkin telah kedaluwarsa atau terjadi masalah keamanan (CSRF). Silakan refresh halaman dan coba lagi.');
                        } else {
                            alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                        }
                    }
                });
            }
        });

        // Fungsi untuk mengupdate tampilan keranjang
        function updateCartDisplay(cartItems, totalBelanja) {
            var html = '';
            if (cartItems && cartItems.length > 0) {
                $.each(cartItems, function(i, item) {
                    html += '<tr data-id_obat="' + item.id_obat + '">';
                    html += '<td>' + item.nama_obat + '</td>';
                    html += '<td>' + item.jumlah + '</td>';
                    html += '<td>Rp. ' + parseFloat(item.harga_satuan).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '</td>';
                    html += '<td>Rp. ' + parseFloat(item.subtotal).toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '</td>';
                    html += '<td><button type="button" class="btn btn-sm btn-danger remove-item-from-cart"><i class="ti ti-trash"></i> Hapus</button></td>';
                    html += '</tr>';
                });
            } else {
                html = '<tr id="empty_cart_row"><td colspan="5" class="text-center">Keranjang belanja kosong.</td></tr>';
            }
            $('#cart_items_body').html(html);
            $('#display_total_belanja').text('Rp. ' + parseFloat(totalBelanja).toLocaleString('id-ID', { minimumFractionDigits: 2 }));
            hitungKembalian(); // Hitung ulang kembalian setelah keranjang diupdate

            // Nonaktifkan tombol proses pembayaran jika keranjang kosong
            if (totalBelanja <= 0) {
                $('#btn_proses_bayar').prop('disabled', true);
            } else {
                $('#btn_proses_bayar').prop('disabled', false);
            }
        }
        
        // Panggil updateCartDisplay saat halaman dimuat untuk memastikan state UI sesuai sesi
        var initialCartItems = <?php echo json_encode($cart_items); ?>;
        var initialTotalBelanja = <?php echo $total_belanja; ?>;
        updateCartDisplay(initialCartItems, initialTotalBelanja);

        // Untuk kasus setelah validasi form pembayaran gagal, dan ada item di keranjang
        <?php if (isset($cart_items) && !empty($cart_items) && isset($total_belanja)): ?>
            $('#bayar_amount').val('<?php echo set_value('bayar_amount', ''); ?>');
            hitungKembalian(); 
        <?php endif; ?>

        // --- Logika Otomatis Cetak Struk Setelah Transaksi Berhasil ---
        <?php if ($this->session->flashdata('last_penjualan_id_for_print')): ?>
            var lastPenjualanId = '<?php echo $this->session->flashdata('last_penjualan_id_for_print'); ?>';
            // Buka jendela baru untuk mencetak struk
            window.open('<?php echo site_url('cetak_struk/'); ?>' + lastPenjualanId, '_blank');
            // Flashdata 'last_penjualan_id_for_print' akan otomatis dihapus setelah diakses sekali
        <?php endif; ?>

    });
</script>
</body>
</html>