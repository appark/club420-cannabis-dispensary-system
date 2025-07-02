
// Enhanced "Club420 Product URL Fields with Scheduler" - COMPLETE WITH FIXED SAVE
// PHASE 2B: Radio button interface with scheduling system
// Backward compatible with existing checkbox system

add_action('woocommerce_product_options_general_product_data', 'club420_add_enhanced_product_fields_with_scheduler');
function club420_add_enhanced_product_fields_with_scheduler() {
    global $post;
    $product_id = $post->ID;
    
    echo '<div class="options_group">';
    echo '<h4 style="padding-left: 12px; margin-bottom: 10px; color: #0073aa;">CLUB420 Store Settings with Scheduler</h4>';
    
    // Get current WordPress timezone for proper time handling
    $wp_timezone = wp_timezone();
    
    // Davis Store Section
    echo '<div style="padding: 12px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 5px;">';
    echo '<h5 style="margin: 0 0 10px 0; color: #2271b1;">Davis Store</h5>';
    
    // Davis visibility options (radio buttons)
    echo '<p style="margin-bottom: 10px; font-weight: 500;">Product visibility for Davis customers:</p>';
    
    // Get current Davis visibility setting (with backward compatibility)
    $davis_visibility = get_post_meta($product_id, '_club420_davis_visibility', true);
    $davis_enabled = get_post_meta($product_id, '_club420_davis_enabled', true);
    
    // Backward compatibility: convert old checkbox to radio value
    if (empty($davis_visibility)) {
        if ($davis_enabled === 'yes') {
            $davis_visibility = 'always';
        } else {
            $davis_visibility = 'disabled';
        }
    }
    
    woocommerce_wp_radio( array(
        'id' => '_club420_davis_visibility',
        'value' => $davis_visibility,
        'options' => array(
            'always' => 'Always visible to Davis customers',
            'scheduled' => 'Visible only during scheduled times',
            'disabled' => 'Not available to Davis customers'
        )
    ));
    
    // Davis URL Field (unchanged)
    woocommerce_wp_text_input(array(
        'id' => '_club420_davis_url',
        'label' => 'Davis Store URL',
        'type' => 'url',
        'placeholder' => 'https://club420.com/menu/f-street/categories/...'
    ));
    
    // Davis Schedule Section (shows when scheduled is selected)
    echo '<div id="davis_schedule_section" style="display: none; background: #e8f4fd; border: 1px solid #0073aa; border-radius: 4px; padding: 15px; margin-top: 10px;">';
    echo '<h6 style="margin: 0 0 10px 0; color: #0073aa;">📅 Davis Store Schedule</h6>';
    echo '<p style="font-size: 12px; color: #666; margin-bottom: 10px;">Times are in Los Angeles timezone (your business timezone)</p>';
    
    // Davis Start Date/Time
    woocommerce_wp_text_input(array(
        'id' => '_club420_davis_schedule_start',
        'label' => '🟢 START: Show product from',
        'type' => 'datetime-local',
        'custom_attributes' => array(
            'step' => '60'
        ),
        'description' => 'Date and time when product becomes visible'
    ));
    
    // Davis End Date/Time
    woocommerce_wp_text_input(array(
        'id' => '_club420_davis_schedule_end',
        'label' => '🔴 END: Hide product after',
        'type' => 'datetime-local',
        'custom_attributes' => array(
            'step' => '60'
        ),
        'description' => 'Date and time when product becomes hidden'
    ));
    
    echo '</div>'; // End Davis schedule section
    echo '</div>'; // End Davis store section
    
    // Dixon Store Section
    echo '<div style="padding: 12px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 5px;">';
    echo '<h5 style="margin: 0 0 10px 0; color: #2271b1;">Dixon Store</h5>';
    
    // Dixon visibility options (radio buttons)
    echo '<p style="margin-bottom: 10px; font-weight: 500;">Product visibility for Dixon customers:</p>';
    
    // Get current Dixon visibility setting (with backward compatibility)
    $dixon_visibility = get_post_meta($product_id, '_club420_dixon_visibility', true);
    $dixon_enabled = get_post_meta($product_id, '_club420_dixon_enabled', true);
    
    // Backward compatibility: convert old checkbox to radio value
    if (empty($dixon_visibility)) {
        if ($dixon_enabled === 'yes') {
            $dixon_visibility = 'always';
        } else {
            $dixon_visibility = 'disabled';
        }
    }
    
    // Dixon visibility radio buttons
    woocommerce_wp_radio( array(
        'id' => '_club420_dixon_visibility',
        'value' => $dixon_visibility,
        'options' => array(
            'always' => 'Always visible to Dixon customers',
            'scheduled' => 'Visible only during scheduled times',
            'disabled' => 'Not available to Dixon customers'
        )
    ));
    
    // Dixon URL Field (unchanged)
    woocommerce_wp_text_input(array(
        'id' => '_club420_dixon_url',
        'label' => 'Dixon Store URL',
        'type' => 'url',
        'placeholder' => 'https://club420.com/menu/highway-80/categories/...'
    ));
    
    // Dixon Schedule Section (shows when scheduled is selected)
    echo '<div id="dixon_schedule_section" style="display: none; background: #e8f4fd; border: 1px solid #0073aa; border-radius: 4px; padding: 15px; margin-top: 10px;">';
    echo '<h6 style="margin: 0 0 10px 0; color: #0073aa;">📅 Dixon Store Schedule</h6>';
    echo '<p style="font-size: 12px; color: #666; margin-bottom: 10px;">Times are in Los Angeles timezone (your business timezone)</p>';
    
    // Dixon Start Date/Time
    woocommerce_wp_text_input(array(
        'id' => '_club420_dixon_schedule_start',
        'label' => '🟢 START: Show product from',
        'type' => 'datetime-local',
        'custom_attributes' => array(
            'step' => '60'
        ),
        'description' => 'Date and time when product becomes visible'
    ));
    
    // Dixon End Date/Time
    woocommerce_wp_text_input(array(
        'id' => '_club420_dixon_schedule_end',
        'label' => '🔴 END: Hide product after',
        'type' => 'datetime-local',
        'custom_attributes' => array(
            'step' => '60'
        ),
        'description' => 'Date and time when product becomes hidden'
    ));
    
    echo '</div>'; // End Dixon schedule section
    echo '</div>'; // End Dixon store section
    
    // Info section
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 10px; margin-top: 15px; font-size: 13px; color: #856404;">';
    echo '<strong>💡 How Store Visibility Works:</strong><br>';
    echo '• <strong>Always visible:</strong> Product shows to store customers at all times<br>';
    echo '• <strong>Scheduled visibility:</strong> Product shows only during specific date/time periods<br>';
    echo '• <strong>Not available:</strong> Product never shows to store customers<br>';
    echo '• <strong>Timezone:</strong> All scheduled times are in Los Angeles timezone';
    echo '</div>';
    
    echo '</div>'; // End options_group
    
    // JavaScript for show/hide schedule sections
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Function to show/hide Davis schedule section
        function toggleDavisSchedule() {
            var selectedValue = $('input[name="_club420_davis_visibility"]:checked').val();
            if (selectedValue === 'scheduled') {
                $('#davis_schedule_section').slideDown();
            } else {
                $('#davis_schedule_section').slideUp();
            }
        }
        
        // Function to show/hide Dixon schedule section
        function toggleDixonSchedule() {
            var selectedValue = $('input[name="_club420_dixon_visibility"]:checked').val();
            if (selectedValue === 'scheduled') {
                $('#dixon_schedule_section').slideDown();
            } else {
                $('#dixon_schedule_section').slideUp();
            }
        }
        
        // Bind change events to radio buttons
        $('input[name="_club420_davis_visibility"]').change(toggleDavisSchedule);
        $('input[name="_club420_dixon_visibility"]').change(toggleDixonSchedule);
        
        // Show sections if scheduled is already selected (on page load)
        toggleDavisSchedule();
        toggleDixonSchedule();
    });
    </script>
    <?php
}

