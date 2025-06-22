<?php
// File: application/models/Suplier_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Suplier_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_suplier()
    {
        $query = $this->db->get('suplier');
        return $query->result();
    }

    public function get_suplier_by_id($id_suplier)
    {
        $this->db->where('id_suplier', $id_suplier);
        $query = $this->db->get('suplier');
        return $query->row();
    }

    public function create_suplier($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('suplier', $data);
    }

    public function update_suplier($id_suplier, $data)
    {
        $this->db->where('id_suplier', $id_suplier);
        return $this->db->update('suplier', $data);
    }

    public function delete_suplier($id_suplier)
    {
        // Pertimbangkan apakah suplier bisa dihapus jika ada stok masuk terkait
        $this->db->where('id_suplier', $id_suplier);
        return $this->db->delete('suplier');
    }
}