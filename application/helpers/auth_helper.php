<?php
/**
 * Auth Helper for CodeIgniter 3
 *
 * This helper provides utility functions for user authentication and authorization,
 * including checking login status, user roles, and redirecting unauthorized users.
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Authentication & Authorization
 * @author      Your Name/Company Name
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

if ( ! function_exists('is_logged_in'))
{
    /**
     * Checks if the user is currently logged in.
     * If not logged in, it redirects to the login page and sets a flash message.
     *
     * @return bool True if logged in, false otherwise (after redirect).
     */
    function is_logged_in()
    {
        $CI =& get_instance(); // Get CodeIgniter instance

        if (!$CI->session->userdata('logged_in')) {
            $CI->session->set_flashdata('error', 'Anda harus login untuk mengakses halaman ini.');
            redirect('auth/login');
            return FALSE; // Return false after redirect to stop further execution in the calling function
        }
        return TRUE;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_user_role'))
{
    /**
     * Retrieves the role of the currently logged-in user.
     *
     * @return string|null The user's role (e.g., 'admin', 'kasir', 'apoteker') or null if not logged in.
     */
    function get_user_role()
    {
        $CI =& get_instance();
        return $CI->session->userdata('role');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_user_id'))
{
    /**
     * Retrieves the ID of the currently logged-in user.
     *
     * @return int|null The user's ID or null if not logged in.
     */
    function get_user_id()
    {
        $CI =& get_instance();
        return $CI->session->userdata('id_user');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_user_username'))
{
    /**
     * Retrieves the username of the currently logged-in user.
     *
     * @return string|null The user's username or null if not logged in.
     */
    function get_user_username()
    {
        $CI =& get_instance();
        return $CI->session->userdata('username');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_user_name'))
{
    /**
     * Retrieves the full name of the currently logged-in user.
     *
     * @return string|null The user's full name or null if not logged in.
     */
    function get_user_name()
    {
        $CI =& get_instance();
        return $CI->session->userdata('nama_user');
    }
}


// ------------------------------------------------------------------------

if ( ! function_exists('has_role'))
{
    /**
     * Checks if the currently logged-in user has a specific role.
     * This function can check for a single role or an array of roles.
     *
     * @param string|array $required_roles The role(s) to check against.
     * @return bool True if the user has the required role(s), false otherwise.
     */
    function has_role($required_roles)
    {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('role');

        if (is_array($required_roles)) {
            return in_array($user_role, $required_roles);
        } else {
            return ($user_role === $required_roles);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_admin'))
{
    /**
     * Checks if the currently logged-in user is an 'admin'.
     *
     * @return bool True if the user is an admin, false otherwise.
     */
    function is_admin()
    {
        return has_role('admin');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_apoteker'))
{
    /**
     * Checks if the currently logged-in user is an 'apoteker'.
     *
     * @return bool True if the user is an apoteker, false otherwise.
     */
    function is_apoteker()
    {
        return has_role('apoteker');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_kasir'))
{
    /**
     * Checks if the currently logged-in user is a 'kasir'.
     *
     * @return bool True if the user is a kasir, false otherwise.
     */
    function is_kasir()
    {
        return has_role('kasir');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('redirect_unauthorized'))
{
    /**
     * Redirects the user to a specified URL if they do not have the required role(s).
     *
     * @param string|array $required_roles The role(s) to check against.
     * @param string $redirect_url The URL to redirect to if unauthorized. Defaults to 'dashboard'.
     * @param string $message The flash message to set if unauthorized.
     * @return void
     */
    function redirect_unauthorized($required_roles, $redirect_url = 'dashboard', $message = 'Anda tidak memiliki izin untuk mengakses halaman ini.')
    {
        $CI =& get_instance();

        if (!has_role($required_roles)) {
            $CI->session->set_flashdata('error', $message);
            redirect($redirect_url);
            exit(); // Ensure script stops execution after redirect
        }
    }
}

/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */