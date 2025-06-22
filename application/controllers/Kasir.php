<?php
// File: application/controllers/Kasir.php

defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
class Kasir extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('auth_helper', 'form'));
        $this->load->library(array('form_validation', 'session', 'ciqrcode', 'ci_dompdf')); // ci_dompdf untuk cetak struk

        // Memuat model yang relevan untuk Kasir
        $this->load->model('Log_Aktivitas_model');
        $this->load->model('Penjualan_model');
        $this->load->model('Obat_model'); // Untuk mencari obat saat transaksi
        $this->load->model('User_model'); // Untuk detail kasir di laporan
        $this->load->model('Pengaturan_Sistem_model'); // Untuk data klinik di struk

        // Pastikan pengguna sudah login
        is_logged_in();

        // Pastikan hanya user dengan role 'kasir' atau 'admin' yang bisa mengakses modul Kasir
        redirect_unauthorized(array('kasir', 'admin'), 'dashboard', 'Anda tidak memiliki izin akses ke modul Kasir.');
    }

    // --- Private Helper Method untuk Memuat Data Umum View ---
    private function _load_common_data($title) {
        $data['title'] = $title;
        $data['user_nama'] = get_user_name();
        $data['user_role'] = get_user_role();
        return $data;
    }

    /**
     * Dashboard Kasir
     * Navigasi: Dashboard
     * Fungsi: Menampilkan total transaksi hari ini, total pemasukan, dan shortcut ke transaksi baru.
     * URL: /kasir (jika di-route langsung ke sini), atau melalui /dashboard
     */
    public function index() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Dashboard Kasir.');

        $data = $this->_load_common_data('Dashboard Kasir');

        // Ambil data ringkasan untuk dashboard kasir
        $data['total_transaksi_hari_ini'] = $this->Penjualan_model->count_all_transactions_today();
        $data['total_pemasukan_hari_ini'] = $this->Penjualan_model->get_total_sales_today();

        $this->load->view('kasir/dashboard', $data); // Memuat view dashboard kasir (full template)
    }

    /**
     * Mengambil Detail Penjualan via AJAX
     * Digunakan oleh modal di laporan penjualan (Admin & Kasir).
     * URL: /laporan_penjualan/detail_ajax/:id
     */
    public function detail_ajax($id_penjualan = NULL) {
        // Otentikasi dan Otorisasi: Pastikan user sudah login
        // Dan hanya Admin atau Kasir yang boleh akses detail transaksi
        if (!is_logged_in() || !has_role(array('admin', 'kasir'))) { // Hanya admin dan kasir
            echo json_encode(array('status' => 'error', 'message' => 'Anda tidak memiliki izin akses untuk melihat detail transaksi.'));
            exit();
        }

        if ($id_penjualan === NULL) {
            echo json_encode(array('status' => 'error', 'message' => 'ID Penjualan tidak valid.'));
            exit();
        }

        $penjualan = $this->Penjualan_model->get_all_penjualan_by_id($id_penjualan); // Mengambil data penjualan utama
        $details = $this->Penjualan_model->get_detail_penjualan($id_penjualan);     // Mengambil detail item obat

        if (!empty($penjualan)) {
            // Pastikan data yang dikirim ke JS punya semua field yang dibutuhkan
            echo json_encode(array(
                'status' => 'success',
                'data' => array(
                    'uuid_penjualan' => $penjualan->uuid_penjualan,
                    'tgl_penjualan'  => $penjualan->tgl_penjualan,
                    'total_harga'    => $penjualan->total_harga,
                    'metode_bayar'   => $penjualan->metode_bayar,
                    'nama_kasir'     => isset($penjualan->nama_kasir_lengkap) ? $penjualan->nama_kasir_lengkap : $penjualan->nama_kasir // Prefer nama lengkap
                ),
                'details' => $details
                // Karena ini GET request, tidak perlu mengembalikan csrf_hash
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Data penjualan tidak ditemukan.'));
        }
        exit(); // Sangat penting untuk menghentikan eksekusi setelah mengirim JSON
    }

    /**
     * Transaksi Penjualan
     * Navigasi: Transaksi Penjualan
     * Fungsi: Halaman untuk melakukan transaksi: scan QR → isi jumlah → total → input pembayaran → simpan transaksi.
     * URL: /transaksi_penjualan
     */
    public function transaksi_penjualan() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses halaman Transaksi Penjualan.');

        $data = $this->_load_common_data('Transaksi Penjualan Baru');
        $data['cart_items'] = $this->session->userdata('cart_items') ? $this->session->userdata('cart_items') : array();
        $data['total_belanja'] = $this->session->userdata('total_belanja') ? $this->session->userdata('total_belanja') : 0;

        // Validasi untuk proses pembayaran
        $this->form_validation->set_rules('metode_bayar', 'Metode Pembayaran', 'required|in_list[tunai,non-tunai]');
        $this->form_validation->set_rules('bayar_amount', 'Jumlah Bayar', 'required|numeric|greater_than_equal_to[0]');

        if ($this->input->post('process_payment')) { // Tombol "Proses Pembayaran"
            $cart_items = $this->session->userdata('cart_items');
            $total_belanja = $this->session->userdata('total_belanja');

            if (empty($cart_items)) {
                $this->session->set_flashdata('error', 'Keranjang belanja kosong. Silakan tambahkan obat terlebih dahulu.');
                redirect('transaksi_penjualan');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('form_error', validation_errors());
            } else {
                $metode_bayar = $this->input->post('metode_bayar', TRUE);
                $bayar_amount = $this->input->post('bayar_amount', TRUE);

                if ($bayar_amount < $total_belanja) {
                    $this->session->set_flashdata('error', 'Jumlah bayar kurang dari total belanja. Kurang Rp. ' . number_format($total_belanja - $bayar_amount, 2, ',', '.'));
                } else {
                    $this->db->trans_begin(); // Mulai transaksi database

                    try {
                        // 1. Catat penjualan
                        $penjualan_data = array(
                            'uuid_penjualan' => $this->db->query('SELECT UUID() AS uuid')->row()->uuid, // Generate UUID
                            'id_user'        => get_user_id(),
                            'total_harga'    => $total_belanja,
                            'metode_bayar'   => $metode_bayar,
                            'tgl_penjualan'  => date('Y-m-d H:i:s')
                        );
                        if (!$this->Penjualan_model->create_penjualan($penjualan_data)) {
                            throw new Exception('Gagal menyimpan data penjualan.');
                        }
                        $id_penjualan = $this->db->insert_id(); // ID penjualan yang baru

                        // 2. Catat detail penjualan dan kurangi stok
                        foreach ($cart_items as $item) {
                            $detail_data = array(
                                'id_penjualan'  => $id_penjualan,
                                'id_obat'       => $item['id_obat'],
                                'jumlah'        => $item['jumlah'],
                                'harga_satuan'  => $item['harga_satuan'],
                                'subtotal'      => $item['subtotal']
                            );
                            if (!$this->Penjualan_model->create_detail_penjualan($detail_data)) {
                                throw new Exception('Gagal menyimpan detail penjualan untuk obat: ' . $item['nama_obat']);
                            }
                            // Kurangi stok obat
                            if (!$this->Obat_model->update_stok($item['id_obat'], -$item['jumlah'])) {
                                throw new Exception('Gagal mengurangi stok untuk obat: ' . $item['nama_obat']);
                            }
                            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Penjualan: mengurangi stok obat ' . $item['nama_obat'] . ' sebanyak ' . $item['jumlah'] . ' unit.');
                        }

                        $this->db->trans_commit();
                        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Transaksi penjualan berhasil dengan ID: ' . $id_penjualan . ' Total: ' . $total_belanja);
                        $this->session->set_flashdata('success', 'Transaksi berhasil! Kembalian: Rp. ' . number_format($bayar_amount - $total_belanja, 2, ',', '.') . '.');
                        
                        // Simpan ID penjualan untuk TOMBOL "Cetak Struk Terakhir" (bertahan beberapa request)
                        $this->session->set_flashdata('last_penjualan_id', $id_penjualan);
                        // Simpan ID penjualan untuk OTOMATIS CETAK (hanya untuk request berikutnya, lalu hilang)
                        $this->session->set_tempdata('last_penjualan_id_for_print', $id_penjualan, 300); // Bertahan 5 menit atau sampai diakses

                        // Bersihkan keranjang
                        $this->session->unset_userdata('cart_items');
                        $this->session->unset_userdata('total_belanja');

                    } catch (Exception $e) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('error', 'Transaksi gagal: ' . $e->getMessage());
                    }
                }
            }
            redirect('transaksi_penjualan');
        }

        // Ambil daftar obat untuk fitur dropdown manual
        $data['obat_list_dropdown'] = $this->Obat_model->get_all_obat();
        $this->load->view('kasir/transaksi_penjualan/form', $data);
    }

    // AJAX untuk mendapatkan info obat dari QR
    /**
     * AJAX: Mendapatkan Info Obat untuk Keranjang (dari QR atau ID)
     * URL: /kasir/get_obat_info_for_kasir_ajax
     */
    public function get_obat_info_for_kasir_ajax() {
        if (!has_role(array('kasir', 'admin'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized access.', 'csrf_hash' => $this->security->get_csrf_hash()));
            exit();
        }

        $qr_data_raw = $this->input->post('qr_data', TRUE);
        $obat_id_manual = $this->input->post('obat_id', TRUE);

        $obat = NULL;
        // Coba proses qr_data_raw
        if (!empty($qr_data_raw)) {
            $uuid_to_search = NULL;
            // Jika formatnya APOTEK_OBAT_UUID
            if (strpos($qr_data_raw, 'APOTEK_OBAT_') === 0) {
                $uuid_to_search = substr($qr_data_raw, strlen('APOTEK_OBAT_'));
            }
            // Jika formatnya hanya UUID (langsung dari URL publik)
            else if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qr_data_raw)) {
                $uuid_to_search = $qr_data_raw;
            }

            if ($uuid_to_search) {
                $obat = $this->Obat_model->get_obat_by_uuid($uuid_to_search);
            }
        }
        // Jika tidak ditemukan dari QR atau QR tidak valid, coba dari ID manual
        if (empty($obat) && !empty($obat_id_manual)) {
            $obat = $this->Obat_model->get_obat_by_id($obat_id_manual);
        }

        if (!empty($obat)) {
            echo json_encode(array(
                'status' => 'success',
                'data' => array(
                    'id_obat' => $obat->id_obat,
                    'nama_obat' => $obat->nama_obat,
                    'stok' => $obat->stok,
                    'harga' => $obat->harga,
                    'tanggal_kadaluarsa' => $obat->tanggal_kadaluarsa,
                    'is_expired' => ($obat->tanggal_kadaluarsa < date('Y-m-d')) ? TRUE : FALSE
                ),
                'csrf_hash' => $this->security->get_csrf_hash()
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Obat tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()));
        }
        exit();
    }


    // Helper untuk menghitung total keranjang (ini harusnya di Penjualan_model atau di helper jika sering dipakai)
    private function _calculate_cart_total($cart_items) {
        $total = 0;
        if (!empty($cart_items)) {
            foreach ($cart_items as $item) {
                $total += $item['subtotal'];
            }
        }
        return $total;
    }

    

    /**
     * Riwayat Transaksi (Kasir)
     * Navigasi: Riwayat Transaksi
     * Fungsi: Menampilkan daftar transaksi yang pernah dilakukan oleh kasir tersebut.
     * URL: /riwayat_transaksi
     */
    public function riwayat_transaksi() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Riwayat Transaksi (Kasir).');

        $data = $this->_load_common_data('Riwayat Transaksi');

        $id_kasir = get_user_id();
        $data['transaksi'] = $this->Penjualan_model->get_all_penjualan_by_user($id_kasir); // Perlu method baru di Penjualan_model

        $this->load->view('kasir/riwayat_transaksi/list', $data); // View riwayat transaksi
    }

    /**
     * Cetak Struk
     * Fungsi: Cetak ulang struk penjualan berdasarkan ID transaksi.
     * URL: /cetak_struk/:id
     */
    public function cetak_struk($id_penjualan = NULL) {
        if ($id_penjualan === NULL) {
            $this->session->set_flashdata('error', 'ID Transaksi tidak ditemukan.');
            redirect('riwayat_transaksi');
        }

        $penjualan = $this->Penjualan_model->get_all_penjualan_by_id($id_penjualan);
        $details = $this->Penjualan_model->get_detail_penjualan($id_penjualan);

        if (empty($penjualan) || empty($details)) {
            $this->session->set_flashdata('error', 'Data transaksi tidak ditemukan.');
            redirect('riwayat_transaksi');
        }

        // Pastikan kasir yang login adalah yang melakukan transaksi, atau admin
        if (!is_admin() && $penjualan->id_user != get_user_id()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk mencetak struk transaksi ini.');
            redirect('riwayat_transaksi');
        }

        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mencetak struk untuk Transaksi ID: ' . $id_penjualan);

        $data['penjualan'] = $penjualan;
        $data['details'] = $details;
        $data['pengaturan_klinik'] = $this->Pengaturan_Sistem_model->get_pengaturan_array(); // Ambil pengaturan klinik

        // Load view HTML untuk struk (desain sederhana untuk printer thermal)
        $html = $this->load->view('kasir/cetak_struk/struk_pdf', $data, TRUE);

        $filename = 'Struk_Penjualan_' . $penjualan->uuid_penjualan . '.pdf';

        $options_array = array(
            'isRemoteEnabled' => TRUE,
            'defaultFont' => 'Courier', // Font monospace cocok untuk struk
            'isHtml5ParserEnabled' => TRUE, // Penting untuk HTML5
            'isCssFloatEnabled' => TRUE, // Penting untuk layout sederhana
            // 'paperSize' => array(0,0,226.77,566.93), // Custom paper size untuk printer thermal (58mm width, 150mm height approx)
            // Coba ukuran ini dulu, jika tidak cocok, bisa sesuaikan
            // width = 58mm = 58/25.4 * 72 = 164.4 pt
            // height bisa auto atau cukup besar (e.g. 200mm = 566.93 pt)
            'paperSize' => 'custom',
            'paperWidth' => '58mm', // Lebar kertas thermal
            'paperHeight' => 'auto', // Tinggi menyesuaikan konten
            'dpi' => 96 // Default DPI, sesuaikan jika PDF terlihat blur/kecil
        );

        $this->ci_dompdf->generate_pdf($html, $filename, TRUE, $options_array);
        exit();
    }

    /**
     * Laporan Penjualan (Kasir)
     * Navigasi: Laporan Penjualan (Optional)
     * Fungsi: Melihat dan mengekspor transaksi pribadi per hari/bulan.
     * URL: /laporan_penjualan_kasir (membedakan dari laporan penjualan Admin)
     * Keterangan: Jika kasir hanya bisa melihat laporan penjualan pribadinya.
     * Jika kasir melihat semua laporan penjualan, ini bisa diarahkan ke Admin::laporan_penjualan(),
     * tapi di sidebar kita akan batasi Kasir hanya melihat transaksi yang dia lakukan.
     */
    public function laporan_penjualan_kasir() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Laporan Penjualan Pribadi (Kasir).');

        $data = $this->_load_common_data('Laporan Penjualan Pribadi');

        $id_kasir = get_user_id();
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);

        $data['penjualan'] = $this->Penjualan_model->get_all_penjualan_by_user($id_kasir, $start_date, $end_date);
        $data['start_date_filter'] = $start_date;
        $data['end_date_filter'] = $end_date;

        $this->load->view('kasir/laporan_penjualan/list', $data);
    }

    /**
     * Export Laporan Penjualan Kasir ke PDF
     * URL: /laporan_penjualan_kasir/export
     */
    public function export_laporan_penjualan_kasir_pdf() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengekspor Laporan Penjualan Pribadi ke PDF.');

        $id_kasir = get_user_id();
        $start_date = $this->input->post('start_date', TRUE);
        $end_date = $this->input->post('end_date', TRUE);

        $penjualan_data = $this->Penjualan_model->get_all_penjualan_by_user($id_kasir, $start_date, $end_date);

        $data['penjualan'] = $penjualan_data;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['title'] = 'Laporan Penjualan Pribadi';

        $html = $this->load->view('kasir/laporan_penjualan/report_pdf', $data, TRUE);

        $filename = 'Laporan_Penjualan_Pribadi_';
        if (!empty($start_date) && !empty($end_date)) {
            $filename .= $start_date . '_to_' . $end_date;
        } else {
            $filename .= date('Ymd');
        }
        $filename .= '.pdf';

        $options_array = array(
            'isRemoteEnabled' => TRUE,
            'defaultFont' => 'Helvetica',
        );

        $this->ci_dompdf->generate_pdf($html, $filename, TRUE, $options_array);
        exit();
    }

    /**
     * Hapus Item dari Keranjang via AJAX
     * Digunakan di halaman Transaksi Penjualan.
     * URL: /transaksi_penjualan/remove_item_from_cart_ajax
     */
    public function remove_item_from_cart_ajax() {
        if (!has_role(array('kasir', 'admin'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized access.'));
            exit();
        }

        $id_obat_to_remove = $this->input->post('id_obat', TRUE);

        if (empty($id_obat_to_remove)) {
            echo json_encode(array('status' => 'error', 'message' => 'ID Obat tidak valid.'));
            exit();
        }

        $cart_items = $this->session->userdata('cart_items');
        if (empty($cart_items)) {
            echo json_encode(array('status' => 'error', 'message' => 'Keranjang kosong.'));
            exit();
        }

        $new_cart_items = array();
        $item_removed = FALSE;
        foreach ($cart_items as $item) {
            if ($item['id_obat'] != $id_obat_to_remove) {
                $new_cart_items[] = $item;
            } else {
                $item_removed = TRUE;
            }
        }

        if ($item_removed) {
            $this->session->set_userdata('cart_items', $new_cart_items);
            $total_belanja = $this->_calculate_cart_total($new_cart_items);
            $this->session->set_userdata('total_belanja', $total_belanja);
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus item dari keranjang: Obat ID ' . $id_obat_to_remove);
            echo json_encode(array('status' => 'success', 'message' => 'Item berhasil dihapus.', 'cart_items' => $new_cart_items, 'total_belanja' => $total_belanja));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Item tidak ditemukan di keranjang.'));
        }
        exit();
    }

    /**
     * AJAX: Tambah Item ke Keranjang Sesi
     * URL: /transaksi_penjualan/add_to_cart_ajax
     */
    public function add_to_cart_ajax() {
        if (!has_role(array('kasir', 'admin'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized access.'));
            exit();
        }

        $id_obat = $this->input->post('id_obat', TRUE);
        $jumlah_beli = $this->input->post('jumlah_beli', TRUE);

        if (empty($id_obat) || !is_numeric($jumlah_beli) || $jumlah_beli <= 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Data tidak valid.'));
            exit();
        }

        $obat = $this->Obat_model->get_obat_by_id($id_obat);
        if (empty($obat)) {
            echo json_encode(array('status' => 'error', 'message' => 'Obat tidak ditemukan.'));
            exit();
        }

        if ($obat->stok < $jumlah_beli) {
            echo json_encode(array('status' => 'error', 'message' => 'Stok ' . $obat->nama_obat . ' tidak mencukupi. Tersedia: ' . $obat->stok . '.'));
            exit();
        }

        if ($obat->tanggal_kadaluarsa < date('Y-m-d')) {
             echo json_encode(array('status' => 'error', 'message' => 'Obat ' . $obat->nama_obat . ' sudah kadaluarsa dan tidak dapat dijual.'));
             exit();
        }

        $cart_items = $this->session->userdata('cart_items') ? $this->session->userdata('cart_items') : array();
        $item_found = FALSE;
        foreach ($cart_items as &$item) {
            if ($item['id_obat'] == $obat->id_obat) {
                // Pastikan penambahan tidak melebihi stok yang tersedia
                if (($item['jumlah'] + $jumlah_beli) > $obat->stok) {
                    echo json_encode(array('status' => 'error', 'message' => 'Penambahan melebihi stok tersedia. Stok saat ini: ' . $obat->stok . ', sudah ada di keranjang: ' . $item['jumlah'] . '.'));
                    exit();
                }
                $item['jumlah'] += $jumlah_beli;
                $item['subtotal'] = $item['jumlah'] * $item['harga_satuan'];
                $item_found = TRUE;
                break;
            }
        }
        if (!$item_found) {
            $cart_items[] = array(
                'id_obat'       => $obat->id_obat,
                'nama_obat'     => $obat->nama_obat,
                'harga_satuan'  => $obat->harga,
                'jumlah'        => $jumlah_beli,
                'subtotal'      => $obat->harga * $jumlah_beli,
                'stok_tersedia' => $obat->stok // Simpan stok saat ini untuk validasi
            );
        }

        $this->session->set_userdata('cart_items', $cart_items);
        $total_belanja = $this->_calculate_cart_total($cart_items);
        $this->session->set_userdata('total_belanja', $total_belanja);

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan.',
            'cart_items' => $cart_items,
            'total_belanja' => $total_belanja,
            'csrf_hash' => $this->security->get_csrf_hash() // <--- Pastikan ini ada di setiap respons AJAX POST
        ));
        exit();
    }
    
}