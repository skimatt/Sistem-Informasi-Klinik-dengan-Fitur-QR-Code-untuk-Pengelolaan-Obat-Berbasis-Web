<?php
// File: application/libraries/CI_QRcode.php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI_QRcode Class
 *
 * CodeIgniter wrapper for the PHP QR Code library (phpqrcode)
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    QR Code Generation
 * @author      Your Name
 * @link        https://github.com/phpqrcode/phpqrcode/
 */
class Ciqrcode {

    protected $ci; // CodeIgniter instance
    protected $phpqrcode_path; // Path to the phpqrcode library

    public function __construct()
    {
        $this->ci =& get_instance(); // Get CodeIgniter instance

        // Define the path to the phpqrcode library relative to FCPATH (root of your CI project)
        // Adjust this path if you placed phpqrcode in a different location.
        $this->phpqrcode_path = FCPATH . 'application/libraries/phpqrcode/qrlib.php';

        // Check if the phpqrcode library file exists
        if (!file_exists($this->phpqrcode_path)) {
            log_message('error', 'PHP QR Code library (qrlib.php) not found at: ' . $this->phpqrcode_path);
            show_error('Required QR Code library not found. Please ensure phpqrcode is installed correctly in ' . $this->phpqrcode_path);
            exit(); // Stop execution if the library is critical
        }

        // Include the main phpqrcode library file
        require_once($this->phpqrcode_path);

        // Optional: Set default error reporting level for phpqrcode
        // It can sometimes throw notices/warnings
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

        log_message('info', 'CI_QRcode Library Loaded');
    }

    /**
     * Generates a QR Code image.
     *
     * @param array $params An array of parameters for QR code generation.
     * Possible keys:
     * - 'data' (string, required): The data to encode in the QR code.
     * - 'filename' (string, optional): Full path and filename to save the QR code (e.g., FCPATH.'assets/qrcodes/my_qr.png').
     * If not set, QR code will be output directly to browser.
     * - 'level' (char, optional): Error correction level (L, M, Q, H). Default 'L'.
     * - 'size' (int, optional): Size of the QR code (1-10). Default 3.
     * - 'savename' (string, optional): Alias for 'filename'.
     * - 'format' (string, optional): 'png' (default), 'gif', 'jpeg'. Depends on your GD capabilities.
     * - 'quality' (int, optional): JPEG quality (0-100). Default 90. Only for JPEG.
     * - 'output' (string, optional): 'file' (default if filename is set), 'display' (if no filename).
     */
    public function generate($params = array())
    {
        // Set default parameters
        $params = array_merge(array(
            'data'      => 'No Data Provided',
            'filename'  => null,
            'level'     => QR_ECLEVEL_L, // QR_ECLEVEL_L, QR_ECLEVEL_M, QR_ECLEVEL_Q, QR_ECLEVEL_H
            'size'      => 4, // 1 to 10
            'savename'  => null, // Alias for filename
            'format'    => 'png', // png, gif, jpeg
            'quality'   => 90, // JPEG quality (0-100)
            'output'    => null, // file or display
        ), $params);

        // Use savename if filename is not explicitly set
        if ($params['filename'] === null && $params['savename'] !== null) {
            $params['filename'] = $params['savename'];
        }

        // Determine output mode (file or display)
        if ($params['filename'] !== null) {
            $output_mode = 'file';
        } else {
            $output_mode = 'display';
        }

        // Output QR Code
        if ($output_mode == 'file') {
            // Ensure the directory exists
            $dir = dirname($params['filename']);
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, TRUE); // Recursive directory creation
            }
            QRcode::png($params['data'], $params['filename'], $params['level'], $params['size'], $params['quality']);
            return TRUE;
        } else {
            // Output directly to browser
            header('Content-Type: image/' . $params['format']);
            QRcode::png($params['data'], false, $params['level'], $params['size'], $params['quality']);
            return TRUE;
        }
        return FALSE;
    }
}

/* End of file CI_QRcode.php */
/* Location: ./application/libraries/CI_QRcode.php */