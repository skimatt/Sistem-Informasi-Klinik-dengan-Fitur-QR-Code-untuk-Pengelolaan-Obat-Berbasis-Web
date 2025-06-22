<?php
// File: application/models/Jenis_Obat_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jenis_Obat_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_jenis()
    {
        $query = $this->db->get('jenis_obat');
        return $query->result();
    }

    public function get_jenis_by_id($id_jenis)
    {
        $this->db->where('id_jenis', $id_jenis);
        $query = $this->db->get('jenis_obat');
        return $query->row();
    }

    public function create_jenis($data)
    {
        return $this->db->insert('jenis_obat', $data);
    }

    public function update_jenis($id_jenis, $data)
    {
        $this->db->where('id_jenis', $id_jenis);
        return $this->db->update('jenis_obat', $data);
    }

    public function delete_jenis($id_jenis)
    {
        // Pertimbangkan apakah jenis bisa dihapus jika ada obat yang terkait
        $this->db->where('id_jenis', $id_jenis);
        return $this->db->delete('jenis_obat');
    }
}