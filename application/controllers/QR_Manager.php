<?php
// File: application/controllers/QR_Manager.php

defined('BASEPATH') OR exit('No direct script access allowed');

class QR_Manager extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('auth_helper', 'form'));
        $this->load->library(array('session', 'ciqrcode')); // ciqrcode untuk generate jika diperlukan

        $this->load->model('Log_Aktivitas_model');
        $this->load->model('Obat_model'); // Untuk ambil data obat
        $this->load->model('Pengaturan_Sistem_model'); // Untuk logo di QR (opsional)

        // Pastikan pengguna sudah login
        is_logged_in();

        // Otorisasi: Hanya Admin dan Apoteker yang bisa mengakses controller ini
        redirect_unauthorized(array('admin', 'apoteker'), 'dashboard', 'Anda tidak memiliki izin akses ke modul Manajemen QR Code.');
    }

    // --- Private Helper Method untuk Memuat Data Umum View ---
    private function _load_common_data($title) {
        $data['title'] = $title;
        $data['user_nama'] = get_user_name();
        $data['user_role'] = get_user_role();
        return $data;
    }

    /**
     * Melihat Daftar Semua QR Code Obat
     * URL: /obat/all_qrcodes
     */
    public function index() { // Gunakan index() sebagai default method
        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mengakses daftar semua QR Code obat.');

        $data = $this->_load_common_data('Daftar Semua QR Code Obat');
        $data['obat'] = $this->Obat_model->get_all_obat();

        $this->load->view('admin/obat/all_qrcodes', $data); // View tetap di 'admin/obat'
    }

    /**
     * Metode untuk men-generate/melihat QR code obat tunggal (dipindahkan dari Admin.php)
     * URL: /obat/qrcode/:id
     */
    public function view_or_generate($id_obat = NULL) {
        if ($id_obat === NULL) {
            $this->session->set_flashdata('error', 'ID Obat tidak ditemukan untuk QR Code.');
            redirect(is_admin() || is_apoteker() ? 'obat/all_qrcodes' : 'dashboard'); // Redirect ke daftar semua QR atau dashboard
        }
        $obat = $this->Obat_model->get_obat_by_id($id_obat);
        if (empty($obat)) {
            $this->session->set_flashdata('error', 'Obat tidak ditemukan.');
            redirect(is_admin() || is_apoteker() ? 'obat/all_qrcodes' : 'dashboard');
        }

        $this->Log_Aktivitas_model->log_activity(get_user_id(), 'Mencetak/Mengenerate QR Code untuk obat ID: ' . $id_obat . ' (' . $obat->nama_obat . ')');

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

            $this->load->view('admin/obat/qrcode', $data); // View tetap di 'admin/obat/qrcode'
        } else {
            $this->session->set_flashdata('error', 'Gagal menghasilkan QR Code untuk obat ini.');
            redirect(is_admin() || is_apoteker() ? 'obat/all_qrcodes' : 'dashboard');
        }
    }
}