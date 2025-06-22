<?php
// File: application/controllers/Admin.php

defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options; // Opsional, jika Anda ingin mengatur Options secara terpisah


class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('auth_helper', 'form')); // Memuat auth_helper dan form_helper
        $this->load->library(array('form_validation', 'session', 'ciqrcode', 'ci_dompdf')); // <<< Tambahkan 'ci_dompdf' di sini

        // Memuat semua model yang dibutuhkan oleh Admin
        $this->load->model('Log_Aktivitas_model');
        $this->load->model('User_model');
        $this->load->model('Obat_model');
        $this->load->model('Kategori_Obat_model');
        $this->load->model('Jenis_Obat_model');
        $this->load->model('Stok_Masuk_model');
        $this->load->model('Suplier_model');
        $this->load->model('Penjualan_model');
        $this->load->model('Pengaturan_Sistem_model');

        // Pastikan pengguna sudah login
        // Fungsi is_logged_in() sudah otomatis redirect jika belum login
        is_logged_in();

        // Pastikan hanya user dengan role 'admin' yang bisa mengakses seluruh controller ini
        // Fungsi redirect_unauthorized() akan menangani pengalihan dan pesan flashdata
        redirect_unauthorized('admin', 'dashboard', 'Anda tidak memiliki izin akses ke modul Administrasi.');
    }

    // --- Private Helper Method untuk Memuat Data Umum View ---
    // Metode ini akan membantu mengisi data dasar seperti title, user_nama, user_role
    private function _load_common_data($title) {
        $data['title'] = $title;
        $data['user_nama'] = get_user_name(); // Dari auth_helper
        $data['user_role'] = get_user_role(); // Dari auth_helper
        // Anda bisa menambahkan data umum lainnya di sini jika diperlukan di setiap halaman
        return $data;
    }

    // --- Custom Form Validation Rule untuk Tanggal (kompatibel PHP 5) ---
    public function _valid_date($date) {
        // Format YYYY-MM-DD
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
            // checkdate(month, day, year)
            return checkdate($matches[2], $matches[3], $matches[1]);
        }
        $this->form_validation->set_message('_valid_date', 'Kolom {field} harus dalam format YYYY-MM-DD dan tanggal valid.');
        return FALSE;
    }

    // --- Custom Callback untuk Unique Username (kompatibel PHP 5) ---
    public function _unique_username($username, $id_user_str) {
        $id_user_arr = explode('-', $id_user_str); // Mengurai string menjadi array [id, current_id]
        $current_id = count($id_user_arr) > 1 ? $id_user_arr[1] : NULL; // Ambil ID pengguna saat ini jika ada

        $this->db->where('username', $username);
        if ($current_id !== NULL) {
            $this->db->where('id_user !=', $current_id);
        }
        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_unique_username', 'Username ini sudah digunakan oleh pengguna lain.');
            return FALSE;
        }
        return TRUE;
    }

    // --- Custom Callback untuk Unique Email (kompatibel PHP 5) ---
    public function _unique_email($email, $id_user_str) {
        $id_user_arr = explode('-', $id_user_str);
        $current_id = count($id_user_arr) > 1 ? $id_user_arr[1] : NULL;

        $this->db->where('email', $email);
        if ($current_id !== NULL) {
            $this->db->where('id_user !=', $current_id);
        }
        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_unique_email', 'Email ini sudah digunakan oleh pengguna lain.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Dashboard Admin
     * Navigasi: Dashboard
     * Fungsi: Melihat ringkasan total stok, obat menipis, jumlah user, dan grafik transaksi bulanan.
     * URL: /admin (route ini sudah ada)
     */
    public function index() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Dashboard Admin.');

        $data = $this->_load_common_data('Dashboard Administrator');

        // Mengambil nilai threshold stok minimal dengan kompatibilitas PHP 5
        $min_stok_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('min_stok_threshold');
        $threshold_value = (isset($min_stok_setting->nilai_pengaturan) && is_numeric($min_stok_setting->nilai_pengaturan)) ? $min_stok_setting->nilai_pengaturan : 10;

        $data['total_obat'] = $this->Obat_model->count_all_obat();
        $data['obat_menipis'] = $this->Obat_model->get_low_stock_drugs($threshold_value);
        $data['jumlah_user'] = $this->User_model->count_all_users();
        $data['grafik_transaksi_bulanan'] = $this->Penjualan_model->get_monthly_sales_summary(date('Y'));
        $data['recent_logs'] = $this->Log_Aktivitas_model->get_recent_logs(5);
        $data['total_penjualan_hari_ini'] = $this->Penjualan_model->get_total_sales_today();
        // Pastikan total_penjualan_hari_ini selalu diinisialisasi untuk menghindari warning di view jika null
        if (!isset($data['total_penjualan_hari_ini'])) {
            $data['total_penjualan_hari_ini'] = 0;
        }

        $this->load->view('admin/dashboard', $data); // Memuat view dashboard (full template)
    }

    /**
     * Manajemen User (Daftar Pengguna)
     * Navigasi: Manajemen User -> Daftar Pengguna
     * Fungsi: Menampilkan daftar semua user.
     * URL: /users
     */
    public function users() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Manajemen User.');

        $data = $this->_load_common_data('Manajemen Pengguna');
        $data['users'] = $this->User_model->get_all_users();

        $this->load->view('admin/users/list', $data); // Memuat view daftar user (full template)
    }

    /**
     * Form Tambah/Edit User
     * URL: /users/add, /users/edit/:id
     */
    public function user_form($id_user = NULL) {
        $data = $this->_load_common_data(($id_user === NULL) ? 'Tambah Pengguna Baru' : 'Edit Pengguna');
        $data['user'] = NULL; // Data user untuk form edit

        if ($id_user !== NULL) {
            $data['user'] = $this->User_model->get_user_by_id($id_user);
            if (empty($data['user'])) { // Gunakan empty() untuk PHP 5
                $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
                redirect('users');
            }
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit pengguna ID: ' . $id_user);
        } else {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form tambah pengguna.');
        }

        // Set rules validasi
        $this->form_validation->set_rules('nama_user', 'Nama Pengguna', 'required|trim|max_length[100]');
        // Untuk is_unique dengan pengecualian ID saat edit
        $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[50]|callback__unique_username[id-' . (isset($data['user']->id_user) ? $data['user']->id_user : '0') . ']');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|callback__unique_email[id-' . (isset($data['user']->id_user) ? $data['user']->id_user : '0') . ']');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,kasir,apoteker]');

        // Password hanya required saat tambah atau jika diisi saat edit
        if ($id_user === NULL || !empty($this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/users/form', $data); // Memuat view form user (full template)
        } else {
            $user_data = array(
                'nama_user' => $this->input->post('nama_user', TRUE),
                'username'  => $this->input->post('username', TRUE),
                'email'     => $this->input->post('email', TRUE),
                'role'      => $this->input->post('role', TRUE),
            );

            // Hash password hanya jika diisi
            if (!empty($this->input->post('password'))) {
                $user_data['password'] = $this->input->post('password'); // Model akan menghash
            }

            if ($id_user === NULL) { // Mode Tambah
                if ($this->User_model->create_user($user_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah pengguna baru: ' . $user_data['username']);
                    $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan pengguna.');
                }
            } else { // Mode Edit
                if ($this->User_model->update_user($id_user, $user_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui pengguna ID: ' . $id_user . ' (' . $user_data['username'] . ')');
                    $this->session->set_flashdata('success', 'Pengguna berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui pengguna.');
                }
            }
            redirect('users'); // Redirect ke daftar user
        }
    }

    /**
     * Hapus User
     * URL: /users/delete/:id
     */
    public function delete_user($id_user = NULL) {
        if ($id_user === NULL) {
            $this->session->set_flashdata('error', 'ID Pengguna tidak ditemukan.');
            redirect('users');
        }

        $user = $this->User_model->get_user_by_id($id_user);
        if (empty($user)) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan.');
            redirect('users');
        }

        // Tidak boleh menghapus akun sendiri
        if ($id_user == get_user_id()) {
            $this->session->set_flashdata('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
            redirect('users');
        }

        if ($this->User_model->delete_user($id_user)) {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus pengguna ID: ' . $id_user . ' (' . $user->username . ')');
            $this->session->set_flashdata('success', 'Pengguna berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pengguna.');
        }
        redirect('users');
    }

    /**
     * Data Obat
     * Navigasi: Data Obat
     * Fungsi: Kelola semua data obat (tambah, edit, hapus), lihat stok, cetak QR code.
     * URL: /obat
     */
    public function obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Data Obat.');

        $data = $this->_load_common_data('Data Obat');
        $data['obat'] = $this->Obat_model->get_all_obat();

        $this->load->view('admin/obat/list', $data); // Memuat view daftar obat (full template)
    }

    /**
     * Form Tambah/Edit Obat
     * URL: /obat/add, /obat/edit/:id
     */
    public function obat_form($id_obat = NULL) {
        $data = $this->_load_common_data(($id_obat === NULL) ? 'Tambah Obat Baru' : 'Edit Obat');
        $data['obat'] = NULL; // Data obat untuk form edit

        if ($id_obat !== NULL) {
            $data['obat'] = $this->Obat_model->get_obat_by_id($id_obat);
            if (empty($data['obat'])) {
                $this->session->set_flashdata('error', 'Obat tidak ditemukan.');
                redirect('obat');
            }
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit obat ID: ' . $id_obat);
        } else {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form tambah obat.');
        }

        $this->form_validation->set_rules('nama_obat', 'Nama Obat', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required|integer');
        $this->form_validation->set_rules('id_jenis', 'Jenis', 'required|integer');
        $this->form_validation->set_rules('stok', 'Stok', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('tanggal_kadaluarsa', 'Tanggal Kadaluarsa', 'required|callback__valid_date');

        if ($this->form_validation->run() == FALSE) {
            $data['kategori_obat'] = $this->Kategori_Obat_model->get_all_kategori();
            $data['jenis_obat'] = $this->Jenis_Obat_model->get_all_jenis();
            $this->load->view('admin/obat/form', $data); // Memuat view form obat (full template)
        } else {
            $obat_data = array(
                'nama_obat'          => $this->input->post('nama_obat', TRUE),
                'id_kategori'        => $this->input->post('id_kategori', TRUE),
                'id_jenis'           => $this->input->post('id_jenis', TRUE),
                'stok'               => $this->input->post('stok', TRUE),
                'harga'              => $this->input->post('harga', TRUE),
                'tanggal_kadaluarsa' => $this->input->post('tanggal_kadaluarsa', TRUE)
            );

            if ($id_obat === NULL) { // Mode Tambah
                if ($this->Obat_model->create_obat($obat_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah obat baru: ' . $obat_data['nama_obat']);
                    $this->session->set_flashdata('success', 'Data obat berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan data obat.');
                }
            } else { // Mode Edit
                if ($this->Obat_model->update_obat($id_obat, $obat_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui obat ID: ' . $id_obat . ' (' . $obat_data['nama_obat'] . ')');
                    $this->session->set_flashdata('success', 'Data obat berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui data obat.');
                }
            }
            redirect('obat'); // Redirect ke daftar obat
        }
    }

    /**
     * Hapus Obat
     * URL: /obat/delete/:id
     */
    public function delete_obat($id_obat = NULL) {
        if ($id_obat === NULL) {
            $this->session->set_flashdata('error', 'ID Obat tidak ditemukan.');
            redirect('obat');
        }

        $obat = $this->Obat_model->get_obat_by_id($id_obat);
        if (empty($obat)) {
            $this->session->set_flashdata('error', 'Obat tidak ditemukan.');
            redirect('obat');
        }

        if ($this->Obat_model->delete_obat($id_obat)) {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus obat ID: ' . $id_obat . ' (' . $obat->nama_obat . ')');
            $this->session->set_flashdata('success', 'Obat berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus obat.');
        }
        redirect('obat');
    }

    /**
     * Cetak QR Code Obat
     * Fungsi: Mencetak QR Code yang mengarah ke halaman info obat publik.
     * URL: /obat/qrcode/:id
     */
    /**
     * Cetak QR Code Obat (dari Admin atau Apoteker)
     * URL: /obat/qrcode/:id
     */
    public function obat_qrcode($id_obat = NULL) {
        // Otentikasi dan Otorisasi: Pastikan user sudah login
        // Dan hanya Admin atau Apoteker yang boleh mengakses dan men-generate QR
        if (!is_logged_in() || !has_role(array('admin', 'apoteker'))) { // <<< PASTIKAN INI ADALAH ARRAY UNTUK ADMIN DAN APOTEKER
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin akses untuk membuat QR Code obat.');
            redirect('dashboard'); // Redirect ke dashboard jika tidak diizinkan
        }

        if ($id_obat === NULL) {
            $this->session->set_flashdata('error', 'ID Obat tidak ditemukan untuk QR Code.');
            redirect('obat'); // Redirect ke daftar obat admin jika ID tidak ada
        }
        $obat = $this->Obat_model->get_obat_by_id($id_obat);
        if (empty($obat)) {
            $this->session->set_flashdata('error', 'Obat tidak ditemukan.');
            redirect('obat'); // Redirect ke daftar obat admin jika obat tidak ditemukan
        }

        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mencetak/Mengenerate QR Code untuk obat ID: ' . $id_obat . ' (' . $obat->nama_obat . ')');

        // Data yang di-encode di QR Code adalah URL publik
        $qr_data = site_url('info_obat/' . $obat->uuid_obat);

        $qr_file_path = FCPATH . 'assets/qr_codes/obat-' . $obat->id_obat . '.png';

        $params['data'] = $qr_data;
        $params['savename'] = $qr_file_path;
        $params['level'] = 'H';
        $params['size'] = 10;

        if ($this->ciqrcode->generate($params)) {
            $data = $this->_load_common_data('QR Code Obat: ' . $obat->nama_obat);
            $data['obat'] = $obat;
            $data['qr_image_url'] = base_url('assets/qr_codes/obat-' . $obat->id_obat . '.png');

            $this->load->view('admin/obat/qrcode', $data); // View untuk menampilkan QR Code
        } else {
            $this->session->set_flashdata('error', 'Gagal menghasilkan QR Code untuk obat ini.');
            // Sesuaikan redirect jika Apoteker yang memanggil, mungkin ke daftar obat apoteker
            redirect(is_admin() ? 'obat' : 'data_obat'); // Redirect ke daftar obat admin atau apoteker
        }
    }

    /**
     * Kategori Obat
     * Navigasi: Kategori Obat
     * Fungsi: Tambah/edit kategori obat.
     * URL: /kategori_obat
     */
    public function kategori_obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Manajemen Kategori Obat.');

        $data = $this->_load_common_data('Manajemen Kategori Obat');
        $data['kategori'] = $this->Kategori_Obat_model->get_all_kategori();

        $this->load->view('admin/kategori_obat/list', $data); // Memuat view daftar kategori (full template)
    }

    /**
     * Form Tambah/Edit Kategori Obat
     * URL: /kategori_obat/add, /kategori_obat/edit/:id
     */
    public function kategori_obat_form($id_kategori = NULL) {
        $data = $this->_load_common_data(($id_kategori === NULL) ? 'Tambah Kategori Baru' : 'Edit Kategori');
        $data['kategori'] = NULL;

        if ($id_kategori !== NULL) {
            $data['kategori'] = $this->Kategori_Obat_model->get_kategori_by_id($id_kategori);
            if (empty($data['kategori'])) {
                $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
                redirect('kategori_obat');
            }
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit kategori obat ID: ' . $id_kategori);
        } else {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form tambah kategori obat.');
        }

        // Set rules validasi
        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required|trim|max_length[50]|callback__unique_kategori[' . (isset($data['kategori']->id_kategori) ? $data['kategori']->id_kategori : '0') . ']');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/kategori_obat/form', $data); // Memuat view form kategori (full template)
        } else {
            $kategori_data = array(
                'nama_kategori' => $this->input->post('nama_kategori', TRUE)
            );

            if ($id_kategori === NULL) { // Mode Tambah
                if ($this->Kategori_Obat_model->create_kategori($kategori_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah kategori obat baru: ' . $kategori_data['nama_kategori']);
                    $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan kategori.');
                }
            } else { // Mode Edit
                if ($this->Kategori_Obat_model->update_kategori($id_kategori, $kategori_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui kategori obat ID: ' . $id_kategori . ' (' . $kategori_data['nama_kategori'] . ')');
                    $this->session->set_flashdata('success', 'Kategori berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui kategori.');
                }
            }
            redirect('kategori_obat'); // Redirect ke daftar kategori
        }
    }

    // Custom Callback for unique kategori name during edit
    public function _unique_kategori($nama_kategori, $id_kategori) {
        $this->db->where('nama_kategori', $nama_kategori);
        if ($id_kategori != '0') { // Jika ini mode edit
            $this->db->where('id_kategori !=', $id_kategori);
        }
        $query = $this->db->get('kategori_obat');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_unique_kategori', 'Nama kategori ini sudah ada.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Hapus Kategori Obat
     * URL: /kategori_obat/delete/:id
     */
    public function delete_kategori_obat($id_kategori = NULL) {
        if ($id_kategori === NULL) {
            $this->session->set_flashdata('error', 'ID Kategori tidak ditemukan.');
            redirect('kategori_obat');
        }

        $kategori = $this->Kategori_Obat_model->get_kategori_by_id($id_kategori);
        if (empty($kategori)) {
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
            redirect('kategori_obat');
        }

        // Cek apakah ada obat yang menggunakan kategori ini (perlu method di Obat_model)
        $this->load->model('Obat_model'); // Pastikan sudah di-load di construct
        if ($this->Obat_model->count_obat_by_kategori($id_kategori) > 0) {
            $this->session->set_flashdata('error', 'Kategori ini tidak dapat dihapus karena masih ada obat yang menggunakannya.');
            redirect('kategori_obat');
        }

        if ($this->Kategori_Obat_model->delete_kategori($id_kategori)) {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus kategori obat ID: ' . $id_kategori . ' (' . $kategori->nama_kategori . ')');
            $this->session->set_flashdata('success', 'Kategori berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus kategori.');
        }
        redirect('kategori_obat');
    }


    public function all_qrcodes() {
        // Log aktivitas
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses daftar semua QR Code obat.');

        // Memastikan hanya Admin atau Apoteker yang bisa mengakses halaman ini
        redirect_unauthorized(array('admin', 'apoteker'), 'dashboard', 'Anda tidak memiliki izin akses untuk melihat daftar QR Code.'); // <<< Sudah array

        $data = $this->_load_common_data('Daftar Semua QR Code Obat');
        $data['obat'] = $this->Obat_model->get_all_obat(); // Ambil semua data obat

        $this->load->view('admin/obat/all_qrcodes', $data); // Memuat view daftar QR Code
    }

    /**
     * Jenis Obat
     * Navigasi: Jenis Obat
     * Fungsi: Tambah/edit jenis bentuk obat.
     * URL: /jenis_obat
     */
    public function jenis_obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Manajemen Jenis Obat.');

        $data = $this->_load_common_data('Manajemen Jenis Obat');
        $data['jenis'] = $this->Jenis_Obat_model->get_all_jenis();

        $this->load->view('admin/jenis_obat/list', $data); // Memuat view daftar jenis (full template)
    }

    /**
     * Form Tambah/Edit Jenis Obat
     * URL: /jenis_obat/add, /jenis_obat/edit/:id
     */
    public function jenis_obat_form($id_jenis = NULL) {
        $data = $this->_load_common_data(($id_jenis === NULL) ? 'Tambah Jenis Baru' : 'Edit Jenis');
        $data['jenis'] = NULL;

        if ($id_jenis !== NULL) {
            $data['jenis'] = $this->Jenis_Obat_model->get_jenis_by_id($id_jenis);
            if (empty($data['jenis'])) {
                $this->session->set_flashdata('error', 'Jenis tidak ditemukan.');
                redirect('jenis_obat');
            }
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit jenis obat ID: ' . $id_jenis);
        } else {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form tambah jenis obat.');
        }

        // Set rules validasi
        $this->form_validation->set_rules('nama_jenis', 'Nama Jenis', 'required|trim|max_length[50]|callback__unique_jenis[' . (isset($data['jenis']->id_jenis) ? $data['jenis']->id_jenis : '0') . ']');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/jenis_obat/form', $data); // Memuat view form jenis (full template)
        } else {
            $jenis_data = array(
                'nama_jenis' => $this->input->post('nama_jenis', TRUE)
            );

            if ($id_jenis === NULL) { // Mode Tambah
                if ($this->Jenis_Obat_model->create_jenis($jenis_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah jenis obat baru: ' . $jenis_data['nama_jenis']);
                    $this->session->set_flashdata('success', 'Jenis berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan jenis.');
                }
            } else { // Mode Edit
                if ($this->Jenis_Obat_model->update_jenis($id_jenis, $jenis_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui jenis obat ID: ' . $id_jenis . ' (' . $jenis_data['nama_jenis'] . ')');
                    $this->session->set_flashdata('success', 'Jenis berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui jenis.');
                }
            }
            redirect('jenis_obat'); // Redirect ke daftar jenis
        }
    }

    // Custom Callback for unique jenis name during edit
    public function _unique_jenis($nama_jenis, $id_jenis) {
        $this->db->where('nama_jenis', $nama_jenis);
        if ($id_jenis != '0') { // Jika ini mode edit
            $this->db->where('id_jenis !=', $id_jenis);
        }
        $query = $this->db->get('jenis_obat');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_unique_jenis', 'Nama jenis ini sudah ada.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Hapus Jenis Obat
     * URL: /jenis_obat/delete/:id
     */
    public function delete_jenis_obat($id_jenis = NULL) {
        if ($id_jenis === NULL) {
            $this->session->set_flashdata('error', 'ID Jenis tidak ditemukan.');
            redirect('jenis_obat');
        }

        $jenis = $this->Jenis_Obat_model->get_jenis_by_id($id_jenis);
        if (empty($jenis)) {
            $this->session->set_flashdata('error', 'Jenis tidak ditemukan.');
            redirect('jenis_obat');
        }

        // Cek apakah ada obat yang menggunakan jenis ini (perlu method di Obat_model)
        $this->load->model('Obat_model'); // Pastikan sudah di-load di construct
        if ($this->Obat_model->count_obat_by_jenis($id_jenis) > 0) {
            $this->session->set_flashdata('error', 'Jenis ini tidak dapat dihapus karena masih ada obat yang menggunakannya.');
            redirect('jenis_obat');
        }

        if ($this->Jenis_Obat_model->delete_jenis($id_jenis)) {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus jenis obat ID: ' . $id_jenis . ' (' . $jenis->nama_jenis . ')');
            $this->session->set_flashdata('success', 'Jenis berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus jenis.');
        }
        redirect('jenis_obat');
    }

    /**
     * Stok Obat
     * Navigasi: Stok Obat
     * Fungsi: Lihat semua stok yang ada, filter stok menipis, atau obat kadaluarsa.
     * URL: /stok_obat
     */
    public function stok_obat() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Stok Obat.');

        $data = $this->_load_common_data('Stok Obat');
        $data['obat'] = $this->Obat_model->get_all_obat(); // Semua obat dengan stoknya

        // Ambil filter dari GET request
        $filter_type = $this->input->get('filter'); // 'menipis', 'kadaluarsa'
        $min_stok_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('min_stok_threshold');
        $threshold_value = (isset($min_stok_setting->nilai_pengaturan) && is_numeric($min_stok_setting->nilai_pengaturan)) ? $min_stok_setting->nilai_pengaturan : 10;

        if ($filter_type == 'menipis') {
            $data['obat'] = $this->Obat_model->get_low_stock_drugs($threshold_value);
            $data['filter_aktif'] = 'menipis';
        } elseif ($filter_type == 'kadaluarsa') {
            $data['obat'] = $this->Obat_model->get_expiring_drugs(90); // Obat kadaluarsa dalam 90 hari
            $data['filter_aktif'] = 'kadaluarsa';
        } else {
            $data['filter_aktif'] = ''; // Tidak ada filter aktif
        }

        $this->load->view('admin/stok_obat/list', $data); // Memuat view daftar stok obat (full template)
    }

    /**
     * Stok Masuk
     * Navigasi: Stok Masuk
     * Fungsi: Melihat histori penerimaan obat dari suplier.
     * URL: /stok_masuk
     */
     public function stok_masuk() {
    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses histori Stok Masuk.');

    $data = $this->_load_common_data('Histori Stok Masuk');

    // Ambil filter tanggal dari GET request
    $start_date = $this->input->get('start_date', TRUE);
    $end_date = $this->input->get('end_date', TRUE);

    // Perbarui model untuk bisa menerima parameter tanggal
    $data['stok_masuk'] = $this->Stok_Masuk_model->get_all_stok_masuk($start_date, $end_date);
    $data['start_date_filter'] = $start_date; // Kirim kembali ke view untuk mengisi input
    $data['end_date_filter'] = $end_date;   // Kirim kembali ke view untuk mengisi input

    $this->load->view('admin/stok_masuk/list', $data);
    }

    /**
     * Suplier
     * Navigasi: Suplier
     * Fungsi: Kelola data pemasok (tambah/edit/hapus suplier).
     * URL: /suplier
     */
    public function suplier() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Manajemen Suplier.');

        $data = $this->_load_common_data('Manajemen Suplier');
        $data['suplier'] = $this->Suplier_model->get_all_suplier();

        $this->load->view('admin/suplier/list', $data); // Memuat view daftar suplier (full template)
    }

    /**
     * Form Tambah/Edit Suplier
     * URL: /suplier/add, /suplier/edit/:id
     */
    public function suplier_form($id_suplier = NULL) {
        $data = $this->_load_common_data(($id_suplier === NULL) ? 'Tambah Suplier Baru' : 'Edit Suplier');
        $data['suplier'] = NULL;

        if ($id_suplier !== NULL) {
            $data['suplier'] = $this->Suplier_model->get_suplier_by_id($id_suplier);
            if (empty($data['suplier'])) {
                $this->session->set_flashdata('error', 'Suplier tidak ditemukan.');
                redirect('suplier');
            }
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form edit suplier ID: ' . $id_suplier);
        } else {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses form tambah suplier.');
        }

        $this->form_validation->set_rules('nama_suplier', 'Nama Suplier', 'required|trim|max_length[100]|callback__unique_suplier[' . (isset($data['suplier']->id_suplier) ? $data['suplier']->id_suplier : '0') . ']');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
        $this->form_validation->set_rules('no_telp', 'No. Telepon', 'trim|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/suplier/form', $data); // Memuat view form suplier (full template)
        } else {
            $suplier_data = array(
                'nama_suplier' => $this->input->post('nama_suplier', TRUE),
                'alamat'       => $this->input->post('alamat', TRUE),
                'no_telp'      => $this->input->post('no_telp', TRUE)
            );

            if ($id_suplier === NULL) { // Mode Tambah
                if ($this->Suplier_model->create_suplier($suplier_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menambah suplier baru: ' . $suplier_data['nama_suplier']);
                    $this->session->set_flashdata('success', 'Suplier berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan suplier.');
                }
            } else { // Mode Edit
                if ($this->Suplier_model->update_suplier($id_suplier, $suplier_data)) {
                    $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui suplier ID: ' . $id_suplier . ' (' . $suplier_data['nama_suplier'] . ')');
                    $this->session->set_flashdata('success', 'Suplier berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui suplier.');
                }
            }
            redirect('suplier'); // Redirect ke daftar suplier
        }
    }

     /**
 * Mengambil Detail Penjualan via AJAX
 * Digunakan oleh modal di laporan penjualan.
 * URL: /laporan_penjualan/detail_ajax/:id
 */
public function detail_ajax($id_penjualan = NULL) {
        // --- Bagian Otorisasi yang perlu diperiksa ---
        // is_logged_in() akan redirect jika tidak login.
        // Dalam konteks AJAX, kita tidak ingin redirect, kita ingin respons JSON error.
        // Jadi, kita harus mengubah cek ini.

        // Solusi: Lakukan cek manual dan kembalikan JSON error jika tidak diizinkan.
        if (!is_logged_in()) {
            echo json_encode(array('status' => 'error', 'message' => 'Anda harus login untuk mengakses data ini.'));
            exit();
        }
        // Jika sudah login, cek role.
        if (!has_role(array('admin', 'kasir'))) { // Hanya admin dan kasir yang boleh
            echo json_encode(array('status' => 'error', 'message' => 'Anda tidak memiliki izin akses untuk melihat detail transaksi.'));
            exit();
        }

        if ($id_penjualan === NULL) {
            echo json_encode(array('status' => 'error', 'message' => 'ID Penjualan tidak valid.'));
            exit();
        }

        $penjualan = $this->Penjualan_model->get_all_penjualan_by_id($id_penjualan);
        $details = $this->Penjualan_model->get_detail_penjualan($id_penjualan);

        // Tambahan: Jika Kasir, pastikan hanya bisa melihat transaksinya sendiri
        if (is_kasir() && $penjualan->id_user != get_user_id()) {
            echo json_encode(array('status' => 'error', 'message' => 'Anda tidak diizinkan melihat detail transaksi pengguna lain.'));
            exit();
        }


        if (!empty($penjualan)) {
            echo json_encode(array(
                'status' => 'success',
                'data' => array(
                    'uuid_penjualan' => $penjualan->uuid_penjualan,
                    'tgl_penjualan'  => $penjualan->tgl_penjualan,
                    'total_harga'    => $penjualan->total_harga,
                    'metode_bayar'   => $penjualan->metode_bayar,
                    'nama_kasir'     => isset($penjualan->nama_kasir_lengkap) ? $penjualan->nama_kasir_lengkap : $penjualan->nama_kasir
                ),
                'details' => $details
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Data penjualan tidak ditemukan.'));
        }
        exit();
    }



    // Custom Callback for unique suplier name during edit
    public function _unique_suplier($nama_suplier, $id_suplier) {
        $this->db->where('nama_suplier', $nama_suplier);
        if ($id_suplier != '0') {
            $this->db->where('id_suplier !=', $id_suplier);
        }
        $query = $this->db->get('suplier');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_unique_suplier', 'Nama suplier ini sudah ada.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Hapus Suplier
     * URL: /suplier/delete/:id
     */
    public function delete_suplier($id_suplier = NULL) {
        if ($id_suplier === NULL) {
            $this->session->set_flashdata('error', 'ID Suplier tidak ditemukan.');
            redirect('suplier');
        }

        $suplier = $this->Suplier_model->get_suplier_by_id($id_suplier);
        if (empty($suplier)) {
            $this->session->set_flashdata('error', 'Suplier tidak ditemukan.');
            redirect('suplier');
        }

        // Cek apakah ada stok masuk yang terkait dengan suplier ini (perlu method di Stok_Masuk_model)
        if ($this->Stok_Masuk_model->count_stok_masuk_by_suplier($id_suplier) > 0) {
            $this->session->set_flashdata('error', 'Suplier ini tidak dapat dihapus karena masih ada riwayat stok masuk terkait.');
            redirect('suplier');
        }

        if ($this->Suplier_model->delete_suplier($id_suplier)) {
            $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Menghapus suplier ID: ' . $id_suplier . ' (' . $suplier->nama_suplier . ')');
            $this->session->set_flashdata('success', 'Suplier berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus suplier.');
        }
        redirect('suplier');
    }

    /**
     * Laporan Penjualan
     * Navigasi: Laporan Penjualan
     * Fungsi: Melihat laporan transaksi penjualan oleh kasir dalam periode tertentu (bisa di-export Excel/PDF).
     * URL: /laporan_penjualan
     */
    public function laporan_penjualan() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Laporan Penjualan.');

        $data = $this->_load_common_data('Laporan Penjualan');

        $start_date = $this->input->post('start_date', TRUE);
        $end_date = $this->input->post('end_date', TRUE);

        if (!empty($start_date) && !empty($end_date)) {
            $data['penjualan'] = $this->Penjualan_model->get_all_penjualan($start_date, $end_date);
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
        } else {
            $data['penjualan'] = $this->Penjualan_model->get_all_penjualan(); // Ambil semua data penjualan jika filter tidak ada
        }

        $this->load->view('admin/laporan_penjualan/list', $data); // Memuat view laporan penjualan (full template)
    }

    /**
     * Export Laporan Penjualan (Excel/PDF) - Placeholder
     * URL: /laporan_penjualan/export
     */
    /**
     * Export Laporan Penjualan ke PDF
     * Fungsi: Mengekspor laporan penjualan ke format PDF.
     * URL: /laporan_penjualan/export
     */
    public function export_laporan_penjualan() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengekspor Laporan Penjualan ke PDF.');

        // Ambil data filter dari POST request (seperti yang digunakan di laporan_penjualan())
        $start_date = $this->input->post('start_date', TRUE);
        $end_date = $this->input->post('end_date', TRUE);

        $penjualan_data = $this->Penjualan_model->get_all_penjualan($start_date, $end_date);

        // Siapkan data untuk view laporan PDF
        $data['penjualan'] = $penjualan_data;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['title'] = 'Laporan Penjualan'; // Untuk judul di PDF

        // Load view HTML yang akan diubah menjadi PDF
        // Penting: View ini harus sesimpel mungkin HTML-nya agar Dompdf mudah memparsingnya.
        // Hindari JS kompleks atau CSS eksternal yang terlalu rumit.
        $html = $this->load->view('admin/laporan_penjualan/report_pdf', $data, TRUE); // TRUE untuk mengembalikan HTML sebagai string

        $filename = 'Laporan_Penjualan_';
        if (!empty($start_date) && !empty($end_date)) {
            $filename .= $start_date . '_to_' . $end_date;
        } else {
            $filename .= date('Ymd');
        }
        $filename .= '.pdf';

        // Konfigurasi Dompdf (opsional)
        // $options = new Options(); // Jika Anda menggunakan kelas Options
        // $options->set('isRemoteEnabled', TRUE); // Aktifkan jika ada gambar dari URL eksternal
        // $options->set('defaultFont', 'Helvetica');

        // Untuk PHP 5.6, gunakan array biasa untuk options
        $options_array = array(
            'isRemoteEnabled' => TRUE, // Penting jika ada gambar di HTML yang diakses via base_url()
            'defaultFont' => 'Helvetica',
            // tambahkan opsi lain jika perlu
        );

        // Generate PDF
        $this->ci_dompdf->generate_pdf($html, $filename, TRUE, $options_array); // TRUE untuk langsung stream ke browser
        exit(); // Penting: Hentikan eksekusi setelah PDF di-stream
    }

    /**
     * Log Aktivitas
     * Navigasi: Log Aktivitas
     * Fungsi: Memantau aktivitas user (login, input data, edit, hapus) untuk keamanan sistem.
     * URL: /log_aktivitas
     */
    public function log_aktivitas() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Log Aktivitas.');

        $data = $this->_load_common_data('Log Aktivitas Sistem');
        $data['logs'] = $this->Log_Aktivitas_model->get_all_logs();

        $this->load->view('admin/log_aktivitas/list', $data); // Memuat view daftar log aktivitas (full template)
    }

    /**
     * Pengaturan Sistem
     * Navigasi: Pengaturan Sistem
     * Fungsi: Atur profil klinik, batas minimal stok, logo, dan pengaturan umum lainnya.
     * URL: /pengaturan
     */
    public function pengaturan() {
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses Pengaturan Sistem.');

        $data = $this->_load_common_data('Pengaturan Sistem');
        // Mendapatkan pengaturan sebagai array asosiatif untuk kemudahan akses di view
        $data['pengaturan_array'] = $this->Pengaturan_Sistem_model->get_pengaturan_array();

        $this->form_validation->set_rules('nama_klinik', 'Nama Klinik', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('min_stok_threshold', 'Batas Minimal Stok', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('alamat_klinik', 'Alamat Klinik', 'required|trim');
        $this->form_validation->set_rules('telepon_klinik', 'Telepon Klinik', 'required|trim|max_length[20]');
        // Validasi untuk logo akan terpisah jika ada upload file

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/pengaturan/form', $data); // Memuat view form pengaturan (full template)
        } else {
            $updated_any_setting = FALSE; // Flag untuk melacak apakah ada yang diupdate
            $error_upload = FALSE;

            // Proses update pengaturan teks
            if ($this->Pengaturan_Sistem_model->update_pengaturan('nama_klinik', $this->input->post('nama_klinik', TRUE))) $updated_any_setting = TRUE;
            if ($this->Pengaturan_Sistem_model->update_pengaturan('min_stok_threshold', $this->input->post('min_stok_threshold', TRUE))) $updated_any_setting = TRUE;
            if ($this->Pengaturan_Sistem_model->update_pengaturan('alamat_klinik', $this->input->post('alamat_klinik', TRUE))) $updated_any_setting = TRUE;
            if ($this->Pengaturan_Sistem_model->update_pengaturan('telepon_klinik', $this->input->post('telepon_klinik', TRUE))) $updated_any_setting = TRUE;

            // Handle upload logo jika ada file yang di-upload
            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $config['upload_path']   = './assets/img/'; // Pastikan folder ini ada
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = 2048; // 2MB
                $config['file_name']     = 'logo_apotek_' . time(); // Nama file unik

                $this->load->library('upload', $config); // Load library upload dengan konfigurasi

                if ($this->upload->do_upload('logo')) {
                    $upload_data = $this->upload->data();
                    $new_logo_path = '/assets/img/' . $upload_data['file_name'];
                    if ($this->Pengaturan_Sistem_model->update_pengaturan('logo_url', $new_logo_path)) {
                        $updated_any_setting = TRUE;
                        // Opsional: Hapus logo lama jika ada
                        // $old_logo_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('logo_url');
                        // if ($old_logo_setting && !empty($old_logo_setting->nilai_pengaturan) && file_exists('./' . $old_logo_setting->nilai_pengaturan)) {
                        //     unlink('./' . $old_logo_setting->nilai_pengaturan);
                        // }
                    } else {
                        $this->session->set_flashdata('error', 'Gagal memperbarui URL logo di database.');
                        $error_upload = TRUE;
                    }
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengupload logo: ' . $this->upload->display_errors());
                    $error_upload = TRUE;
                }
            }


            if ($updated_any_setting && !$error_upload) {
                $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Memperbarui Pengaturan Sistem.');
                $this->session->set_flashdata('success', 'Pengaturan berhasil disimpan.');
            } elseif ($error_upload) {
                // Pesan error upload sudah diatur di atas
            } else {
                $this->session->set_flashdata('info', 'Tidak ada perubahan pengaturan yang disimpan.');
            }
            redirect('pengaturan');
        }

        
    }
}