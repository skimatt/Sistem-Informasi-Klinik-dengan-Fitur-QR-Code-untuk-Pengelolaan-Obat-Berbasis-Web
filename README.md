# Sistem Informasi Apotek (Apotek App)

Sistem Informasi Apotek adalah aplikasi berbasis web yang dirancang untuk membantu mengelola operasional harian sebuah apotek atau klinik. Aplikasi ini menyediakan fungsionalitas manajemen data obat, stok, transaksi penjualan, pengguna, hingga laporan, dengan pembagian akses berdasarkan peran (role) pengguna.

## Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Database](#struktur-database)
- [Instalasi](#instalasi)
  - [Persyaratan Sistem](#persyaratan-sistem)
  - [Langkah-langkah Instalasi](#langkah-langkah-instalasi)
- [Penggunaan Aplikasi](#penggunaan-aplikasi)
  - [Login](#login)
  - [Peran Pengguna](#peran-pengguna)
- [Fitur Unggulan](#fitur-unggulan)
- [Kontributor](#kontributor)
- [Lisensi](#lisensi)

## Fitur Utama

Sistem ini memiliki fitur-fitur komprehensif yang dibagi berdasarkan peran pengguna:

### 1. Role: Admin 🧑‍💼
Admin memiliki kendali penuh atas seluruh sistem.
- **Dashboard:** Ringkasan total stok obat, obat menipis, jumlah user, dan grafik transaksi bulanan.
- **Manajemen User:** Tambah, edit, dan hapus user (admin, kasir, apoteker).
- **Data Obat:** Kelola semua data obat (tambah, edit, hapus), lihat stok.
- **Kategori Obat:** Tambah, edit, dan hapus kategori obat (misal: Antibiotik, Vitamin).
- **Jenis Obat:** Tambah, edit, dan hapus jenis bentuk obat (misal: Tablet, Sirup, Kapsul).
- **Stok Obat:** Lihat semua stok terkini, filter stok menipis, atau obat kadaluarsa.
- **Histori Stok Masuk:** Melihat histori penerimaan obat dari suplier.
- **Suplier:** Kelola data pemasok (tambah, edit, hapus suplier).
- **Laporan Penjualan:** Melihat laporan transaksi penjualan oleh kasir dalam periode tertentu, dapat diekspor ke PDF.
- **Log Aktivitas:** Memantau semua aktivitas user (login, input data, edit, hapus) untuk keamanan sistem.
- **Pengaturan Sistem:** Atur profil klinik, batas minimal stok, logo, dan pengaturan umum lainnya.
- **Cetak Semua QR Code:** Mencetak daftar QR Code semua obat.

### 2. Role: Apoteker 🧑‍⚕️
Apoteker bertanggung jawab atas manajemen inventori obat dan pergerakan stok.
- **Dashboard:** Ringkasan total jenis obat, obat menipis, jumlah stok masuk hari ini, dan status kadaluarsa.
- **Data Obat:** Lihat dan edit informasi obat (tanpa hapus).
- **Input Stok Masuk:** Input kedatangan obat baru dari suplier.
- **Stok Obat:** Melihat semua stok terkini, status menipis, dan filter berdasarkan kategori/jenis.
- **Scan QR Obat:** Scan QR Code obat untuk mengurangi stok (pergerakan internal, bukan penjualan).
- **Obat Kadaluarsa:** Lihat daftar obat yang akan atau sudah kedaluwarsa.
- **Laporan Obat Masuk:** Lihat & ekspor histori obat masuk dari suplier ke PDF.
- **Cetak Semua QR Code:** Mencetak daftar QR Code semua obat.

### 3. Role: Kasir 💵
Kasir bertanggung jawab atas proses transaksi penjualan dan pencatatan riwayat transaksi.
- **Dashboard:** Menampilkan total transaksi hari ini, total pemasukan, dan shortcut ke transaksi baru.
- **Transaksi Penjualan:** Halaman untuk melakukan transaksi penjualan: scan QR / input manual / pilih obat dari daftar → isi jumlah → total → input pembayaran → simpan transaksi.
- **Riwayat Transaksi:** Menampilkan daftar transaksi yang pernah dilakukan oleh kasir tersebut.
- **Cetak Struk:** Cetak ulang struk penjualan berdasarkan ID transaksi (otomatis setelah pembayaran).
- **Laporan Penjualan:** Melihat dan mengekspor transaksi pribadi per hari/bulan ke PDF.

## Teknologi yang Digunakan
- **Framework:** CodeIgniter 3
- **Bahasa Pemrograman:** PHP 5.6 (kompatibilitas penuh)
- **Database:** MySQL / MariaDB
- **Frontend Framework:** Vuexy Admin Template (Bootstrap 5)
- **JavaScript Libraries:**
  - jQuery
  - Select2 (untuk dropdown interaktif)
  - Instascan (untuk QR Code scanning via webcam)
  - Chart.js (untuk grafik di dashboard)
  - DataTables (untuk tabel data interaktif)
  - Dompdf (untuk PDF generation)
  - random_compat (polyfill untuk `random_bytes()` di PHP 5)

## Struktur Database
Aplikasi ini menggunakan database `db_apotek` dengan tabel-tabel berikut:
- `user`: Pengelolaan pengguna (admin, apoteker, kasir).
- `obat`: Data master obat.
- `jenis_obat`: Kategori jenis bentuk obat.
- `kategori_obat`: Kategori fungsional obat.
- `suplier`: Data pemasok obat.
- `stok_masuk`: Riwayat penerimaan stok.
- `detail_penjualan`: Detail item dalam setiap penjualan.
- `penjualan`: Data transaksi penjualan utama.
- `log_aktivitas`: Catatan aktivitas pengguna.
- `password_resets`: Untuk fitur reset password.
- `pengaturan_sistem`: Konfigurasi umum aplikasi.

*Anda dapat menemukan skema database lengkapnya di file `database/db_apotek.sql` (buat file ini dan paste SQL schema di sana).*

## Instalasi

### Persyaratan Sistem
- Web server (Apache / Nginx)
- PHP 5.6 (dengan ekstensi `php_mysql`, `php_gd2`, `php_openssl`, `php_mbstring`, `php_json` aktif)
- MySQL / MariaDB
- Composer (opsional, jika Anda ingin menggunakan Autoloading PSR-4 untuk library tertentu, namun instalasi manual juga didukung).
- Koneksi internet stabil (untuk CDN dan update library).
- **PENTING: Untuk fitur scan QR Code dengan kamera, web server harus diakses melalui HTTPS, meskipun di localhost.** (Konfigurasi SSL lokal di XAMPP/WAMP sangat disarankan).

### Langkah-langkah Instalasi

1.  **Clone Repositori:**
    ```bash
    git clone [https://github.com/your-username/your-repo-name.git](https://github.com/your-username/your-repo-name.git)
    cd your-repo-name # Masuk ke folder proyek
    ```
    *(Ganti `your-username/your-repo-name` dengan username dan nama repositori GitHub Anda)*

2.  **Konfigurasi Web Server:**
    * Pindahkan seluruh folder proyek ke dalam direktori `htdocs` (untuk XAMPP) atau `www` (untuk WAMP) di web server Anda.
    * Pastikan `mod_rewrite` aktif di Apache dan file `.htaccess` CodeIgniter berfungsi untuk *clean URLs*.

3.  **Konfigurasi Database:**
    * Buat database baru di MySQL/MariaDB (misal: `db_apotek`).
    * Impor skema database dari file `database/db_apotek.sql` (buat file ini dengan skema yang sudah kita diskusikan) ke database yang baru dibuat.

4.  **Konfigurasi CodeIgniter:**
    * Buka `application/config/database.php` dan sesuaikan pengaturan database Anda:
        ```php
        'hostname' => 'localhost',
        'username' => 'your_db_user', // Ganti dengan username database Anda
        'password' => 'your_db_password', // Ganti dengan password database Anda
        'database' => 'db_apotek',
        'dbdriver' => 'mysqli',
        // ...
        ```
    * Buka `application/config/config.php` dan sesuaikan `base_url`:
        ```php
        $config['base_url'] = 'http://localhost/apoteker/'; // Sesuaikan dengan URL proyek Anda
        // Jika menggunakan HTTPS lokal, ubah menjadi:
        // $config['base_url'] = 'https://localhost/apoteker/';
        ```
    * Di `application/config/config.php`, atur konfigurasi CSRF:
        ```php
        $config['csrf_protection'] = TRUE;
        $config['csrf_token_name'] = 'csrf_test_name'; // Bisa diganti
        $config['csrf_cookie_name'] = 'csrf_cookie_name'; // Bisa diganti
        $config['csrf_expire'] = 7200; // 2 jam
        $config['csrf_regenerate'] = FALSE; // Penting untuk kompatibilitas AJAX PHP 5
        $config['csrf_exclude_uris'] = array(); // Tambahkan URI yang tidak butuh CSRF jika ada
        ```
    * Di `application/config/autoload.php`, pastikan library dan helper berikut dimuat:
        ```php
        $autoload['libraries'] = array('database', 'session', 'form_validation', 'email', 'ciqrcode', 'ci_dompdf');
        $autoload['helper'] = array('url', 'form', 'auth_helper');
        ```

5.  **Instalasi Library Pihak Ketiga (Manual):**
    * **`phpqrcode`:**
        * Unduh `phpqrcode` dari [https://github.com/phpqrcode/phpqrcode/releases](https://github.com/phpqrcode/phpqrcode/archive/refs/tags/v2.0.20.zip) (atau versi `v2.0.x`).
        * Ekstrak dan letakkan folder `phpqrcode-v2.0.20/qrlib.php` ke `application/libraries/phpqrcode/qrlib.php`. Pastikan path-nya benar di `CI_QRcode.php`.
    * **`dompdf`:**
        * Unduh `dompdf` versi 0.8.x dari [https://github.com/dompdf/dompdf/releases](https://github.com/dompdf/dompdf/archive/refs/tags/v0.8.6.zip).
        * Ekstrak dan letakkan folder `dompdf-0.8.6` ke `application/libraries/dompdf/`.
    * **`random_compat`:**
        * Unduh `random_compat` dari [https://github.com/paragonie/random_compat/releases](https://github.com/paragonie/random_compat/archive/refs/tags/v2.0.20.zip) (atau versi `v2.0.x`).
        * Ekstrak dan letakkan folder `lib` ke `application/libraries/random_compat_lib/`.
    * **`instascan.min.js`:**
        * Unduh `instascan.min.js` dari [https://raw.githack.com/schmich/instascan-umd/master/dist/instascan.min.js](https://raw.githack.com/schmich/instascan-umd/master/dist/instascan.min.js).
        * Letakkan di `assets/js/vendor/instascan.min.js`.
    * **`beep.mp3`:**
        * Siapkan file suara `beep.mp3` dan letakkan di `assets/sounds/beep.mp3`.

6.  **Buat Akun Admin Pertama:**
    * Akses phpMyAdmin atau client SQL Anda.
    * Jalankan query berikut untuk membuat akun admin:
    ```sql
    INSERT INTO `user` (`uuid_user`, `nama_user`, `username`, `password`, `role`, `email`, `created_at`) VALUES
    (UUID(), 'Administrator Apotek', 'admin', '$2y$10$WzQ0EmGf9K2PoyMG4EwbEOzJYrM6LO3CwShlP3i/8DhFi0Cg8dLOe', 'admin', 'admin@apotek.com', NOW());
    ```
    *(Ganti `password` dengan hash password yang Anda generate sendiri untuk keamanan! Gunakan `password_hash('password_anda', PASSWORD_DEFAULT)` di PHP)*
    * **Login dengan username `admin` dan password yang Anda atur.**

7.  **Konfigurasi HTTPS Lokal (Jika menggunakan fitur kamera):**
    * Ikuti panduan XAMPP/WAMP untuk mengaktifkan SSL lokal. Ini penting agar browser mengizinkan akses kamera di `https://localhost`.
    * Setelah dikonfigurasi, akses aplikasi Anda via `https://localhost/apoteker/`.

8.  **Jalankan Aplikasi:**
    * Buka browser Anda dan navigasi ke URL proyek Anda (misal: `http://localhost/apoteker/` atau `https://localhost/apoteker/`).

## Penggunaan Aplikasi

### Login
Akses halaman login di `http://localhost/apoteker/auth/login`. Masukkan *username* dan *password* Anda.

### Peran Pengguna
Setelah login, Anda akan diarahkan ke dashboard sesuai peran Anda. Navigasi sidebar akan menyesuaikan hak akses masing-masing peran.

## Fitur Unggulan

- **Manajemen Peran (Role-Based Access Control):** Hak akses yang terdefinisi dengan jelas untuk Admin, Apoteker, dan Kasir.
- **QR Code Dinamis:**
  - Setiap obat memiliki QR Code unik yang dapat dipindai.
  - Memindai QR Code akan mengarahkan ke halaman informasi obat publik (tanpa perlu login) yang menampilkan detail, dosis, peringatan, dan tanggal kedaluwarsa.
  - Membantu Apoteker dan Kasir dalam pencarian obat dan pengurangan stok yang efisien.
- **Transaksi Penjualan Interaktif:**
  - Proses penjualan yang cepat dengan scan QR Code langsung ke keranjang.
  - Penghitungan total dan kembalian otomatis.
  - Cetak struk instan setelah pembayaran.
- **Pelacakan Stok Komprehensif:**
  - Pemantauan stok real-time, obat menipis, dan obat kadaluarsa.
  - Pencatatan detail histori stok masuk.
- **Laporan & Audit:**
  - Laporan penjualan dan obat masuk yang dapat difilter dan diekspor ke PDF.
  - Log aktivitas pengguna untuk keamanan dan audit sistem.

## Kontributor
- [Rahmat Mulia] - Inisiator & Pengembang Utama

## Lisensi
Aplikasi ini bersifat sumber terbuka dan dilisensikan di bawah [Sebutkan Lisensi Anda, misal: MIT License].

---
