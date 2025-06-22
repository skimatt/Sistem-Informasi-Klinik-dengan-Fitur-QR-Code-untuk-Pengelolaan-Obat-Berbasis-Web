<?php
// File: application/controllers/Public_Obat.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Public_Obat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Obat_model'); // Hanya butuh Obat_model
        $this->load->helper('url'); // Untuk base_url() dan site_url()
        $this->load->model('Pengaturan_Sistem_model'); // Untuk nama klinik
    }

    /**
     * Menampilkan informasi detail obat untuk publik.
     * URL: /info_obat/:uuid
     */
    public function info($uuid_obat = NULL)
    {
        if ($uuid_obat === NULL) {
            show_404(); // Atau redirect ke halaman error/informasi
        }

        $obat = $this->Obat_model->get_obat_by_uuid($uuid_obat);

        if (empty($obat)) {
            show_404(); // Obat tidak ditemukan
        }

        $data['obat'] = $obat;
        $data['title'] = 'Info Obat: ' . $obat->nama_obat;
        // Mengambil pengaturan sistem untuk informasi klinik di halaman publik (opsional)
        $data['pengaturan_klinik'] = $this->Pengaturan_Sistem_model->get_pengaturan_array();

        $this->load->view('public/obat/info', $data); // Memuat view publik
    }
}