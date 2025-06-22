<?php
// File: application/controllers/Auth.php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['form_validation', 'session', 'email']);
        $this->load->model('User_model');
        $this->load->model('Log_aktivitas_model');
        $this->load->helper(['url', 'form', 'auth_helper']);
        $random_compat_path = APPPATH . 'libraries/random_compat_lib/random.php'; // APPPATH mengarah ke application/
    }

    public function index()
    {
        redirect('auth/login');
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            // Setelah login, selalu redirect ke 'dashboard'
            redirect('dashboard');
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->load->view('auth/login', $data);
        } else {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password');

            $user = $this->User_model->get_by_username($username);

            if ($user && password_verify($password, $user->password)) {
            $this->session->set_userdata([
                'id_user'   => $user->id_user,
                'uuid_user' => $user->uuid_user,
                'nama_user' => $user->nama_user, // <<< PASTIKAN INI TERSIMPAN DI SESI
                'username'  => $user->username,
                'role'      => $user->role,
                'logged_in' => true
            ]);

                $this->Log_aktivitas_model->log_activity($user->id_user, 'Login berhasil');

                $this->session->set_flashdata('success', 'Selamat datang, ' . $user->nama_user . '!');
                // Selalu redirect ke 'dashboard'
                redirect('dashboard');
            } else {
                $user_id_for_log = ($user) ? $user->id_user : null;
                $this->Log_aktivitas_model->log_activity($user_id_for_log, 'Percobaan login gagal untuk username: ' . $username);

                $this->session->set_flashdata('error', 'Username atau password salah.');
                redirect('auth/login');
            }
        }
    }

    public function logout()
    {
        $user_id = $this->session->userdata('id_user');
        if ($user_id) {
            $this->Log_aktivitas_model->log_activity($user_id, 'Logout');
        }

        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'Anda telah berhasil logout.');
        redirect('auth/login');
    }

    // ... fungsi forgot_password dan reset_password tetap sama ...

    public function forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        if ($this->form_validation->run() == FALSE) {
            $data['msg'] = $this->session->flashdata('msg');
            $this->load->view('auth/forgot_password', $data);
        } else {
            $email = $this->input->post('email', TRUE);
            $user = $this->User_model->get_by_email($email);

            if ($user) {
                $this->db->delete('password_resets', ['email' => $email]);
                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $this->db->insert('password_resets', [
                    'email' => $email,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $reset_link = site_url('auth/reset_password/' . $token);
                $this->email->set_newline("\r\n");
                $this->email->from('no-reply@apotek.com', 'Sistem Apotek');
                $this->email->to($email);
                $this->email->subject('Reset Password Anda');
                $email_message = "Halo,<br><br>";
                $email_message .= "Anda menerima email ini karena ada permintaan reset password untuk akun Anda di Sistem Apotek.<br>";
                $email_message .= "Silakan klik link berikut untuk mereset password Anda: <br><br>";
                $email_message .= "<a href='" . $reset_link . "'>Link Reset Password</a><br><br>";
                $email_message .= "Link ini akan kadaluarsa dalam 24 jam.<br>";
                $email_message .= "Jika Anda tidak melakukan permintaan ini, abaikan email ini.<br><br>";
                $email_message .= "Terima kasih,<br>";
                $email_message .= "Tim Sistem Apotek";
                $this->email->message($email_message);

                if ($this->email->send()) {
                    $this->Log_aktivitas_model->log_activity($user->id_user, 'Meminta reset password via email: ' . $email);
                    $this->session->set_flashdata('success', 'Link reset password telah dikirim ke email Anda. Periksa folder spam jika tidak ditemukan.');
                } else {
                    log_message('error', 'Gagal mengirim email reset password ke ' . $email . ': ' . $this->email->print_debugger());
                    $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengirim email. Silakan coba lagi.');
                }
            } else {
                $this->session->set_flashdata('error', 'Email tidak ditemukan dalam sistem.');
            }
            redirect('auth/forgot_password');
        }
    }

    public function reset_password($token = null)
    {
        if (!$token) {
            $this->session->set_flashdata('error', 'Token reset password tidak ditemukan.');
            redirect('auth/forgot_password');
        }

        $reset = $this->db->get_where('password_resets', ['token' => $token])->row();

        if (!$reset || (strtotime($reset->created_at) < (time() - 86400))) {
            $this->session->set_flashdata('error', 'Token tidak valid atau telah kadaluarsa.');
            $this->db->delete('password_resets', ['token' => $token]);
            redirect('auth/forgot_password');
        }

        $this->form_validation->set_rules('password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $data['token'] = $token;
            $data['error'] = validation_errors();
            $data['msg'] = $this->session->flashdata('msg');
            $this->load->view('auth/reset_password', $data);
        } else {
            $new_password = password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT);

            $this->db->where('email', $reset->email)->update('user', ['password' => $new_password]);
            $this->db->delete('password_resets', ['email' => $reset->email]);

            $user_info = $this->User_model->get_by_email($reset->email);
            if ($user_info) {
                $this->Log_aktivitas_model->log_activity($user_info->id_user, 'Password berhasil direset via link.');
            }

            $this->session->set_flashdata('success', 'Password Anda berhasil direset. Silakan login dengan password baru Anda.');
            redirect('auth/login');
        }
    }
}