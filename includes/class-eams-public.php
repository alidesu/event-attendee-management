<?php
/**
 * Public Class for Event Attendee Management System
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class EAMS_Public {
    
    /**
     * Initialize the class
     */
    public function init() {
        // Register shortcodes
        add_shortcode('eams_verification', array($this, 'verification_shortcode'));
        
        // Register scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue public scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue on our verification page
        if (is_page(get_option('eams_verification_page_id'))) {
            wp_enqueue_style('eams-public-css', EAMS_PLUGIN_URL . 'public/css/eams-public.css', array(), EAMS_VERSION);
            wp_enqueue_script('eams-public-js', EAMS_PLUGIN_URL . 'public/js/eams-public.js', array('jquery'), EAMS_VERSION, true);
            
            // Localize script
            wp_localize_script('eams-public-js', 'eams_public_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eams_public_nonce')
            ));
        }
    }
    
    /**
     * Verification shortcode callback
     */
    public function verification_shortcode() {
        ob_start();
        
        if (isset($_GET['aid']) && !empty($_GET['aid'])) {
            $attendee_id = sanitize_text_field($_GET['aid']);
            $attendee = $this->get_attendee_by_id($attendee_id);
            
            if ($attendee) {
                include EAMS_PLUGIN_DIR . 'public/templates/verification-result.php';
            } else {
                include EAMS_PLUGIN_DIR . 'public/templates/verification-not-found.php';
            }
        } else {
            include EAMS_PLUGIN_DIR . 'public/templates/verification-form.php';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Get attendee by ID
     */
    private function get_attendee_by_id($attendee_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'event_attendees';
        
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE attendee_id = %s", $attendee_id));
    }
}