<?php
// File: application/libraries/CI_Dompdf.php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CI_Dompdf Class
 *
 * CodeIgniter wrapper for the Dompdf library
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    PDF Generation
 * @author      Your Name
 * @link        https://github.com/dompdf/dompdf
 */
class CI_Dompdf {

    public function __construct()
    {
        // Define DOMPDF_ENABLE_AUTOLOAD if not already defined (might be defined by older versions of dompdf)
        if (!defined('DOMPDF_ENABLE_AUTOLOAD')) {
            define('DOMPDF_ENABLE_AUTOLOAD', FALSE);
        }

        // Define the path to the dompdf library relative to FCPATH (root of your CI project)
        // Adjust this path if you placed dompdf in a different location.
        $dompdf_path = FCPATH . 'application/libraries/dompdf/autoload.inc.php'; // For Dompdf 0.8.x and above
                                                                                 // For older versions like 0.6.x, it might be dompdf_config.inc.php and you need to manually include dompdf/dompdf_config.inc.php then new Dompdf()
                                                                                 // If autoload.inc.php doesn't work, try including dompdf_config.inc.php and then require 'vendor/autoload.php' inside dompdf folder, or just include dompdf/dompdf_config.inc.php and manually require class files.
                                                                                 // For 0.8.x, autoload.inc.php is the correct entry.

        if (!file_exists($dompdf_path)) {
            log_message('error', 'Dompdf library (autoload.inc.php) not found at: ' . $dompdf_path);
            show_error('Required Dompdf library not found. Please ensure Dompdf is installed correctly in ' . $dompdf_path);
            exit();
        }

        require_once($dompdf_path);

        // Use Dompdf namespace
        // Make sure to add 'use Dompdf\Dompdf;' at the top of the controller
        // if you want to directly use new Dompdf();
        log_message('info', 'CI_Dompdf Library Loaded');
    }

    /**
     * Generates a PDF from HTML content.
     *
     * @param string $html The HTML content to convert to PDF.
     * @param string $filename The desired output filename for the PDF.
     * @param bool $stream Whether to stream the PDF to the browser (true) or save to file (false).
     * @param array $options An array of Dompdf options.
     * @return string|void Returns PDF content if $stream is false, otherwise streams to browser.
     */
    public function generate_pdf($html, $filename = 'document.pdf', $stream = TRUE, $options = array())
    {
        $dompdf = new Dompdf\Dompdf(); // Instantiate Dompdf object

        // Set options
        foreach ($options as $key => $value) {
            $dompdf->set_option($key, $value);
        }

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        // Default is A4 portrait
        // $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF (1 = stream to browser, 0 = save to file)
        if ($stream) {
            $dompdf->stream($filename, array('Attachment' => 0)); // 'Attachment' => 0 means open in browser
        } else {
            return $dompdf->output(); // Return PDF content for saving to file
        }
    }
}

/* End of file CI_Dompdf.php */
/* Location: ./application/libraries/CI_Dompdf.php */