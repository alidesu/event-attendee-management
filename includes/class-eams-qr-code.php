<?php
/**
 * QR Code Generator Class for Event Attendee Management System
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class EAMS_QR_Code {
    
    /**
     * Generate QR code for a given URL
     *
     * @param string $url The URL to encode in QR code
     * @return string Base64 encoded image data
     */
    public function generate($url) {
        // Include QR Code library
        if (!class_exists('QRcode')) {
            require_once EAMS_PLUGIN_DIR . 'includes/phpqrcode/qrlib.php';
        }
        
        // Generate QR code
        ob_start();
        QRcode::png($url, null, QR_ECLEVEL_H, 20, 2); // Increased size and error correction level
        $qr_image_data = ob_get_contents();
        ob_end_clean();
        
        // Convert to base64
        $base64 = 'data:image/png;base64,' . base64_encode($qr_image_data);
        
        return $base64;
    }
}