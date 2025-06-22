<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// --- Authentication Routes ---
$route['auth'] = 'Auth';
$route['auth/login'] = 'Auth/login';
$route['auth/logout'] = 'Auth/logout';
$route['auth/forgot_password'] = 'Auth/forgot_password';
$route['auth/reset_password/(:any)'] = 'Auth/reset_password/$1';

// --- Dashboard Route (Always 'dashboard') ---
$route['dashboard'] = 'Dashboard'; // Tetap mengarah ke controller Dashboard

// --- Admin Module Routes (Clean URLs) ---

// Manajemen User
$route['users'] = 'Admin/users';
$route['users/add'] = 'Admin/user_form';
$route['users/edit/(:num)'] = 'Admin/user_form/$1';
$route['users/delete/(:num)'] = 'Admin/delete_user/$1';

// Data Obat
$route['obat'] = 'Admin/obat';
$route['obat/add'] = 'Admin/obat_form';
$route['obat/edit/(:num)'] = 'Admin/obat_form/$1';
$route['obat/delete/(:num)'] = 'Admin/delete_obat/$1';
$route['obat/qrcode/(:num)'] = 'Admin/obat_qrcode/$1';
$route['obat/qrcode/(:num)'] = 'QR_Manager/view_or_generate/$1'; // Ubah ini juga

// Kategori Obat
$route['kategori_obat'] = 'Admin/kategori_obat';
$route['kategori_obat/add'] = 'Admin/kategori_obat_form';
$route['kategori_obat/edit/(:num)'] = 'Admin/kategori_obat_form/$1';
$route['kategori_obat/delete/(:num)'] = 'Admin/delete_kategori_obat/$1';

// Jenis Obat
$route['jenis_obat'] = 'Admin/jenis_obat';
$route['jenis_obat/add'] = 'Admin/jenis_obat_form';
$route['jenis_obat/edit/(:num)'] = 'Admin/jenis_obat_form/$1';
$route['jenis_obat/delete/(:num)'] = 'Admin/delete_jenis_obat/$1';

// Stok Obat
$route['stok_obat'] = 'Admin/stok_obat';

// Stok Masuk
$route['stok_masuk'] = 'Admin/stok_masuk';

// Suplier
$route['suplier'] = 'Admin/suplier';
$route['suplier/add'] = 'Admin/suplier_form';
$route['suplier/edit/(:num)'] = 'Admin/suplier_form/$1';
$route['suplier/delete/(:num)'] = 'Admin/delete_suplier/$1';

// Laporan Penjualan
$route['laporan_penjualan'] = 'Admin/laporan_penjualan';
$route['laporan_penjualan/export'] = 'Admin/export_laporan_penjualan';

// Log Aktivitas
$route['log_aktivitas'] = 'Admin/log_aktivitas';

// Pengaturan Sistem
$route['pengaturan'] = 'Admin/pengaturan';
$route['laporan_penjualan/detail_ajax/(:num)'] = 'Admin/detail_ajax/$1';

/*
| -------------------------------------------------------------------------
| END URI ROUTING
| -------------------------------------------------------------------------
*/

// --- Apoteker Module Routes (Clean URLs) ---
// Rute utama untuk Apoteker (jika diakses langsung /apoteker)
$route['apoteker'] = 'Apoteker'; // Mengarah ke Apoteker controller -> index() (Dashboard Apoteker)

// Data Obat (untuk Apoteker: lihat & edit)
$route['data_obat'] = 'Apoteker/data_obat';
$route['data_obat/edit/(:num)'] = 'Apoteker/edit_obat/$1';

// Input Stok Masuk
$route['input_stok_masuk'] = 'Apoteker/input_stok_masuk';

// Scan QR Obat
$route['scan_qr_obat'] = 'Apoteker/scan_qr_obat';

// Obat Kadaluarsa
$route['obat_kadaluarsa'] = 'Apoteker/obat_kadaluarsa';

// Laporan Obat Masuk
$route['laporan_obat_masuk'] = 'Apoteker/laporan_obat_masuk';
$route['laporan_obat_masuk/export'] = 'Apoteker/export_laporan_obat_masuk_pdf';

// Scan QR Obat (AJAX untuk ambil info obat)
$route['apoteker/get_obat_info_by_qr_ajax'] = 'Apoteker/get_obat_info_by_qr_ajax';
$route['obat/all_qrcodes'] = 'QR_Manager'; // Jika diakses /obat/all_qrcodes akan memanggil QR_Manager->index()



/*
| -------------------------------------------------------------------------
| END URI ROUTING
| -------------------------------------------------------------------------
*/

// --- Kasir Module Routes (Clean URLs) ---
// Rute utama untuk Kasir (jika diakses langsung /kasir)
$route['kasir'] = 'Kasir'; // Mengarah ke Kasir controller -> index() (Dashboard Kasir)

// Transaksi Penjualan
$route['transaksi_penjualan'] = 'Kasir/transaksi_penjualan';
$route['kasir/get_obat_info_for_kasir_ajax'] = 'Kasir/get_obat_info_for_kasir_ajax'; // Untuk AJAX info obat

// Riwayat Transaksi
$route['riwayat_transaksi'] = 'Kasir/riwayat_transaksi';

// Cetak Struk
$route['cetak_struk/(:num)'] = 'Kasir/cetak_struk/$1';

// Laporan Penjualan (Kasir - opsional jika ada laporan pribadi)
$route['laporan_penjualan_kasir'] = 'Kasir/laporan_penjualan_kasir';
$route['laporan_penjualan_kasir/export'] = 'Kasir/export_laporan_penjualan_kasir_pdf';

// Transaksi Penjualan (Rute AJAX tambahan)
$route['transaksi_penjualan/remove_item_from_cart_ajax'] = 'Kasir/remove_item_from_cart_ajax';

$route['transaksi_penjualan/add_to_cart_ajax'] = 'Kasir/add_to_cart_ajax';
$route['transaksi_penjualan/remove_item_from_cart_ajax'] = 'Kasir/remove_item_from_cart_ajax'; // Sudah ada, hanya memastikan konsisten
$route['info_obat/(:any)'] = 'Public_Obat/info/$1';

$route['obat/all_qrcodes'] = 'Admin/all_qrcodes'; // <<< PASTIKAN BARIS INI ADA DAN BENAR
/*
| -------------------------------------------------------------------------
| END URI ROUTING
| -------------------------------------------------------------------------
*/