// FIXED Enhanced save function - handles radio buttons and scheduling CORRECTLY
add_action('woocommerce_process_product_meta', 'club420_save_enhanced_product_fields_with_scheduler');
function club420_save_enhanced_product_fields_with_scheduler($post_id) {
    
    // DAVIS STORE - Save radio button and calculate correct enabled status
    $davis_visibility = isset($_POST['_club420_davis_visibility']) ? sanitize_text_field($_POST['_club420_davis_visibility']) : 'disabled';
    update_post_meta($post_id, '_club420_davis_visibility', $davis_visibility);
    
    // Calculate Davis enabled status based on visibility and schedule
    $davis_enabled = club420_calculate_enabled_status($post_id, 'davis', $davis_visibility, $_POST);
    update_post_meta($post_id, '_club420_davis_enabled', $davis_enabled);
    
    // DIXON STORE - Save radio button and calculate correct enabled status  
    $dixon_visibility = isset($_POST['_club420_dixon_visibility']) ? sanitize_text_field($_POST['_club420_dixon_visibility']) : 'disabled';
    update_post_meta($post_id, '_club420_dixon_visibility', $dixon_visibility);
    
    // Calculate Dixon enabled status based on visibility and schedule
    $dixon_enabled = club420_calculate_enabled_status($post_id, 'dixon', $dixon_visibility, $_POST);
    update_post_meta($post_id, '_club420_dixon_enabled', $dixon_enabled);
    
    // URL FIELDS (unchanged)
    if (isset($_POST['_club420_davis_url'])) {
        update_post_meta($post_id, '_club420_davis_url', sanitize_text_field($_POST['_club420_davis_url']));
    }
    if (isset($_POST['_club420_dixon_url'])) {
        update_post_meta($post_id, '_club420_dixon_url', sanitize_text_field($_POST['_club420_dixon_url']));
    }
    
    // DAVIS SCHEDULE - Only save if 'scheduled' is selected
    if ($davis_visibility === 'scheduled') {
        club420_save_schedule_times($post_id, 'davis', $_POST);
    } else {
        // Clear schedule fields if not using scheduled visibility
        delete_post_meta($post_id, '_club420_davis_schedule_start');
        delete_post_meta($post_id, '_club420_davis_schedule_end');
    }
    
    // DIXON SCHEDULE - Only save if 'scheduled' is selected
    if ($dixon_visibility === 'scheduled') {
        club420_save_schedule_times($post_id, 'dixon', $_POST);
    } else {
        // Clear schedule fields if not using scheduled visibility
        delete_post_meta($post_id, '_club420_dixon_schedule_start');
        delete_post_meta($post_id, '_club420_dixon_schedule_end');
    }
}

