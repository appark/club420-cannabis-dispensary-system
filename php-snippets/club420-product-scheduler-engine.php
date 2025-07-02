// Snippet 6: Club420 Product Scheduler Dashboard
// Clean Production Version - Real-time scheduling monitoring

class Club420ProductSchedulerDashboard {
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
        // Add dashboard shortcode for monitoring scheduled products
        add_shortcode('club420_scheduler_dashboard', array($this, 'scheduler_dashboard_shortcode'));
        
        // Add admin notice with system status
        add_action('admin_notices', array($this, 'show_system_status'));
    }
    
    /**
     * Scheduler dashboard shortcode
     * Usage: [club420_scheduler_dashboard]
     */
    public function scheduler_dashboard_shortcode($atts) {
        // Only allow admins to run this
        if (!current_user_can('manage_options')) {
            return '<p>Access denied. Admin privileges required.</p>';
        }
        
        ob_start();
        
        echo '<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; font-family: Arial, sans-serif;">';
        echo '<h3 style="margin-top: 0;">🕐 Club420 Real-Time Scheduler Dashboard</h3>';
        
        // Display current time info
        $current_wp_time = current_time('mysql');
        $current_utc_time = current_time('mysql', true);
        $wp_timezone = wp_timezone();
        
        echo '<div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
        echo '<p style="margin: 5px 0;"><strong>Current WordPress Time (Los Angeles):</strong> ' . $current_wp_time . '</p>';
        echo '<p style="margin: 5px 0;"><strong>Current UTC Time:</strong> ' . $current_utc_time . '</p>';
        echo '<p style="margin: 5px 0;"><strong>WordPress Timezone:</strong> ' . $wp_timezone->getName() . '</p>';
        echo '<p style="margin: 5px 0; color: green;"><strong>✅ Real-Time Scheduling:</strong> ACTIVE (instant activation)</p>';
        echo '</div>';
        
        // Show system status
        echo '<div style="background: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745;">';
        echo '<h4 style="margin-top: 0; color: #155724;">📊 System Status</h4>';
        echo '<p style="margin: 5px 0; color: #155724;"><strong>Scheduling Method:</strong> Real-time (instant activation)</p>';
        echo '<p style="margin: 5px 0; color: #155724;"><strong>Cache Management:</strong> WP Engine cache exclusion for scheduled content</p>';
        echo '<p style="margin: 5px 0; color: #155724;"><strong>Manual Toggles:</strong> Working (instant override)</p>';
        echo '<p style="margin: 5px 0; color: #155724;"><strong>Scheduled Products:</strong> Activate/deactivate immediately at set times</p>';
        echo '</div>';
        
        // Show ALL scheduled products with their current status
        $scheduled_products = $this->get_products_with_schedules();
        echo '<h4>Products with Visibility Settings: ' . count($scheduled_products) . '</h4>';
        
        if (!empty($scheduled_products)) {
            echo '<div style="overflow-x: auto; margin-top: 20px;">';
            echo '<table style="border-collapse: collapse; width: 100%; font-size: 12px; background: white;">';
            echo '<thead>';
            echo '<tr style="background: #0073aa; color: white;">';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Product Title</th>';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Davis Settings</th>';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Davis Status</th>';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Dixon Settings</th>';
            echo '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Dixon Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            // Show ALL products
            foreach ($scheduled_products as $product_id) {
                $product = get_post($product_id);
                
                if (!$product) continue;
                
                // Get visibility and enabled status for both stores
                $davis_visibility = get_post_meta($product_id, '_club420_davis_visibility', true);
                $davis_enabled = get_post_meta($product_id, '_club420_davis_enabled', true);
                $dixon_visibility = get_post_meta($product_id, '_club420_dixon_visibility', true);
                $dixon_enabled = get_post_meta($product_id, '_club420_dixon_enabled', true);
                
                // Calculate individual store status
                $davis_status = $this->calculate_store_status($product_id, 'davis', $davis_visibility, $davis_enabled);
                $dixon_status = $this->calculate_store_status($product_id, 'dixon', $dixon_visibility, $dixon_enabled);
                
                // Format settings display
                $davis_settings = $davis_visibility ? $davis_visibility : 'not set';
                $dixon_settings = $dixon_visibility ? $dixon_visibility : 'not set';
                
                // Add schedule times for scheduled products
                if ($davis_visibility === 'scheduled') {
                    $davis_start = get_post_meta($product_id, '_club420_davis_schedule_start', true);
                    $davis_end = get_post_meta($product_id, '_club420_davis_schedule_end', true);
                    if ($davis_start && $davis_end) {
                        $davis_settings .= '<br><small>' . date('m/d H:i', $davis_start) . ' - ' . date('m/d H:i', $davis_end) . '</small>';
                    }
                }
                
                if ($dixon_visibility === 'scheduled') {
                    $dixon_start = get_post_meta($product_id, '_club420_dixon_schedule_start', true);
                    $dixon_end = get_post_meta($product_id, '_club420_dixon_schedule_end', true);
                    if ($dixon_start && $dixon_end) {
                        $dixon_settings .= '<br><small>' . date('m/d H:i', $dixon_start) . ' - ' . date('m/d H:i', $dixon_end) . '</small>';
                    }
                }
                
                echo '<tr style="border-bottom: 1px solid #eee;">';
                echo '<td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">' . $product_id . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; max-width: 200px; word-wrap: break-word;">' . esc_html($product->post_title) . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $davis_settings . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">' . $davis_status . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $dixon_settings . '</td>';
                echo '<td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">' . $dixon_status . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            
            echo '<p style="margin-top: 15px;"><em>Showing all ' . count($scheduled_products) . ' products with scheduling settings.</em></p>';
            echo '<p style="margin-top: 10px; font-size: 11px; color: #666;"><strong>Note:</strong> Status updates in real-time. Refresh page to see latest status.</p>';
        } else {
            echo '<p style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">No products with scheduling settings found. Set up scheduling on products to see them here.</p>';
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }
    
    /**
     * Calculate the current status for a specific store
     */
    private function calculate_store_status($product_id, $store, $visibility, $enabled) {
        $current_utc_time = current_time('timestamp', true);
        
        switch ($visibility) {
            case 'always':
                return '<span style="color: green;">✅ VISIBLE</span>';
                
            case 'disabled':
                return '<span style="color: red;">❌ HIDDEN</span>';
                
            case 'scheduled':
                $start_key = "_club420_{$store}_schedule_start";
                $end_key = "_club420_{$store}_schedule_end";
                $start_time = get_post_meta($product_id, $start_key, true);
                $end_time = get_post_meta($product_id, $end_key, true);
                
                if (!$start_time || !$end_time) {
                    return '<span style="color: orange;">⚠️ NO TIMES SET</span>';
                }
                
                if ($current_utc_time >= $start_time && $current_utc_time <= $end_time) {
                    return '<span style="color: green;">✅ SCHEDULED LIVE</span>';
                } else if ($current_utc_time < $start_time) {
                    $hours_until = round(($start_time - $current_utc_time) / 3600, 1);
                    return '<span style="color: blue;">⏳ STARTS IN ' . $hours_until . 'h</span>';
                } else {
                    return '<span style="color: gray;">⏰ SCHEDULE ENDED</span>';
                }
                
            default:
                // Check manual toggle as fallback
                if ($enabled === 'yes') {
                    return '<span style="color: green;">✅ MANUAL ENABLED</span>';
                } else {
                    return '<span style="color: red;">❌ MANUAL DISABLED</span>';
                }
        }
    }
    
    /**
     * Get all products that have schedule-related meta data
     */
    private function get_products_with_schedules() {
        global $wpdb;
        
        // Find products that have visibility settings (scheduled products)
        $product_ids = $wpdb->get_col("
            SELECT DISTINCT p.ID 
            FROM {$wpdb->posts} p 
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
            WHERE p.post_type = 'product' 
            AND p.post_status = 'publish'
            AND (
                pm.meta_key = '_club420_davis_visibility' OR 
                pm.meta_key = '_club420_dixon_visibility'
            )
        ");
        
        return array_map('intval', $product_ids);
    }
    
    /**
     * Show system status in admin (only on products page)
     */
    public function show_system_status() {
        global $pagenow, $typenow;
        if ($pagenow !== 'edit.php' || $typenow !== 'product' || !current_user_can('manage_options')) {
            return;
        }
        
        echo '<div class="notice notice-success"><p><strong>Club420 Real-Time Scheduler:</strong> ✅ Active! Products show/hide instantly based on schedules. Use <code>[club420_scheduler_dashboard]</code> shortcode to monitor status.</p></div>';
    }
}

// Initialize the dashboard
new Club420ProductSchedulerDashboard();
