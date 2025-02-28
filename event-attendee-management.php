<?php
/**
 * Plugin Name: Event Attendee Management System
 * Plugin URI: https://example.com/event-attendee-management
 * Description: A plugin to manage event attendees with QR code generation and verification
 * Version: 1.0.0
 * Author: Claude
 * Author URI: https://example.com
 * License: GPL-2.0+
 * Text Domain: event-attendee-management
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('EAMS_VERSION', '1.0.0');
define('EAMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EAMS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Activation hook
 */
function eams_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_attendees';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        attendee_id varchar(50) NOT NULL,
        course varchar(100) NOT NULL,
        training_date date NOT NULL,
        expiry_date date NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY attendee_id (attendee_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Create verification page
    $verification_page = array(
        'post_title'    => 'Attendee Verification',
        'post_content'  => '[eams_verification]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
    );
    
    $page_id = wp_insert_post($verification_page);
    
    // Save the page ID as an option
    update_option('eams_verification_page_id', $page_id);
    
    // Set up flush_rewrite_rules to run once after this function
    add_action('shutdown', 'flush_rewrite_rules');
}
register_activation_hook(__FILE__, 'eams_activate');

/**
 * Deactivation hook
 */
function eams_deactivate() {
    // Deactivation code here
    
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'eams_deactivate');

/**
 * Uninstall hook to remove database tables
 */
function eams_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_attendees';
    
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Delete the verification page
    $page_id = get_option('eams_verification_page_id');
    if ($page_id) {
        wp_delete_post($page_id, true);
    }
    
    // Delete plugin options
    delete_option('eams_verification_page_id');
}
register_uninstall_hook(__FILE__, 'eams_uninstall');

/**
 * Include required files
 */
require_once EAMS_PLUGIN_DIR . 'includes/class-eams-admin.php';
require_once EAMS_PLUGIN_DIR . 'includes/class-eams-public.php';
require_once EAMS_PLUGIN_DIR . 'includes/class-eams-qr-code.php';

/**
 * Initialize the plugin
 */
function eams_init() {
    // Initialize admin class
    $admin = new EAMS_Admin();
    $admin->init();
    
    // Initialize public class
    $public = new EAMS_Public();
    $public->init();
}
add_action('plugins_loaded', 'eams_init');