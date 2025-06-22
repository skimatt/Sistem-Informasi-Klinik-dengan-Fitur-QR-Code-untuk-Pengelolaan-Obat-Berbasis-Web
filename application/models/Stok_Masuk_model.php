<?php
// File: application/models/Stok_Masuk_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_Masuk_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

public function get_all_stok_masuk($start_date = null, $end_date = null, $nama_obat_filter = null)
    {
        $this->db->select('sm.*, o.nama_obat, s.nama_suplier');
        $this->db->from('stok_masuk sm');
        $this->db->join('obat o', 'sm.id_obat = o.id_obat', 'left');
        $this->db->join('suplier s', 'sm.id_suplier = s.id_suplier', 'left');
        if (!empty($start_date)) {
            $this->db->where('DATE(sm.tanggal_masuk) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(sm.tanggal_masuk) <=', $end_date);
        }
        if (!empty($nama_obat_filter)) {
            $this->db->like('o.nama_obat', $nama_obat_filter, 'both'); // 'both' untuk LIKE %keyword%
        }
        $this->db->order_by('sm.tanggal_masuk', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_stok_masuk_by_id($id_stok_masuk)
    {
        $this->db->select('sm.*, o.nama_obat, s.nama_suplier');
        $this->db->from('stok_masuk sm');
        $this->db->join('obat o', 'sm.id_obat = o.id_obat', 'left');
        $this->db->join('suplier s', 'sm.id_suplier = s.id_suplier', 'left');
        $this->db->where('sm.id_stok_masuk', $id_stok_masuk);
        $query = $this->db->get();
        return $query->row();
    }

    public function create_stok_masuk($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('stok_masuk', $data);
    }

    public function update_stok_masuk($id_stok_masuk, $data)
    {
        $this->db->where('id_stok_masuk', $id_stok_masuk);
        return $this->db->update('stok_masuk', $data);
    }

    public function delete_stok_masuk($id_stok_masuk)
    {
        $this->db->where('id_stok_masuk', $id_stok_masuk);
        return $this->db->delete('stok_masuk');
    }

    public function count_stok_masuk_by_suplier($id_suplier)
    {
        $this->db->where('id_suplier', $id_suplier);
        return $this->db->count_all_results('stok_masuk');
    }

    public function count_stok_masuk_today()
    {
        $this->db->where('DATE(tanggal_masuk)', date('Y-m-d'));
        return $this->db->count_all_results('stok_masuk');
    }

    
}