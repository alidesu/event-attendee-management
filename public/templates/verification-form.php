<?php
// verification-form.php
/**
 * Template for verification form
 */
?>
<div class="eams-verification-container">
    <h2 ><?php _e('Attendee Verification', 'event-attendee-management'); ?></h2>
    
    <p><?php _e('Enter the attendee ID or scan the QR code to verify an attendee.', 'event-attendee-management'); ?></p>
    
    <form id="eams-verification-form" class="eams-form" method="get">
        <div class="eams-form-row">
            <label for="eams-attendee-id"><?php _e('Attendee ID:', 'event-attendee-management'); ?></label>
            <input type="text" id="eams-attendee-id" name="aid" class="eams-input" required>
        </div>
        
        <div class="eams-form-row">
            <button type="submit" class="eams-button"><?php _e('Verify Attendee', 'event-attendee-management'); ?></button>
        </div>
    </form>
</div>