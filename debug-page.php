/**
 * Diagnose using wp_footer hook which should always run
 */
function cd_diagnose_from_footer() {
    if (is_account_page()) {
        $current_endpoint = WC()->query->get_current_endpoint();
        
        echo '<!-- Current endpoint: ' . $current_endpoint . ' -->';
        
        if ($current_endpoint == 'vehicles') {
            echo '<div style="background: #f8f8f8; margin: 20px; padding: 15px; border: 1px solid #ddd;">';
            echo '<h3>Debug Information (from footer)</h3>';
            
            $user_id = get_current_user_id();
            echo '<p>User ID: ' . $user_id . '</p>';
            
            // Check if shortcode exists
            echo '<p>Shortcode exists: ' . (shortcode_exists('cd_vehicle_management') ? 'Yes' : 'No') . '</p>';
            
            // Check if class exists
            echo '<p>Vehicle Manager class exists: ' . (class_exists('CD_Vehicle_Manager') ? 'Yes' : 'No') . '</p>';
            
            // Check hooks
            echo '<p>Hooks for vehicles endpoint: ' . (has_action('woocommerce_account_vehicles_endpoint') ? 'Yes' : 'No') . '</p>';
            
            if (class_exists('CD_Vehicle_Manager')) {
                $vehicle_manager = new CD_Vehicle_Manager();
                echo '<p>Subscription status: ' . $vehicle_manager->get_subscription_status($user_id) . '</p>';
                
                // Get vehicles table
                global $wpdb;
                $table_name = $wpdb->prefix . 'cd_vehicles';
                $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
                
                echo '<p>Vehicles table exists: ' . ($table_exists ? 'Yes' : 'No') . '</p>';
                
                if ($table_exists) {
                    $vehicles = $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM $table_name WHERE user_id = %d AND active = 1",
                        $user_id
                    ));
                    
                    echo '<p>Vehicles found: ' . count($vehicles) . '</p>';
                }
            }
            
            // Get subscription product IDs
            $basic_id = get_option('cd_basic_subscription_id', '');
            $premium_id = get_option('cd_premium_subscription_id', '');
            
            echo '<p>Basic subscription ID: ' . $basic_id . '</p>';
            echo '<p>Premium subscription ID: ' . $premium_id . '</p>';
            
            // Orders
            $orders = wc_get_orders(array(
                'customer_id' => $user_id,
                'status' => array('completed', 'processing'),
            ));
            
            echo '<p>Orders found: ' . count($orders) . '</p>';
            
            if (count($orders) > 0) {
                echo '<ul>';
                foreach ($orders as $order) {
                    $order_id = $order->get_id();
                    $items = $order->get_items();
                    $products = [];
                    
                    foreach ($items as $item) {
                        $products[] = $item->get_product_id();
                    }
                    
                    echo '<li>Order #' . $order_id . ' - Products: ' . implode(', ', $products) . '</li>';
                }
                echo '</ul>';
            }
            
            echo '</div>';
        }
    }
}
add_action('wp_footer', 'cd_diagnose_from_footer');