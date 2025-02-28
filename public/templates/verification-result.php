<?php
// verification-result.php
/**
 * Template for displaying verification results
 */
?>
<div class="eams-verification-container">
    <h2><?php _e('Attendee Verification', 'event-attendee-management'); ?></h2>
    
    <div class="eams-verification-result eams-verification-success">
        <div class="eams-result-icon">
            <span class="dashicons dashicons-yes-alt"></span>
        </div>
        
        <div class="eams-result-details">
            <h3><?php _e('Attendee Verified', 'event-attendee-management'); ?></h3>
            
            <div class="eams-attendee-info">
                <p><strong><?php _e('Name:', 'event-attendee-management'); ?></strong> <?php echo esc_html($attendee->name); ?></p>
                <p><strong><?php _e('Attendee ID:', 'event-attendee-management'); ?></strong> <?php echo esc_html($attendee->attendee_id); ?></p>
                <p><strong><?php _e('Course:', 'event-attendee-management'); ?></strong> <?php echo esc_html($attendee->course); ?></p>
                <p><strong><?php _e('Training Date:', 'event-attendee-management'); ?></strong> <?php echo esc_html(date('F j, Y', strtotime($attendee->training_date))); ?></p>
                <p><strong><?php _e('Expiry Date:', 'event-attendee-management'); ?></strong> <?php echo esc_html(date('F j, Y', strtotime($attendee->expiry_date))); ?></p>
                
                <?php 
                // Check if expired
                $is_expired = strtotime($attendee->expiry_date) < time();
                if ($is_expired) : 
                ?>
                <p class="eams-expired-notice"><?php _e('This certification has expired.', 'event-attendee-management'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <p class="eams-back-link">
        <a href="<?php echo esc_url(get_permalink()); ?>"><?php _e('← Back to Verification', 'event-attendee-management'); ?></a>
    </p>
</div>