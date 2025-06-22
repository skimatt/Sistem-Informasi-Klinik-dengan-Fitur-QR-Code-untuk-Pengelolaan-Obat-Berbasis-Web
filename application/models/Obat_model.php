<?php
// File: application/models/Obat_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Obat_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_obat()
    {
        $this->db->select('o.*, k.nama_kategori, j.nama_jenis');
        $this->db->from('obat o');
        $this->db->join('kategori_obat k', 'o.id_kategori = k.id_kategori', 'left');
        $this->db->join('jenis_obat j', 'o.id_jenis = j.id_jenis', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_obat_by_id($id_obat)
    {
        $this->db->select('o.*, k.nama_kategori, j.nama_jenis');
        $this->db->from('obat o');
        $this->db->join('kategori_obat k', 'o.id_kategori = k.id_kategori', 'left');
        $this->db->join('jenis_obat j', 'o.id_jenis = j.id_jenis', 'left');
        $this->db->where('o.id_obat', $id_obat);
        $query = $this->db->get();
        return $query->row();
    }

    public function create_obat($data)
    {
        $data['uuid_obat'] = $this->db->query('SELECT UUID() AS uuid')->row()->uuid;
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('obat', $data);
    }

    public function update_obat($id_obat, $data)
    {
        $this->db->where('id_obat', $id_obat);
        return $this->db->update('obat', $data);
    }

    public function delete_obat($id_obat)
    {
        $this->db->where('id_obat', $id_obat);
        return $this->db->delete('obat');
    }

    public function get_all_kategori()
    {
        $query = $this->db->get('kategori_obat');
        return $query->result();
    }

    public function get_all_jenis()
    {
        $query = $this->db->get('jenis_obat');
        return $query->result();
    }

    public function get_low_stock_drugs($threshold = 10)
    {
        $this->db->where('stok <=', $threshold);
        $query = $this->db->get('obat');
        return $query->result();
    }

    public function get_expiring_drugs($days = 90)
    {
        $future_date = date('Y-m-d', strtotime("+$days days"));
        $this->db->where('tanggal_kadaluarsa <=', $future_date);
        $this->db->order_by('tanggal_kadaluarsa', 'ASC');
        $query = $this->db->get('obat');
        return $query->result();
    }

    public function count_all_obat()
    {
        return $this->db->count_all('obat');
    }
   

    // Metode baru untuk cek kategori saat hapus kategori_obat
    public function count_obat_by_kategori($id_kategori)
    {
        $this->db->where('id_kategori', $id_kategori);
        return $this->db->count_all_results('obat');
    }

    // Metode baru untuk cek jenis saat hapus jenis_obat
    public function count_obat_by_jenis($id_jenis)
    {
        $this->db->where('id_jenis', $id_jenis);
        return $this->db->count_all_results('obat');
    }


    public function count_all_jenis_obat()
    {
        // Ini akan menghitung jumlah baris di tabel jenis_obat, bukan jumlah jenis unik di tabel obat.
        // Jika Anda ingin jumlah jenis obat yang ada di daftar obat, gunakan distinct count dari tabel obat.
        // Untuk saat ini, asumsikan ini menghitung jumlah record di tabel jenis_obat.
        return $this->db->count_all('jenis_obat');
    }

    public function get_obat_by_uuid($uuid_obat)
    {
        $this->db->select('o.*, k.nama_kategori, j.nama_jenis'); // Pastikan 'o.*' mengambil semua kolom baru jika ada
        $this->db->from('obat o');
        $this->db->join('kategori_obat k', 'o.id_kategori = k.id_kategori', 'left');
        $this->db->join('jenis_obat j', 'o.id_jenis = j.id_jenis', 'left');
        $this->db->where('o.uuid_obat', $uuid_obat); // Filter berdasarkan UUID
        $query = $this->db->get();
        return $query->row();
    }

    public function update_stok($id_obat, $jumlah_perubahan)
    {
        // $jumlah_perubahan bisa positif (tambah) atau negatif (kurang)
        $this->db->set('stok', 'stok + ' . (int)$jumlah_perubahan, FALSE); // FALSE untuk menghindari escape string pada 'stok + jumlah'
        $this->db->where('id_obat', $id_obat);
        return $this->db->update('obat');
    }

    
}