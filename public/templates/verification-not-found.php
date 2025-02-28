<?php
// verification-not-found.php
/**
 * Template for attendee not found
 */
?>
<div class="eams-verification-container">
    <h2><?php _e('Attendee Verification', 'event-attendee-management'); ?></h2>
    
    <div class="eams-verification-result eams-verification-error">
        <div class="eams-result-icon">
            <span class="dashicons dashicons-no"></span>
        </div>
        
        <div class="eams-result-details">
            <h3><?php _e('Attendee Not Found', 'event-attendee-management'); ?></h3>
            <p><?php _e('The attendee ID you provided was not found in our system.', 'event-attendee-management'); ?></p>
            <p><?php _e('Please check the ID and try again.', 'event-attendee-management'); ?></p>
        </div>
    </div>
    
    <p class="eams-back-link">
        <a href="<?php echo esc_url(get_permalink()); ?>"><?php _e('← Back to Verification', 'event-attendee-management'); ?></a>
    </p>
</div>