/**
 * Calculate the correct enabled status based on visibility setting and current time
 */
function club420_calculate_enabled_status($post_id, $store, $visibility, $post_data) {
    switch ($visibility) {
        case 'always':
            return 'yes';
            
        case 'disabled':
            return 'no';
            
        case 'scheduled':
            // For scheduled products, check if we're within the time window
            $start_key = "_club420_{$store}_schedule_start";
            $end_key = "_club420_{$store}_schedule_end";
            
            // Get the schedule times from the form data
            $start_input = isset($post_data[$start_key]) ? sanitize_text_field($post_data[$start_key]) : '';
            $end_input = isset($post_data[$end_key]) ? sanitize_text_field($post_data[$end_key]) : '';
            
            // If no schedule times provided, disable the product
            if (empty($start_input) || empty($end_input)) {
                return 'no';
            }
            
            try {
                // Convert form times to UTC timestamps
                $wp_timezone = wp_timezone();
                $current_utc = current_time('timestamp', true);
                
                $start_wp = new DateTime($start_input, $wp_timezone);
                $start_utc = $start_wp->getTimestamp();
                
                $end_wp = new DateTime($end_input, $wp_timezone);
                $end_utc = $end_wp->getTimestamp();
                
                // Check if current time is within the scheduled period
                if ($current_utc >= $start_utc && $current_utc <= $end_utc) {
                    return 'yes'; // Within schedule - enable
                } else {
                    return 'no';  // Outside schedule - disable
                }
                
            } catch (Exception $e) {
                error_log("Club420 Schedule: Error calculating enabled status for product {$post_id} {$store}: " . $e->getMessage());
                return 'no'; // Error = disable for safety
            }
            
        default:
            return 'no';
    }
}

