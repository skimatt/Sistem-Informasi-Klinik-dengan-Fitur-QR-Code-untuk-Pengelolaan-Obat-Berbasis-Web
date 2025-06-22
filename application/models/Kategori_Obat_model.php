<?php
// File: application/models/Kategori_Obat_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_Obat_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_kategori()
    {
        $query = $this->db->get('kategori_obat');
        return $query->result();
    }

    public function get_kategori_by_id($id_kategori)
    {
        $this->db->where('id_kategori', $id_kategori);
        $query = $this->db->get('kategori_obat');
        return $query->row();
    }

    public function create_kategori($data)
    {
        return $this->db->insert('kategori_obat', $data);
    }

    public function update_kategori($id_kategori, $data)
    {
        $this->db->where('id_kategori', $id_kategori);
        return $this->db->update('kategori_obat', $data);
    }

    public function delete_kategori($id_kategori)
    {
        // Pertimbangkan apakah kategori bisa dihapus jika ada obat yang terkait
        // Mungkin perlu cek FK atau set NULL pada id_kategori di tabel obat
        $this->db->where('id_kategori', $id_kategori);
        return $this->db->delete('kategori_obat');
    }
}