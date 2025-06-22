<?php
// File: application/models/User_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_by_username($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('user');
        return $query->row();
    }

    public function get_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('user');
        return $query->row();
    }

    public function get_all_users()
    {
        $query = $this->db->get('user');
        return $query->result();
    }

    public function get_user_by_id($id_user)
    {
        $this->db->where('id_user', $id_user);
        $query = $this->db->get('user');
        return $query->row();
    }

    public function create_user($data)
    {
        $data['uuid_user'] = $this->db->query('SELECT UUID() AS uuid')->row()->uuid;
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT); // Pastikan password di-hash
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('user', $data);
    }

    public function update_user($id_user, $data)
    {
        $this->db->where('id_user', $id_user);
        // Jika password di-update, pastikan di-hash
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // Jangan update password jika kosong
        }
        return $this->db->update('user', $data);
    }

    public function delete_user($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->delete('user');
    }

    public function count_all_users()
    {
        return $this->db->count_all('user');
    }
}