/**
 * Save schedule times with timezone conversion
 */
function club420_save_schedule_times($post_id, $store, $post_data) {
    $start_key = "_club420_{$store}_schedule_start";
    $end_key = "_club420_{$store}_schedule_end";
    
    // Save start time with timezone conversion
    if (isset($post_data[$start_key]) && !empty($post_data[$start_key])) {
        $start_input = sanitize_text_field($post_data[$start_key]);
        try {
            $wp_timezone = wp_timezone();
            $start_wp = new DateTime($start_input, $wp_timezone);
            $start_utc = $start_wp->getTimestamp();
            update_post_meta($post_id, $start_key, $start_utc);
        } catch (Exception $e) {
            error_log("Club420 Schedule: Invalid {$store} start date format: " . $start_input);
        }
    }
    
    // Save end time with timezone conversion
    if (isset($post_data[$end_key]) && !empty($post_data[$end_key])) {
        $end_input = sanitize_text_field($post_data[$end_key]);
        try {
            $wp_timezone = wp_timezone();
            $end_wp = new DateTime($end_input, $wp_timezone);
            $end_utc = $end_wp->getTimestamp();
            update_post_meta($post_id, $end_key, $end_utc);
        } catch (Exception $e) {
            error_log("Club420 Schedule: Invalid {$store} end date format: " . $end_input);
        }
    }
}

// Helper function to load and display existing schedule values properly
add_action('admin_footer', 'club420_load_schedule_field_values');
function club420_load_schedule_field_values() {
    global $post, $pagenow;
    
    // Only run on product edit pages
    if ($pagenow !== 'post.php' || !$post || $post->post_type !== 'product') {
        return;
    }
    
    $wp_timezone = wp_timezone();
    
    // Load Davis schedule values and convert from UTC to Los Angeles time for display
    $davis_start_utc = get_post_meta($post->ID, '_club420_davis_schedule_start', true);
    $davis_end_utc = get_post_meta($post->ID, '_club420_davis_schedule_end', true);
    
    echo '<script type="text/javascript">
    jQuery(document).ready(function($) {';
    
    if ($davis_start_utc) {
        $davis_start_wp = new DateTime('@' . $davis_start_utc);
        $davis_start_wp->setTimezone($wp_timezone);
        $davis_start_display = $davis_start_wp->format('Y-m-d\TH:i');
        
        echo '$("#_club420_davis_schedule_start").val("' . esc_js($davis_start_display) . '");';
    }
    
    if ($davis_end_utc) {
        $davis_end_wp = new DateTime('@' . $davis_end_utc);
        $davis_end_wp->setTimezone($wp_timezone);
        $davis_end_display = $davis_end_wp->format('Y-m-d\TH:i');
        
        echo '$("#_club420_davis_schedule_end").val("' . esc_js($davis_end_display) . '");';
    }
    
    // Load Dixon schedule values and convert from UTC to Los Angeles time for display
    $dixon_start_utc = get_post_meta($post->ID, '_club420_dixon_schedule_start', true);
    $dixon_end_utc = get_post_meta($post->ID, '_club420_dixon_schedule_end', true);
    
    if ($dixon_start_utc) {
        $dixon_start_wp = new DateTime('@' . $dixon_start_utc);
        $dixon_start_wp->setTimezone($wp_timezone);
        $dixon_start_display = $dixon_start_wp->format('Y-m-d\TH:i');
        
        echo '$("#_club420_dixon_schedule_start").val("' . esc_js($dixon_start_display) . '");';
    }
    
    if ($dixon_end_utc) {
        $dixon_end_wp = new DateTime('@' . $dixon_end_utc);
        $dixon_end_wp->setTimezone($wp_timezone);
        $dixon_end_display = $dixon_end_wp->format('Y-m-d\TH:i');
        
        echo '$("#_club420_dixon_schedule_end").val("' . esc_js($dixon_end_display) . '");';
    }
    
    echo '});
    </script>';
}
