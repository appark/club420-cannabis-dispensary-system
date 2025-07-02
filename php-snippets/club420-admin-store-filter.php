
// CLUB420 Admin Store Filter - Inline Dropdown Version
// Replace the previous "CLUB420 Admin Store Filter" snippet with this

// Add store filter dropdown inline with other filters
add_action('restrict_manage_posts', 'club420_add_inline_store_filter');
function club420_add_inline_store_filter() {
    global $typenow;
    
    // Only show on products page
    if ($typenow == 'product') {
        $selected = isset($_GET['store_filter_admin']) ? $_GET['store_filter_admin'] : '';
        
        // Create dropdown that matches WooCommerce styling
        echo '<select name="store_filter_admin" id="store_filter_admin" class="wc-enhanced-select" style="width: 200px;">';
        echo '<option value="">Filter by store</option>';
        echo '<option value="davis_only"' . selected($selected, 'davis_only', false) . '>Davis Store Only</option>';
        echo '<option value="dixon_only"' . selected($selected, 'dixon_only', false) . '>Dixon Store Only</option>';
        echo '<option value="both_stores"' . selected($selected, 'both_stores', false) . '>Both Stores</option>';
        echo '<option value="neither_store"' . selected($selected, 'neither_store', false) . '>Neither Store</option>';
        echo '</select>';
    }
}

// Filter products based on store selection
add_filter('parse_query', 'club420_filter_products_by_store');
function club420_filter_products_by_store($query) {
    global $pagenow, $typenow;
    
    // Only apply on products admin page
    if ($pagenow == 'edit.php' && $typenow == 'product' && isset($_GET['store_filter_admin']) && $_GET['store_filter_admin'] != '') {
        
        $store_filter = $_GET['store_filter_admin'];
        
        // Initialize meta query
        if (!isset($query->query_vars['meta_query'])) {
            $query->query_vars['meta_query'] = array();
        }
        
        switch ($store_filter) {
            case 'davis_only':
                // Davis enabled, Dixon disabled or not set
                $query->query_vars['meta_query'] = array(
                    'relation' => 'AND',
                    array(
                        'key' => '_club420_davis_enabled',
                        'value' => 'yes',
                        'compare' => '='
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_club420_dixon_enabled',
                            'value' => 'yes',
                            'compare' => '!='
                        ),
                        array(
                            'key' => '_club420_dixon_enabled',
                            'compare' => 'NOT EXISTS'
                        )
                    )
                );
                break;
                
            case 'dixon_only':
                // Dixon enabled, Davis disabled or not set
                $query->query_vars['meta_query'] = array(
                    'relation' => 'AND',
                    array(
                        'key' => '_club420_dixon_enabled',
                        'value' => 'yes',
                        'compare' => '='
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_club420_davis_enabled',
                            'value' => 'yes',
                            'compare' => '!='
                        ),
                        array(
                            'key' => '_club420_davis_enabled',
                            'compare' => 'NOT EXISTS'
                        )
                    )
                );
                break;
                
            case 'both_stores':
                // Both Davis AND Dixon enabled
                $query->query_vars['meta_query'] = array(
                    'relation' => 'AND',
                    array(
                        'key' => '_club420_davis_enabled',
                        'value' => 'yes',
                        'compare' => '='
                    ),
                    array(
                        'key' => '_club420_dixon_enabled', 
                        'value' => 'yes',
                        'compare' => '='
                    )
                );
                break;
                
            case 'neither_store':
                // Neither Davis nor Dixon enabled
                $query->query_vars['meta_query'] = array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_club420_davis_enabled',
                            'value' => 'yes',
                            'compare' => '!='
                        ),
                        array(
                            'key' => '_club420_davis_enabled',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => '_club420_dixon_enabled',
                            'value' => 'yes', 
                            'compare' => '!='
                        ),
                        array(
                            'key' => '_club420_dixon_enabled',
                            'compare' => 'NOT EXISTS'
                        )
                    )
                );
                break;
        }
    }
}

// Add store status columns to products list 
add_filter('manage_edit-product_columns', 'club420_add_store_columns');
function club420_add_store_columns($columns) {
    // Add store columns after stock status
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Add store columns after stock column
        if ($key == 'product_tag') {
            $new_columns['davis_store'] = 'Davis';
            $new_columns['dixon_store'] = 'Dixon';
        }
    }
    
    return $new_columns;
}

// Display store status in columns
add_action('manage_product_posts_custom_column', 'club420_display_store_columns', 10, 2);
function club420_display_store_columns($column, $post_id) {
    switch ($column) {
        case 'davis_store':
            $enabled = get_post_meta($post_id, '_club420_davis_enabled', true);
            if ($enabled == 'yes') {
                echo '<span style="color: green; font-weight: bold;">✓</span>';
            } else {
                echo '<span style="color: #ccc;">✗</span>';
            }
            break;
            
        case 'dixon_store':
            $enabled = get_post_meta($post_id, '_club420_dixon_enabled', true);
            if ($enabled == 'yes') {
                echo '<span style="color: green; font-weight: bold;">✓</span>';
            } else {
                echo '<span style="color: #ccc;">✗</span>';
            }
            break;
    }
}
