<?php
// File: application/views/apoteker/scan_qr_obat/form.php

// Pastikan variabel $title, $user_nama, $user_role, dan $obat_info (setelah scan) sudah dilewatkan dari controller Apoteker::scan_qr_obat()
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
    <style>
        #preview {
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
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?php echo validation_errors(); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <h5 class="card-header"><?php echo html_escape($title); ?></h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Scan QR Code:</h6>
                                        <div class="mb-3">
                                            <label for="qr_code_data" class="form-label">Input QR Code (atau scan):</label>
                                            <input type="text" class="form-control" id="qr_code_data" name="qr_code_data" placeholder="Scan QR Code di sini..." autofocus>
                                            <small class="text-muted">Masukkan data QR code secara manual atau gunakan scanner.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <video id="preview" autoplay="true" muted="true" style="width: 100%; max-height: 240px; border: 1px solid #ddd;"></video>
                                            <small class="text-muted">Pastikan kamera aktif dan mengarah ke QR Code.</small>
                                            <div class="mt-2">
                                                <button type="button" id="startScannerBtn" class="btn btn-sm btn-info me-2">Mulai Scan Kamera</button>
                                                <button type="button" id="stopScannerBtn" class="btn btn-sm btn-outline-info">Stop Scan Kamera</button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <h6>Informasi Obat:</h6>
                                        <div class="mb-3">
                                            <label for="nama_obat_scanned" class="form-label">Nama Obat:</label>
                                            <input type="text" class="form-control" id="nama_obat_scanned" readonly value="<?php echo html_escape(isset($obat_info->nama_obat) ? $obat_info->nama_obat : ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="stok_saat_ini" class="form-label">Stok Saat Ini:</label>
                                            <input type="text" class="form-control" id="stok_saat_ini" readonly value="<?php echo html_escape(isset($obat_info->stok) ? $obat_info->stok : ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal_kadaluarsa_scanned" class="form-label">Tanggal Kadaluarsa:</label>
                                            <input type="text" class="form-control" id="tanggal_kadaluarsa_scanned" readonly value="<?php echo html_escape(isset($obat_info->tanggal_kadaluarsa) ? $obat_info->tanggal_kadaluarsa : ''); ?>">
                                        </div>
                                        
                                        <?php echo form_open(site_url('scan_qr_obat')); ?>
                                        <input type="hidden" id="hidden_qr_data" name="qr_code_data" value="<?php echo set_value('qr_code_data'); ?>">
                                        <div class="mb-3">
                                            <label for="jumlah_keluar" class="form-label">Jumlah Obat Keluar:</label>
                                            <input type="number" class="form-control" id="jumlah_keluar" name="jumlah_keluar" value="<?php echo set_value('jumlah_keluar'); ?>" required min="1">
                                        </div>
                                        <button type="submit" class="btn btn-danger me-2">Kurangi Stok</button>
                                        <a href="<?php echo site_url('scan_qr_obat'); ?>" class="btn btn-secondary">Reset</a>
                                        <?php echo form_close(); ?>
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

    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/vendor/instascan.min.js'); ?>"></script>


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

                        if (typeof data === 'string') {
                            settings.data += '&' + token.name + '=' + token.hash;
                        } else if (typeof data === 'object' && data !== null) {
                            data[token.name] = token.hash;
                        }
                        settings.data = data;
                    }
                }
            });

            // Update token CSRF setelah SETIAP AJAX request yang berhasil (dari respons JSON)
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

            // --- Logika Scanner QR Code ---
            var scanner = null; // Inisialisasi scanner global
            var beepSound = document.getElementById('scannerBeepSound'); // Ambil elemen audio

            // --- Fungsi untuk memutar suara beep (lebih robust) ---
            function playBeepSound() {
                if (beepSound) {
                    beepSound.volume = 0.5; // Contoh: 50% volume
                    beepSound.currentTime = 0; // Pastikan mulai dari awal
                    var playPromise = beepSound.play();

                    if (playPromise !== undefined) {
                        playPromise.then(function() {
                            console.log("Audio played successfully.");
                        }).catch(function(error) {
                            console.warn("Failed to play audio: ", error);
                        });
                    }
                } else {
                    console.error("Audio element #scannerBeepSound not found.");
                }
            }
            
            // --- Fungsi untuk mengaktifkan audio setelah interaksi pertama ---
            function enableAudioOnFirstInteraction() {
                // Coba putar suara senyap atau suara beep singkat
                if (beepSound) {
                    beepSound.volume = 0;
                    var playPromise = beepSound.play();

                    if (playPromise !== undefined) {
                        playPromise.then(function() {
                            // Autoplay diizinkan
                            beepSound.volume = 0.5; // Kembalikan volume normal
                            console.log("Audio permission granted and enabled.");
                        }).catch(function(error) {
                            console.warn("Audio autoplay blocked, will try again on next user interaction: ", error);
                        });
                    }
                }
            }

            // Pemicu umum untuk mengaktifkan audio (misal klik di mana saja)
            $(document).one('click keydown', function() {
                enableAudioOnFirstInteraction();
            });


            // Fungsi untuk memulai scanner kamera
            function startScanner() {
                if (scanner) {
                    scanner.stop();
                    scanner = null; // Reset scanner
                }

                Instascan.Camera.getCameras().then(function (cameras) {
                    console.log('--- Scan QR Obat Apoteker: Daftar Kamera Ditemukan ---', cameras);

                    if (cameras.length > 0) {
                        var selectedCamera = null;
                        if (cameras[1] && cameras[1].name.toLowerCase().indexOf('back') !== -1) {
                            selectedCamera = cameras[1];
                            console.log('Scan QR Obat Apoteker: Menggunakan kamera belakang.');
                        } else if (cameras[0]) {
                            selectedCamera = cameras[0];
                            console.log('Scan QR Obat Apoteker: Menggunakan kamera utama (indeks 0).');
                        } else {
                            console.error('Scan QR Obat Apoteker: Tidak ada kamera yang cocok ditemukan.');
                            alert('Tidak ada kamera yang cocok ditemukan. Pastikan kamera terhubung.');
                            return; // Hentikan eksekusi jika tidak ada kamera
                        }
                        
                        scanner = new Instascan.Scanner({
                            video: document.getElementById('preview'),
                            scanPeriod: 5, // Scan lebih cepat
                            mirror: false // Set false jika Anda ingin tampilan kamera tidak terbalik
                        });

                        scanner.addListener('scan', function (content) {
                            $('#qr_code_data').val(content);
                            $('#hidden_qr_data').val(content); // Isi hidden input untuk form submission

                            // --- Panggil fungsi untuk mendapatkan info obat ---
                            fetchObatInfo(content); // Ini akan memanggil AJAX
                        });

                        scanner.start(selectedCamera)
                            .then(function() {
                                console.log('Scan QR Obat Apoteker: Kamera berhasil dimulai.');
                                $('#qr_code_data').focus(); // Fokuskan ke input QR untuk input manual/scanner
                            })
                            .catch(function (e) {
                                console.error('Scan QR Obat Apoteker: Error saat memulai kamera.', e);
                                alert('Gagal memulai kamera. Pastikan kamera tidak digunakan oleh aplikasi lain dan Anda telah memberikan izin. Error: ' + e.name + ' - ' + e.message);
                            });

                    } else {
                        console.error('Scan QR Obat Apoteker: Tidak ada kamera ditemukan.');
                        alert('Tidak ada kamera yang terdeteksi di perangkat Anda.');
                    }
                }).catch(function (e) {
                    console.error('Scan QR Obat Apoteker: Error saat mendapatkan daftar kamera.', e);
                    alert('Error saat mengakses kamera: ' + e.name + ' - ' + e.message + '. Pastikan browser Anda diakses via HTTPS.');
                });
            }

            // Fungsi untuk menghentikan scanner kamera
            function stopScanner() {
                if (scanner) {
                    scanner.stop();
                    scanner = null;
                    console.log('Scan QR Obat Apoteker: Kamera dihentikan.');
                }
                // Kosongkan input dan info obat saat stop
                $('#qr_code_data').val('');
                $('#hidden_qr_data').val('');
                $('#nama_obat_scanned').val('');
                $('#stok_saat_ini').val('');
                $('#tanggal_kadaluarsa_scanned').val('');
            }

            // Fungsi untuk memanggil AJAX dan mendapatkan info obat
            function fetchObatInfo(qrContentRaw) {
                var processedQrData = null;

                // Jika qrContentRaw adalah URL, ekstrak UUID-nya
                if (qrContentRaw && qrContentRaw.indexOf('<?php echo site_url('info_obat/'); ?>') === 0) {
                    processedQrData = 'APOTEK_OBAT_' + qrContentRaw.substring('<?php echo site_url('info_obat/'); ?>'.length);
                } else if (qrContentRaw) {
                    processedQrData = qrContentRaw;
                }

                $.ajax({
                    url: '<?php echo site_url("apoteker/get_obat_info_by_qr_ajax"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: { qr_data: processedQrData }, // Mengirim data yang sudah diproses
                    success: function(response) {
                        updateCsrfTokenInDom(response.csrf_hash); // Update CSRF token
                        
                        if (response.status == 'success') {
                            var obat = response.data;
                            $('#nama_obat_scanned').val(obat.nama_obat);
                            $('#stok_saat_ini').val(obat.stok);
                            $('#tanggal_kadaluarsa_scanned').val(obat.tanggal_kadaluarsa);
                            // Fokuskan ke input jumlah_keluar setelah info obat didapat
                            $('#jumlah_keluar').focus();
                            playBeepSound(); // <<< Mainkan suara beep di sini saat berhasil
                        } else {
                            $('#nama_obat_scanned').val('Tidak Ditemukan');
                            $('#stok_saat_ini').val('');
                            $('#tanggal_kadaluarsa_scanned').val('');
                            console.warn('Obat tidak ditemukan:', response.message);
                            alert('Obat tidak ditemukan: ' + response.message); // Tetap munculkan alert untuk Apoteker
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#nama_obat_scanned').val('Error');
                        $('#stok_saat_ini').val('');
                        $('#tanggal_kadaluarsa_scanned').val('');
                        alert('Terjadi kesalahan saat mencari obat: ' + error + ' (Status: ' + xhr.status + ')');
                        console.error('AJAX Error:', xhr.responseText);
                        if (xhr.status === 403 || (xhr.responseText && xhr.responseText.indexOf("The action you have requested is not allowed.") !== -1)) {
                            alert('Sesi Anda mungkin telah kedaluwarsa atau terjadi masalah keamanan (CSRF). Silakan refresh halaman dan coba lagi.');
                        } else {
                            alert('Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.');
                        }
                    }
                });
            }

            // --- Event Listeners ---

            // Mulai scanner secara otomatis saat halaman dimuat
            startScanner();

            // Tombol untuk memulai/menghentikan scanner secara manual (opsional, jika Anda ingin mempertahankan tombol)
            $('#startScannerBtn').on('click', startScanner);
            $('#stopScannerBtn').on('click', stopScanner);

            // Event listener untuk input manual QR Code (jika user mengetik atau menempelkan)
            $('#qr_code_data').on('change', function() {
                var content = $(this).val();
                $('#hidden_qr_data').val(content); // Isi hidden input untuk form submission

                if (content.length > 0) {
                    fetchObatInfo(content); // Panggil fungsi utama
                } else {
                    // Reset info obat jika input dikosongkan
                    $('#nama_obat_scanned').val('');
                    $('#stok_saat_ini').val('');
                    $('#tanggal_kadaluarsa_scanned').val('');
                }
            });

            // Jika ada data QR dari validasi gagal (postback), tampilkan info obatnya
            <?php if (isset($obat_info) && !empty($obat_info)): ?>
                // Set value for the input field to maintain state after validation error
                $('#qr_code_data').val('<?php echo html_escape(set_value("qr_code_data", "")); ?>');
                $('#hidden_qr_data').val('<?php echo html_escape(set_value("qr_code_data", "")); ?>');

                $('#nama_obat_scanned').val('<?php echo html_escape($obat_info->nama_obat); ?>');
                $('#stok_saat_ini').val('<?php echo html_escape($obat_info->stok); ?>');
                $('#tanggal_kadaluarsa_scanned').val('<?php echo html_escape($obat_info->tanggal_kadaluarsa); ?>');
            <?php endif; ?>
        });
    </script>