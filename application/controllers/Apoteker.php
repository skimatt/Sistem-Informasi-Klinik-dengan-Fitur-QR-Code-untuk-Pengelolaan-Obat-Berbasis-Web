<?php
// File: application/controllers/Apoteker.php

defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options; // Opsional, jika Anda ingin mengatur Options secara terpisah

class Apoteker extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('auth_helper', 'form'));
        $this->load->library(array('form_validation', 'session', 'ciqrcode', 'ci_dompdf')); // Ciqrcode mungkin dibutuhkan untuk fitur Scan QR Obat

        // Memuat model yang relevan untuk Apoteker
        $this->load->model('Log_Aktivitas_model');
        $this->load->model('Obat_model');
        $this->load->model('Stok_Masuk_model');
        $this->load->model('Suplier_model'); // Untuk input stok masuk
        $this->load->model('Pengaturan_Sistem_model');

        // Pastikan pengguna sudah login
        is_logged_in();

        // Pastikan hanya user dengan role 'apoteker' atau 'admin' (jika admin juga bisa akses modul apoteker)
        // Kita akan batasi Apoteker controller hanya untuk Apoteker, kecuali ada alasan admin mengakses penuh modul ini
        redirect_unauthorized(array('apoteker', 'admin'), 'dashboard', 'Anda tidak memiliki izin akses ke modul Apoteker.');
    }

    // --- Private Helper Method untuk Memuat Data Umum View ---
    private function _load_common_data($title) {
        $data['title'] = $title;
        $data['user_nama'] = get_user_name();
        $data['user_role'] = get_user_role();
        return $data;
    }

    

    // --- Custom Form Validation Rule untuk Tanggal (kompatibel PHP 5) ---
    public function _valid_date($date) {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
            return checkdate($matches[2], $matches[3], $matches[1]);
        }
        $this->form_validation->set_message('_valid_date', 'Kolom {field} harus dalam formatFlatAppearance-MM-DD dan tanggal valid.');
        return FALSE;
    }

    /**
     * Dashboard Apoteker
     * Navigasi: Dashboard
     * Fungsi: Melihat total jenis obat, obat menipis, jumlah stok masuk hari ini, dan status kadaluarsa.
     * URL: /apoteker (jika di-route langsung ke sini), atau melalui /dashboard
     */
    public function index() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Dashboard Apoteker.');

        $data = $this->_load_common_data('Dashboard Apoteker');

        // Mengambil nilai threshold stok minimal dengan kompatibilitas PHP 5
        $min_stok_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('min_stok_threshold');
        $threshold_value = (isset($min_stok_setting->nilai_pengaturan) && is_numeric($min_stok_setting->nilai_pengaturan)) ? $min_stok_setting->nilai_pengaturan : 10;

        $data['total_jenis_obat'] = $this->Obat_model->count_all_jenis_obat(); // Perlu penambahan di Obat_model
        $data['obat_menipis'] = $this->Obat_model->get_low_stock_drugs($threshold_value);
        $data['stok_masuk_hari_ini'] = $this->Stok_Masuk_model->count_stok_masuk_today();
        $data['obat_kadaluarsa_mendekat'] = $this->Obat_model->get_expiring_drugs(30); // Obat kadaluarsa dalam 30 hari

        $this->load->view('apoteker/dashboard', $data); // Memuat view dashboard apoteker (full template)
    }

    /**
     * Data Obat (Lihat dan Edit, Tanpa Hapus)
     * Navigasi: Data Obat
     * URL: /data_obat (rute akan mengarah ke Apoteker/data_obat)
     */
    public function data_obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Data Obat (Apoteker).');

        $data = $this->_load_common_data('Data Obat');
        $data['obat'] = $this->Obat_model->get_all_obat();

        $this->load->view('apoteker/data_obat/list', $data); // Memuat view daftar obat untuk apoteker
    }

    /**
     * Form Edit Obat (Apoteker hanya bisa Edit, bukan Tambah baru)
     * URL: /data_obat/edit/:id
     */
    public function edit_obat($id_obat = NULL) {
        // Hanya apoteker yang bisa edit, dan tidak bisa tambah baru seperti admin
        if ($id_obat === NULL) {
            $this->session->set_flashdata('error', 'ID Obat tidak ditemukan untuk diedit.');
            redirect('data_obat');
        }

        $obat = $this->Obat_model->get_obat_by_id($id_obat);
        if (empty($obat)) {
            $this->session->set_flashdata('error', 'Obat tidak ditemukan.');
            redirect('data_obat');
        }

        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit obat ID: ' . $id_obat . ' (Apoteker)');

        $data = $this->_load_common_data('Edit Obat');
        $data['obat'] = $obat;

        // Aturan validasi untuk apoteker (mungkin lebih terbatas dari admin)
        $this->form_validation->set_rules('nama_obat', 'Nama Obat', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required|integer');
        $this->form_validation->set_rules('id_jenis', 'Jenis', 'required|integer');
        // Apoteker mungkin tidak bisa mengubah stok secara langsung dari form ini
        // Stok perubahan hanya melalui input stok masuk/scan QR
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('tanggal_kadaluarsa', 'Tanggal Kadaluarsa', 'required|callback__valid_date');

        if ($this->form_validation->run() == FALSE) {
            $data['kategori_obat'] = $this->Obat_model->get_all_kategori(); // Ambil data kategori
            $data['jenis_obat'] = $this->Obat_model->get_all_jenis();     // Ambil data jenis
            $this->load->view('apoteker/data_obat/form', $data); // View form edit obat apoteker
        } else {
            $obat_data = array(
                'nama_obat'          => $this->input->post('nama_obat', TRUE),
                'id_kategori'        => $this->input->post('id_kategori', TRUE),
                'id_jenis'           => $this->input->post('id_jenis', TRUE),
                'harga'              => $this->input->post('harga', TRUE),
                'tanggal_kadaluarsa' => $this->input->post('tanggal_kadaluarsa', TRUE)
            );

            if ($this->Obat_model->update_obat($id_obat, $obat_data)) {
                $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui obat ID: ' . $id_obat . ' (' . $obat_data['nama_obat'] . ') (Apoteker)');
                $this->session->set_flashdata('success', 'Data obat berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data obat.');
            }
            redirect('data_obat'); // Redirect ke daftar obat apoteker
        }
    }


    /**
     * Input Stok Masuk
     * Navigasi: Input Stok Masuk
     * Fungsi: Input kedatangan obat baru dari suplier.
     * URL: /input_stok_masuk
     */
    public function input_stok_masuk() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form Input Stok Masuk.');

        $data = $this->_load_common_data('Input Stok Masuk');

        $this->form_validation->set_rules('id_obat', 'Nama Obat', 'required|integer');
        $this->form_validation->set_rules('id_suplier', 'Suplier', 'required|integer');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal Masuk', 'required|callback__valid_date');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');


        if ($this->form_validation->run() == FALSE) {
            $data['obat_list'] = $this->Obat_model->get_all_obat(); // Daftar obat untuk dropdown
            $data['suplier_list'] = $this->Suplier_model->get_all_suplier(); // Daftar suplier untuk dropdown
            $this->load->view('apoteker/stok_masuk/form', $data); // View form input stok masuk
        } else {
            $stok_masuk_data = array(
                'id_obat'       => $this->input->post('id_obat', TRUE),
                'id_suplier'    => $this->input->post('id_suplier', TRUE),
                'jumlah'        => $this->input->post('jumlah', TRUE),
                'tanggal_masuk' => $this->input->post('tanggal_masuk', TRUE),
                'keterangan'    => $this->input->post('keterangan', TRUE)
            );

            // Transaksi database: Simpan histori stok masuk dan update stok obat
            $this->db->trans_begin(); // Mulai transaksi

            try {
                if (!$this->Stok_Masuk_model->create_stok_masuk($stok_masuk_data)) {
                    throw new Exception('Gagal menyimpan histori stok masuk.');
                }
                // Update stok di tabel obat
                if (!$this->Obat_model->update_stok($stok_masuk_data['id_obat'], $stok_masuk_data['jumlah'])) {
                    throw new Exception('Gagal memperbarui stok obat.');
                }

                $this->db->trans_commit(); // Commit transaksi jika berhasil
                $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah stok masuk: Obat ID ' . $stok_masuk_data['id_obat'] . ', Jumlah ' . $stok_masuk_data['jumlah']);
                $this->session->set_flashdata('success', 'Stok masuk berhasil ditambahkan dan stok obat diperbarui.');
            } catch (Exception $e) {
                $this->db->trans_rollback(); // Rollback jika ada error
                $this->session->set_flashdata('error', 'Transaksi gagal: ' . $e->getMessage());
            }
            redirect('input_stok_masuk');
        }
    }


    /**
     * Stok Obat (Apoteker)
     * Navigasi: Stok Obat
     * Fungsi: Melihat semua stok terkini, status menipis, dan filter berdasarkan kategori/jenis.
     * URL: /stok_obat_apoteker (untuk membedakan dengan rute admin stok_obat)
     * Keterangan: Karena Admin sudah punya /stok_obat, Apoteker bisa share rute yang sama atau rute berbeda.
     * Jika sama, maka sidebar menu Apoteker untuk Stok Obat akan mengarah ke /stok_obat.
     * Controller Admin::stok_obat() sudah memuat data yang relevan.
     * Untuk tujuan ini, kita akan arahkan Apoteker ke Admin::stok_obat(), dan di sidebar kita atur.
     */
    // Tidak perlu metode terpisah jika menggunakan Admin::stok_obat()
    // public function stok_obat() { ... }


    /**
     * Scan QR Obat
     * Navigasi: Scan QR Obat
     * Fungsi: Scan QR Code saat serah terima obat → stok otomatis berkurang (manual/otomatis).
     * URL: /scan_qr_obat
     */
    public function scan_qr_obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses fitur Scan QR Obat.');

        $data = $this->_load_common_data('Scan QR Obat');
        $data['obat_info'] = NULL; // Untuk menampilkan info obat setelah scan

        $this->form_validation->set_rules('qr_code_data', 'Data QR Code', 'required|trim');
        $this->form_validation->set_rules('jumlah_keluar', 'Jumlah Keluar', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('apoteker/scan_qr_obat/form', $data); // View form scan QR
        } else {
            $qr_data_raw = $this->input->post('qr_code_data', TRUE);
            $jumlah_keluar = $this->input->post('jumlah_keluar', TRUE);

            // Asumsi format QR data adalah "APOTEK_OBAT_UUID"
            if (strpos($qr_data_raw, 'APOTEK_OBAT_') === 0) {
                $uuid_obat = substr($qr_data_raw, strlen('APOTEK_OBAT_'));
                $obat = $this->Obat_model->get_obat_by_uuid($uuid_obat); // Perlu method get_obat_by_uuid di Obat_model

                if (!empty($obat)) {
                    if ($obat->stok >= $jumlah_keluar) {
                        // Lakukan pengurangan stok dan catat di log aktivitas/stok keluar
                        $this->db->trans_begin();
                        try {
                            if (!$this->Obat_model->update_stok($obat->id_obat, -$jumlah_keluar)) { // Kurangi stok
                                throw new Exception('Gagal mengurangi stok obat.');
                            }
                            // Catat di tabel stok_keluar (jika ada) atau log aktivitas
                            // $this->load->model('Stok_Keluar_model'); // Perlu model ini
                            // if (!$this->Stok_Keluar_model->create_stok_keluar($obat->id_obat, $jumlah_keluar, 'penjualan', 'Scan QR Code')) {
                            //     throw new Exception('Gagal mencatat stok keluar.');
                            // }
                            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengurangi stok obat via QR: ' . $obat->nama_obat . ' Sebanyak ' . $jumlah_keluar . ' unit.');

                            $this->db->trans_commit();
                            $this->session->set_flashdata('success', 'Stok obat berhasil dikurangi. Stok saat ini: ' . ($obat->stok - $jumlah_keluar));
                        } catch (Exception $e) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('error', 'Transaksi scan QR gagal: ' . $e->getMessage());
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Stok tidak mencukupi. Stok tersedia: ' . $obat->stok);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Obat tidak ditemukan dari QR Code.');
                }
            } else {
                $this->session->set_flashdata('error', 'Format QR Code tidak valid.');
            }
            redirect('scan_qr_obat');
        }
    }


    /**
     * Obat Kadaluarsa
     * Navigasi: Obat Kadaluarsa
     * Fungsi: Lihat daftar obat yang akan atau sudah kedaluwarsa.
     * URL: /obat_kadaluarsa
     */
    public function obat_kadaluarsa() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses daftar Obat Kadaluarsa.');

        $data = $this->_load_common_data('Daftar Obat Kadaluarsa');

        // Mengambil obat yang kadaluarsa atau akan kadaluarsa dalam 90 hari
        $data['obat_kadaluarsa'] = $this->Obat_model->get_expiring_drugs(90);

        $this->load->view('apoteker/obat_kadaluarsa/list', $data); // View daftar obat kadaluarsa
    }

    /**
     * Laporan Obat Masuk
     * Navigasi: Laporan Obat Masuk
     * Fungsi: Lihat & ekspor histori obat masuk dari suplier (berdasarkan tanggal atau nama obat).
     * URL: /laporan_obat_masuk
     */
    public function laporan_obat_masuk() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Laporan Obat Masuk.');

        $data = $this->_load_common_data('Laporan Obat Masuk');

        // Ambil filter dari GET request
        $start_date = $this->input->get('start_date', TRUE);
        $end_date = $this->input->get('end_date', TRUE);
        $nama_obat_filter = $this->input->get('nama_obat', TRUE); // Untuk filter nama obat

        // Perbarui model Stok_Masuk_model untuk bisa menerima filter nama obat
        $data['stok_masuk'] = $this->Stok_Masuk_model->get_all_stok_masuk($start_date, $end_date, $nama_obat_filter);
        $data['start_date_filter'] = $start_date;
        $data['end_date_filter'] = $end_date;
        $data['nama_obat_filter'] = $nama_obat_filter;

        $this->load->view('apoteker/laporan_obat_masuk/list', $data); // View laporan obat masuk
    }

    /**
     * Export Laporan Obat Masuk ke PDF
     * URL: /laporan_obat_masuk/export
     */
    public function export_laporan_obat_masuk_pdf() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengekspor Laporan Obat Masuk ke PDF.');

        $start_date = $this->input->post('start_date', TRUE);
        $end_date = $this->input->post('end_date', TRUE);
        $nama_obat_filter = $this->input->post('nama_obat', TRUE);

        $stok_masuk_data = $this->Stok_Masuk_model->get_all_stok_masuk($start_date, $end_date, $nama_obat_filter);

        $data['stok_masuk'] = $stok_masuk_data;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['nama_obat_filter'] = $nama_obat_filter;
        $data['title'] = 'Laporan Obat Masuk';

        $html = $this->load->view('apoteker/laporan_obat_masuk/report_pdf', $data, TRUE);

        $filename = 'Laporan_Obat_Masuk_';
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
     * Mengambil Info Obat berdasarkan QR Data via AJAX
     * Digunakan oleh fitur Scan QR Obat.
     * URL: /apoteker/get_obat_info_by_qr_ajax
     */
   /**
     * Mengambil Info Obat berdasarkan QR Data via AJAX
     * Digunakan oleh fitur Scan QR Obat (Apoteker).
     * URL: /apoteker/get_obat_info_by_qr_ajax
     */
    public function get_obat_info_by_qr_ajax() {
        if (!has_role(array('apoteker', 'admin'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized access.', 'csrf_hash' => $this->security->get_csrf_hash()));
            exit();
        }

        $qr_data_raw = $this->input->post('qr_data', TRUE);

        $obat = NULL;
        $uuid_to_search = NULL;

        if (!empty($qr_data_raw)) {
            // Coba identifikasi format QR data
            // 1. Jika formatnya URL publik yang kita generate
            if (strpos($qr_data_raw, site_url('info_obat/')) === 0) {
                $uuid_to_search = substr($qr_data_raw, strlen(site_url('info_obat/')));
            }
            // 2. Jika formatnya APOTEK_OBAT_UUID
            else if (strpos($qr_data_raw, 'APOTEK_OBAT_') === 0) {
                $uuid_to_search = substr($qr_data_raw, strlen('APOTEK_OBAT_'));
            }
            // 3. Jika formatnya hanya UUID murni (misal: disalin langsung)
            // Regex untuk UUID (v4)
            else if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qr_data_raw)) {
                $uuid_to_search = $qr_data_raw;
            }

            if ($uuid_to_search) {
                // Sekarang cari obat berdasarkan UUID yang telah diidentifikasi
                $obat = $this->Obat_model->get_obat_by_uuid($uuid_to_search);
            }
        }
        
        if (!empty($obat)) {
            echo json_encode(array(
                'status' => 'success',
                'data' => array(
                    'id_obat' => $obat->id_obat,
                    'nama_obat' => $obat->nama_obat,
                    'stok' => $obat->stok,
                    'harga' => $obat->harga,
                    'tanggal_kadaluarsa' => $obat->tanggal_kadaluarsa
                ),
                'csrf_hash' => $this->security->get_csrf_hash()
            ));
        } else {
            // Jika obat tidak ditemukan setelah semua upaya identifikasi
            echo json_encode(array('status' => 'error', 'message' => 'Format QR Code tidak valid atau obat tidak ditemukan. Pastikan QR Code benar.', 'csrf_hash' => $this->security->get_csrf_hash()));
        }
        exit();
    }

}