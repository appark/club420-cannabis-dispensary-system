// Club420 Carousel Store Filter - REAL-TIME SCHEDULING + CACHE EXCLUSION
// Clean Production Version

add_filter('woocommerce_shortcode_products_query', 'club420_realtime_filter_with_scheduling', 10, 2);
function club420_realtime_filter_with_scheduling($args, $atts) {
    
    // SKIP filtering if this is from our custom carousel
    if (isset($args['club420_carousel_query']) && $args['club420_carousel_query'] === true) {
        return $args; // Let carousel handle its own filtering
    }
    
    $store_location = isset($_GET['store_filter']) ? sanitize_text_field($_GET['store_filter']) : 'all';
    
    if ($store_location !== 'all') {
        if (!isset($args['meta_query'])) {
            $args['meta_query'] = array();
        }
        
        // Set relation to OR - product shows if EITHER manual toggle OR schedule is active
        $args['meta_query']['relation'] = 'OR';
        
        if ($store_location === 'davis') {
            
            // OPTION 1: Manual toggle enabled (original system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_davis_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_davis_enabled',
                    'value' => 'yes',
                    'compare' => '='
                )
            );
            
            // OPTION 2: Scheduled and currently active (NEW real-time system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_davis_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_davis_visibility',
                    'value' => 'scheduled',
                    'compare' => '='
                ),
                array(
                    'key' => '_club420_davis_schedule_start',
                    'value' => current_time('timestamp', true), // Current UTC timestamp
                    'compare' => '<='
                ),
                array(
                    'key' => '_club420_davis_schedule_end',
                    'value' => current_time('timestamp', true), // Current UTC timestamp
                    'compare' => '>='
                )
            );
            
            // OPTION 3: Always visible (from Snippet 6 system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_davis_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_davis_visibility',
                    'value' => 'always',
                    'compare' => '='
                )
            );
            
        } elseif ($store_location === 'dixon') {
            
            // OPTION 1: Manual toggle enabled (original system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_dixon_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_dixon_enabled',
                    'value' => 'yes',
                    'compare' => '='
                )
            );
            
            // OPTION 2: Scheduled and currently active (NEW real-time system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_dixon_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_dixon_visibility',
                    'value' => 'scheduled',
                    'compare' => '='
                ),
                array(
                    'key' => '_club420_dixon_schedule_start',
                    'value' => current_time('timestamp', true), // Current UTC timestamp
                    'compare' => '<='
                ),
                array(
                    'key' => '_club420_dixon_schedule_end',
                    'value' => current_time('timestamp', true), // Current UTC timestamp
                    'compare' => '>='
                )
            );
            
            // OPTION 3: Always visible (from Snippet 6 system)
            $args['meta_query'][] = array(
                'relation' => 'AND',
                array(
                    'key' => '_club420_dixon_url',
                    'value' => '',
                    'compare' => '!='
                ),
                array(
                    'key' => '_club420_dixon_visibility',
                    'value' => 'always',
                    'compare' => '='
                )
            );
        }
        
        // WP ENGINE CACHE BUSTING: Add timestamp to force fresh queries
        if (!isset($args['meta_query']['cache_buster'])) {
            $args['meta_query']['cache_buster'] = array(
                'key' => '_club420_cache_timestamp',
                'value' => floor(current_time('timestamp') / 300), // Changes every 5 minutes
                'compare' => 'NOT EXISTS'
            );
        }
    }
    
    return $args;
}

// WP ENGINE OPTIMIZATION: Clear object cache when products are saved
add_action('save_post_product', 'club420_clear_product_cache');
function club420_clear_product_cache($post_id) {
    // Clear WP Engine object cache for this product
    clean_post_cache($post_id);
    
    // Also clear any WooCommerce transients
    wc_delete_product_transients($post_id);
}

// Keep the conditional loading prep from previous optimization
function club420_conditional_scripts() {
    if (is_front_page() || is_shop() || is_product_category() || is_product()) {
        add_action('wp_footer', 'club420_load_optimized_scripts', 20);
    }
}
add_action('wp', 'club420_conditional_scripts');

function club420_load_optimized_scripts() {
    echo '<script>window.club420LoadOptimized=true;</script>';
}

// WP ENGINE CACHE EXCLUSION - Exclude pages with scheduled products from cache
add_action('wp', 'club420_bypass_cache_for_scheduled_products', 5);
function club420_bypass_cache_for_scheduled_products() {
    // Only check on pages that show product carousels
    if (!is_front_page() && !is_shop() && !is_product_category()) {
        return;
    }
    
    // Skip for logged-in users (they get fresh data anyway)
    if (is_user_logged_in()) {
        return;
    }
    
    global $wpdb;
    
    // Check if ANY products have active schedules RIGHT NOW
    $current_utc_time = current_time('timestamp', true);
    
    $active_scheduled = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) 
        FROM {$wpdb->postmeta} pm1
        INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
        INNER JOIN {$wpdb->postmeta} pm3 ON pm1.post_id = pm3.post_id
        WHERE (pm1.meta_key = '_club420_davis_visibility' OR pm1.meta_key = '_club420_dixon_visibility')
        AND pm1.meta_value = 'scheduled'
        AND ((pm2.meta_key = '_club420_davis_schedule_start' OR pm2.meta_key = '_club420_dixon_schedule_start'))
        AND ((pm3.meta_key = '_club420_davis_schedule_end' OR pm3.meta_key = '_club420_dixon_schedule_end'))
        AND pm2.meta_value <= %s
        AND pm3.meta_value >= %s
    ", $current_utc_time, $current_utc_time));
    
    if ($active_scheduled > 0) {
        // BYPASS WP ENGINE CACHE - This page has active scheduled products
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // WP Engine specific headers
            if (defined('WPE_APIKEY') || function_exists('wpe_param')) {
                header('X-Cache-Control: no-cache');
                header('X-WPE-Cache-Control: no-cache');
            }
        }
    }
}
