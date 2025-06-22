<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.googlemail.com'; // Atau tls://smtp.googlemail.com tergantung port
$config['smtp_port'] = 465; // Atau 587 untuk TLS
$config['smtp_user'] = 'rahmatzkk10@gmail.com'; // Ganti dengan email Anda
$config['smtp_pass'] = 'eoeb cxes wtsu vtix'; // Ganti dengan password aplikasi (bukan password email asli)
$config['mailtype'] = 'html';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['newline'] = "\r\n"; // Penting untuk SMTP

