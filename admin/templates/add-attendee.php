<?php
// add-attendee.php
/**
 * Template for adding new attendees in admin
 */
?>
<div class="wrap eams-admin-wrap">
    <h1><?php _e('Add New Attendee', 'event-attendee-management'); ?></h1>
    
    <form id="eams-add-attendee-form" class="eams-form">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="eams-name"><?php _e('Name', 'event-attendee-management'); ?></label>
                </th>
                <td>
                    <input type="text" id="eams-name" name="name" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eams-attendee-id"><?php _e('Attendee ID', 'event-attendee-management'); ?></label>
                </th>
                <td>
                    <input type="text" id="eams-attendee-id" name="attendee_id" class="regular-text" required>
                    <p class="description"><?php _e('Unique identifier for the attendee', 'event-attendee-management'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eams-course"><?php _e('Course', 'event-attendee-management'); ?></label>
                </th>
                <td>
                    <input type="text" id="eams-course" name="course" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eams-training-date"><?php _e('Training Date', 'event-attendee-management'); ?></label>
                </th>
                <td>
                    <input type="text" id="eams-training-date" name="training_date" class="regular-text eams-datepicker" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="eams-expiry-date"><?php _e('Expiry Date', 'event-attendee-management'); ?></label>
                </th>
                <td>
                    <input type="text" id="eams-expiry-date" name="expiry_date" class="regular-text eams-datepicker" required>
                </td>
            </tr>
        </table>
        
        <input type="hidden" name="action" value="eams_add_attendee">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('eams_nonce'); ?>">
        
        <p class="submit">
            <input type="submit" name="submit" id="eams-submit" class="button button-primary" value="<?php _e('Add Attendee', 'event-attendee-management'); ?>">
        </p>
    </form>
</div>