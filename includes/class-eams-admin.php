<?php
/**
 * Admin Class for Event Attendee Management System
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class EAMS_Admin {
    
    /**
     * Initialize the class
     */
    public function init() {
        // Add menu items
        add_action('admin_menu', array($this, 'add_menu_pages'));
        
        // Register scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Handle AJAX requests
        add_action('wp_ajax_eams_add_attendee', array($this, 'ajax_add_attendee'));
        add_action('wp_ajax_eams_delete_attendee', array($this, 'ajax_delete_attendee'));
        add_action('wp_ajax_eams_get_qr_code', array($this, 'ajax_get_qr_code'));
        add_action('wp_ajax_eams_export_csv', array($this, 'ajax_export_csv'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_menu_pages() {
        add_menu_page(
            __('Event Attendees', 'event-attendee-management'),
            __('Event Attendees', 'event-attendee-management'),
            'manage_options',
            'event-attendees',
            array($this, 'render_attendees_page'),
            'dashicons-groups',
            30
        );
        
        add_submenu_page(
            'event-attendees',
            __('Add New Attendee', 'event-attendee-management'),
            __('Add New Attendee', 'event-attendee-management'),
            'manage_options',
            'event-attendees-add',
            array($this, 'render_add_attendee_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook) {
        // Only enqueue on our plugin pages
        if (strpos($hook, 'event-attendees') === false) {
            return;
        }
        
        // Register DataTables
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css', array(), '1.11.5');
        wp_enqueue_style('datatables-responsive-css', 'https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css', array(), '2.2.9');
        wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), '1.11.5', true);
        wp_enqueue_script('datatables-responsive-js', 'https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js', array('jquery', 'datatables-js'), '2.2.9', true);
        
        // Datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css');
        
        // Plugin specific scripts
        wp_enqueue_style('eams-admin-css', EAMS_PLUGIN_URL . 'admin/css/eams-admin.css', array(), EAMS_VERSION);
        wp_enqueue_script('eams-admin-js', EAMS_PLUGIN_URL . 'admin/js/eams-admin.js', array('jquery', 'datatables-js'), EAMS_VERSION, true);
        
        // Localize script
        wp_localize_script('eams-admin-js', 'eams_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('eams_nonce'),
            'verification_url' => get_permalink(get_option('eams_verification_page_id'))
        ));
    }
    
    /**
     * Render the main attendees page
     */
    public function render_attendees_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'event_attendees';
        
        // Get attendees from database
        $attendees = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        
        // Include template
        include EAMS_PLUGIN_DIR . 'admin/templates/attendees-list.php';
    }
    
    /**
     * Render the add attendee page
     */
    public function render_add_attendee_page() {
        // Include template
        include EAMS_PLUGIN_DIR . 'admin/templates/add-attendee.php';
    }
    
    /**
     * AJAX handler for adding an attendee
     */
    public function ajax_add_attendee() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eams_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'event-attendee-management')));
        }
        
        // Check required fields
        if (empty($_POST['name']) || empty($_POST['attendee_id']) || empty($_POST['course']) || 
            empty($_POST['training_date']) || empty($_POST['expiry_date'])) {
            wp_send_json_error(array('message' => __('All fields are required', 'event-attendee-management')));
        }
        
        // Sanitize input
        $name = sanitize_text_field($_POST['name']);
        $attendee_id = sanitize_text_field($_POST['attendee_id']);
        $course = sanitize_text_field($_POST['course']);
        $training_date = sanitize_text_field($_POST['training_date']);
        $expiry_date = sanitize_text_field($_POST['expiry_date']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'event_attendees';
        
        // Check if attendee ID already exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE attendee_id = %s", $attendee_id));
        if ($exists) {
            wp_send_json_error(array('message' => __('Attendee ID already exists', 'event-attendee-management')));
        }
        
        // Insert into database
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'attendee_id' => $attendee_id,
                'course' => $course,
                'training_date' => $training_date,
                'expiry_date' => $expiry_date
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to add attendee', 'event-attendee-management')));
        }
        
        wp_send_json_success(array(
            'message' => __('Attendee added successfully', 'event-attendee-management'),
            'redirect' => admin_url('admin.php?page=event-attendees')
        ));
    }
    
    /**
     * AJAX handler for deleting an attendee
     */
    public function ajax_delete_attendee() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eams_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'event-attendee-management')));
        }
        
        if (empty($_POST['id'])) {
            wp_send_json_error(array('message' => __('Invalid attendee ID', 'event-attendee-management')));
        }
        
        $id = intval($_POST['id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'event_attendees';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to delete attendee', 'event-attendee-management')));
        }
        
        wp_send_json_success(array('message' => __('Attendee deleted successfully', 'event-attendee-management')));
    }
    
    /**
     * AJAX handler for generating QR code
     */
  

public function ajax_get_qr_code() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'eams_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'event-attendee-management')));
    }

    if (empty($_POST['id'])) {
        wp_send_json_error(array('message' => __('Invalid attendee ID', 'event-attendee-management')));
    }

    $id = intval($_POST['id']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'event_attendees';

    $attendee = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if (!$attendee) {
        wp_send_json_error(array('message' => __('Attendee not found', 'event-attendee-management')));
    }

    // Get the current site URL
    $site_url = 'http://my_current_website.local'; // Replace with your actual site URL
    // Generate the verification URL with the attendee ID
    $verification_url = add_query_arg('aid', $attendee->attendee_id, $site_url);

    // Generate QR code
    $qr_code = new EAMS_QR_Code();
    $qr_image = $qr_code->generate($verification_url);

    if (!$qr_image) {
        wp_send_json_error(array('message' => __('Failed to generate QR code', 'event-attendee-management')));
    }

    wp_send_json_success(array(
        'qr_code' => $qr_image,
        'attendee_name' => $attendee->name,
        'attendee_id' => $attendee->attendee_id
    ));
}
    
    /**
     * AJAX handler for exporting CSV
     */
    public function ajax_export_csv() {
        // Check nonce
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'eams_nonce')) {
            wp_die(__('Security check failed', 'event-attendee-management'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'event_attendees';
        
        $attendees = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
        
        if (empty($attendees)) {
            wp_die(__('No attendees found', 'event-attendee-management'));
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="event-attendees-' . date('Y-m-d') . '.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, array('ID', 'Name', 'Attendee ID', 'Course', 'Training Date', 'Expiry Date', 'Created At'));
        
        // Add data rows
        foreach ($attendees as $attendee) {
            fputcsv($output, $attendee);
        }
        
        fclose($output);
        exit;
    }
}