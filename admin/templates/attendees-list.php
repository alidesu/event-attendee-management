<?php
// attendees-list.php
/**
 * Template for displaying attendees list in admin
 */
?>
<div class="wrap eams-admin-wrap">
    <h1 class="wp-heading-inline"><?php _e('Event Attendees', 'event-attendee-management'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=event-attendees-add'); ?>" class="page-title-action"><?php _e('Add New Attendee', 'event-attendee-management'); ?></a>
    <hr class="wp-header-end">
    
    <div class="eams-action-buttons">
        <a href="<?php echo admin_url('admin-ajax.php?action=eams_export_csv&nonce=' . wp_create_nonce('eams_nonce')); ?>" class="button button-primary eams-export-csv"><?php _e('Export CSV', 'event-attendee-management'); ?></a>
    </div>
    
    <div class="eams-table-wrapper">
        <table id="eams-attendees-table" class="display responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th><?php _e('ID', 'event-attendee-management'); ?></th>
                    <th><?php _e('Name', 'event-attendee-management'); ?></th>
                    <th><?php _e('Attendee ID', 'event-attendee-management'); ?></th>
                    <th><?php _e('Course', 'event-attendee-management'); ?></th>
                    <th><?php _e('Training Date', 'event-attendee-management'); ?></th>
                    <th><?php _e('Expiry Date', 'event-attendee-management'); ?></th>
                    <th><?php _e('Actions', 'event-attendee-management'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendees)) : ?>
                    <?php foreach ($attendees as $attendee) : ?>
                        <tr>
                            <td><?php echo esc_html($attendee->id); ?></td>
                            <td><?php echo esc_html($attendee->name); ?></td>
                            <td><?php echo esc_html($attendee->attendee_id); ?></td>
                            <td><?php echo esc_html($attendee->course); ?></td>
                            <td><?php echo esc_html(date('F j, Y', strtotime($attendee->training_date))); ?></td>
                            <td><?php echo esc_html(date('F j, Y', strtotime($attendee->expiry_date))); ?></td>
                            <td>
                                <button class="button eams-qr-code-btn" data-id="<?php echo esc_attr($attendee->id); ?>"><?php _e('Download QR Code', 'event-attendee-management'); ?></button>
                                <button class="button eams-delete-btn" data-id="<?php echo esc_attr($attendee->id); ?>"><?php _e('Delete', 'event-attendee-management'); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- QR Code Modal -->
<div id="eams-qr-modal" class="eams-modal">
    <div class="eams-modal-content">
        <span class="eams-modal-close">&times;</span>
        <h2><?php _e('Attendee QR Code', 'event-attendee-management'); ?></h2>
        <div class="eams-qr-content">
            <div class="eams-qr-image">
                <!-- QR code will be inserted here -->
            </div>
            <div class="eams-qr-info">
                <p class="eams-qr-name"></p>
                <p class="eams-qr-id"></p>
            </div>
            <div class="eams-qr-actions">
                <button class="button button-primary eams-download-qr"><?php _e('Download QR Code', 'event-attendee-management'); ?></button>
                <button class="button eams-print-qr"><?php _e('Print QR Code', 'event-attendee-management'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="eams-confirm-modal" class="eams-modal">
    <div class="eams-modal-content">
        <span class="eams-modal-close">&times;</span>
        <h2><?php _e('Confirm Deletion', 'event-attendee-management'); ?></h2>
        <p><?php _e('Are you sure you want to delete this attendee? This action cannot be undone.', 'event-attendee-management'); ?></p>
        <div class="eams-confirm-actions">
            <button class="button eams-cancel-btn"><?php _e('Cancel', 'event-attendee-management'); ?></button>
            <button class="button button-primary eams-confirm-delete-btn"><?php _e('Delete', 'event-attendee-management'); ?></button>
        </div>
    </div>
</div>