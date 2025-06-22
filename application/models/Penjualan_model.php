<?php
// File: application/models/Penjualan_model.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_penjualan($start_date = null, $end_date = null)
    {
        $this->db->select('p.*, u.username as nama_kasir');
        $this->db->from('penjualan p');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');
        if ($start_date) {
            $this->db->where('DATE(tgl_penjualan) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(tgl_penjualan) <=', $end_date);
        }
        $this->db->order_by('tgl_penjualan', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_detail_penjualan($id_penjualan)
    {
        $this->db->select('dp.*, o.nama_obat');
        $this->db->from('detail_penjualan dp');
        $this->db->join('obat o', 'dp.id_obat = o.id_obat', 'left');
        $this->db->where('dp.id_penjualan', $id_penjualan);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_monthly_sales_summary($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }
        $this->db->select("DATE_FORMAT(tgl_penjualan, '%Y-%m') AS month, SUM(total_harga) AS total_sales");
        $this->db->from('penjualan');
        $this->db->where("YEAR(tgl_penjualan)", $year);
        $this->db->group_by("month");
        $this->db->order_by("month", "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    public function count_all_transactions()
    {
        return $this->db->count_all('penjualan');
    }

    public function get_total_sales_today()
    {
        $this->db->select('SUM(total_harga) as total_penjualan');
        $this->db->where('DATE(tgl_penjualan)', date('Y-m-d'));
        $query = $this->db->get('penjualan');
        return $query->row()->total_penjualan;
    }


    public function count_all_transactions_today()
    {
        $this->db->where('DATE(tgl_penjualan)', date('Y-m-d'));
        return $this->db->count_all_results('penjualan');
    }
    // Di Penjualan_model.php


// Di Obat_model.php
public function count_all_jenis_obat()
{
    return $this->db->count_all('jenis_obat'); // Atau count distinct id_jenis dari tabel obat
}

 public function get_all_penjualan_by_id($id_penjualan)
    {
        // Pastikan Anda mengambil nama_user lengkap dari user untuk ditampilkan di modal
        $this->db->select('p.*, u.username as nama_kasir, u.nama_user as nama_kasir_lengkap');
        $this->db->from('penjualan p');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');
        $this->db->where('p.id_penjualan', $id_penjualan);
        $query = $this->db->get();
        return $query->row();
    }

public function create_penjualan($data)
    {
        return $this->db->insert('penjualan', $data);
    }

    public function create_detail_penjualan($data)
    {
        return $this->db->insert('detail_penjualan', $data);
    }

    public function get_all_penjualan_by_user($id_user, $start_date = null, $end_date = null)
    {
        $this->db->select('p.*, u.username as nama_kasir');
        $this->db->from('penjualan p');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');
        $this->db->where('p.id_user', $id_user); // Filter berdasarkan ID user
        if (!empty($start_date)) {
            $this->db->where('DATE(tgl_penjualan) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(tgl_penjualan) <=', $end_date);
        }
        $this->db->order_by('tgl_penjualan', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    
}