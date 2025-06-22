<?php
// File: application/controllers/Dashboard.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('auth_helper');
        $this->load->library('session');

        is_logged_in();

        $this->load->model('Log_Aktivitas_model');
        $this->load->model('User_model');
        $this->load->model('Obat_model');
        $this->load->model('Penjualan_model');
        $this->load->model('Stok_Masuk_model');
        $this->load->model('Pengaturan_Sistem_model');
    }

    public function index() {
        $role = get_user_role(); // Menggunakan helper
        $id_user = get_user_id(); // Menggunakan helper
        $nama_user = get_user_name(); // Menggunakan helper

        // Variabel umum untuk semua dashboard, termasuk nama dan role user
        $data['user_role'] = $role;
        $data['username'] = get_user_username();
        $data['nama_user'] = $nama_user; // <<< PASTIKAN INI ADA
        $data['title'] = 'Dashboard';

        $this->Log_Aktivitas_model->log_activity($id_user, 'Mengakses dashboard sebagai ' . $role);

        $min_stok_threshold_setting = $this->Pengaturan_Sistem_model->get_pengaturan_by_name('min_stok_threshold');
        $threshold_value = (isset($min_stok_threshold_setting->nilai_pengaturan) && is_numeric($min_stok_threshold_setting->nilai_pengaturan)) ? $min_stok_threshold_setting->nilai_pengaturan : 10;

        switch ($role) {
            case 'admin':
                $data['title'] = 'Dashboard Administrator';
                $data['total_obat'] = $this->Obat_model->count_all_obat();
                $data['obat_menipis'] = $this->Obat_model->get_low_stock_drugs($threshold_value);
                $data['jumlah_user'] = $this->User_model->count_all_users();
                $data['grafik_transaksi_bulanan'] = $this->Penjualan_model->get_monthly_sales_summary(date('Y'));
                $data['recent_logs'] = $this->Log_Aktivitas_model->get_recent_logs(5);
                $data['total_penjualan_hari_ini'] = $this->Penjualan_model->get_total_sales_today();
                $this->load->view('admin/dashboard', $data);
                break;
            case 'kasir':
                $data['title'] = 'Dashboard Kasir';
                $data['total_transaksi_hari_ini'] = $this->Penjualan_model->count_all_transactions_today();
                $data['total_pemasukan_hari_ini'] = $this->Penjualan_model->get_total_sales_today();
                $this->load->view('kasir/dashboard', $data);
                break;
            case 'apoteker':
                $data['title'] = 'Dashboard Apoteker';
                $data['total_jenis_obat'] = $this->Obat_model->count_all_jenis_obat();
                $data['obat_menipis_apoteker'] = $this->Obat_model->get_low_stock_drugs($threshold_value);
                $data['stok_masuk_hari_ini'] = $this->Stok_Masuk_model->count_stok_masuk_today();
                $data['obat_kadaluarsa_mendekat'] = $this->Obat_model->get_expiring_drugs(30);
                $this->load->view('apoteker/dashboard', $data); // Memuat view apoteker/dashboard
                break;
            default:
                $this->session->set_flashdata('error', 'Peran pengguna tidak valid. Silakan hubungi administrator.');
                $this->session->sess_destroy();
                redirect('auth/login');
                break;
        }
    }
}