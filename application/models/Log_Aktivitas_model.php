<?php
// File: application/models/Log_aktivitas_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Log_Aktivitas_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function log_activity($user_id, $activity_description)
    {
        $data = [
            'id_user' => $user_id, // Pastikan id_user bisa null jika dibutuhkan untuk log gagal login
            'aktivitas' => $activity_description,
            'waktu' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('log_aktivitas', $data);
    }

    public function get_all_logs()
    {
        $this->db->select('la.*, u.username, u.nama_user');
        $this->db->from('log_aktivitas la');
        $this->db->join('user u', 'la.id_user = u.id_user', 'left');
        $this->db->order_by('la.waktu', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_recent_logs($limit = 10)
    {
        $this->db->select('la.*, u.username, u.nama_user');
        $this->db->from('log_aktivitas la');
        $this->db->join('user u', 'la.id_user = u.id_user', 'left');
        $this->db->order_by('la.waktu', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }
}