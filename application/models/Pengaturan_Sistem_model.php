<?php
// File: application/models/Pengaturan_Sistem_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan_Sistem_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_pengaturan()
    {
        $query = $this->db->get('pengaturan_sistem');
        return $query->result();
    }

    public function get_pengaturan_by_name($name)
    {
        $this->db->where('nama_pengaturan', $name);
        $query = $this->db->get('pengaturan_sistem');
        return $query->row();
    }

    public function update_pengaturan($name, $value)
    {
        $this->db->where('nama_pengaturan', $name);
        return $this->db->update('pengaturan_sistem', ['nilai_pengaturan' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // Fungsi untuk mendapatkan pengaturan sebagai array asosiatif (nama_pengaturan => nilai_pengaturan)
    public function get_pengaturan_array()
    {
        $settings = $this->get_all_pengaturan();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->nama_pengaturan] = $setting->nilai_pengaturan;
        }
        return $result;
    }
}