<?php
/**
 * WooCommerce Account Customizations
 * 
 * Customizes the WooCommerce My Account area for Consent Denied
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add tabs to WooCommerce My Account
 */
function cd_add_my_account_tabs($menu_links) {
    // Remove Downloads tab
    if (isset($menu_links['downloads'])) {
        unset($menu_links['downloads']);
    }
    
    // Add our custom tabs
    $new_links = array();
    
    foreach ($menu_links as $key => $label) {
        $new_links[$key] = $label;
        
        // Add Legal Address tab after Dashboard
        if ($key === 'dashboard') {
            $new_links['legal-address'] = 'My Legal Address';
            $new_links['vehicles'] = 'My Vehicles';
        }
    }
    
    return $new_links;
}
add_filter('woocommerce_account_menu_items', 'cd_add_my_account_tabs');

/**
 * Add endpoints for custom tabs
 */
function cd_add_account_endpoints() {
    add_rewrite_endpoint('legal-address', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('vehicles', EP_ROOT | EP_PAGES);
}
add_action('init', 'cd_add_account_endpoints');

/**
 * Add content to the Legal Address tab
 */
function cd_legal_address_content() {
    $user_id = get_current_user_id();
    $message = '';
    
    // Process form submission
    if (isset($_POST['update_legal_address'])) {
        // Update user meta with legal address
        update_user_meta($user_id, 'cd_address_street', sanitize_text_field($_POST['address_street']));
        update_user_meta($user_id, 'cd_address_line2', sanitize_text_field($_POST['address_line2']));
        update_user_meta($user_id, 'cd_address_city', sanitize_text_field($_POST['address_city']));
        update_user_meta($user_id, 'cd_address_postcode', sanitize_text_field($_POST['address_postcode']));
        
        $message = '<div class="woocommerce-message">Your legal address has been updated successfully.</div>';
        
        // Trigger notification emails to operators if needed
        if (function_exists('cd_get_email_manager') && class_exists('CD_Vehicle_Manager')) {
            $vehicle_manager = new CD_Vehicle_Manager();
            $vehicles = $vehicle_manager->get_user_vehicles($user_id);
            
            if (!empty($vehicles)) {
                $email_manager = cd_get_email_manager();
                
                foreach ($vehicles as $vehicle) {
                    $email_manager->send_notices_to_all_operators($user_id, $vehicle->id);
                }
                
                $message .= '<div class="woocommerce-message">Address update notifications have been sent to all parking operators.</div>';
            }
        }
    }
    
    // Get current values
    $address_street = get_user_meta($user_id, 'cd_address_street', true);
    $address_line2 = get_user_meta($user_id, 'cd_address_line2', true);
    $address_city = get_user_meta($user_id, 'cd_address_city', true);
    $address_postcode = get_user_meta($user_id, 'cd_address_postcode', true);
    
    // Display form
    ?>
    <div class="cd-legal-address">
        <h3>My Legal Address</h3>
        <p class="woocommerce-info">
            This is your legal residential address that will be shared with parking operators. 
            This address must be your current residential address. If you change address, 
            please update it here to ensure all parking operators are notified.
        </p>
        
        <?php echo $message; ?>
        
        <form method="post" class="woocommerce-EditAccountForm edit-account">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="address_street">Street Address <span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" 
                       name="address_street" id="address_street" value="<?php echo esc_attr($address_street); ?>" required>
            </p>
            
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="address_line2">Address Line 2</label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" 
                       name="address_line2" id="address_line2" value="<?php echo esc_attr($address_line2); ?>">
            </p>
            
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="address_city">City/Town <span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" 
                       name="address_city" id="address_city" value="<?php echo esc_attr($address_city); ?>" required>
            </p>
            
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="address_postcode">Postcode <span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" 
                       name="address_postcode" id="address_postcode" value="<?php echo esc_attr($address_postcode); ?>" required>
            </p>
            
            <p>
                <button type="submit" class="woocommerce-Button button" name="update_legal_address" value="Save address">
                    Save address
                </button>
            </p>
        </form>
    </div>
    <?php
}
add_action('woocommerce_account_legal-address_endpoint', 'cd_legal_address_content');

/**
 * Initialize legal address from billing address if it doesn't exist
 */
function cd_initialize_legal_address() {
    if (is_account_page() && is_wc_endpoint_url('legal-address')) {
        $user_id = get_current_user_id();
        
        // Only initialize if legal address doesn't exist
        if (!get_user_meta($user_id, 'cd_address_street', true)) {
            // Copy from billing or other existing fields
            $street = get_user_meta($user_id, 'billing_address_1', true);
            if (!$street) {
                $street = get_user_meta($user_id, 'address_street', true);
            }
            
            if ($street) {
                update_user_meta($user_id, 'cd_address_street', $street);
                
                // Copy other fields if street exists
                $line2 = get_user_meta($user_id, 'billing_address_2', true) ?: 
                         get_user_meta($user_id, 'address_line2', true);
                
                $city = get_user_meta($user_id, 'billing_city', true) ?: 
                        get_user_meta($user_id, 'address_city', true);
                
                $postcode = get_user_meta($user_id, 'billing_postcode', true) ?: 
                            get_user_meta($user_id, 'address_postcode', true);
                
                update_user_meta($user_id, 'cd_address_line2', $line2);
                update_user_meta($user_id, 'cd_address_city', $city);
                update_user_meta($user_id, 'cd_address_postcode', $postcode);
            }
        }
    }
}
add_action('template_redirect', 'cd_initialize_legal_address');

/**
 * Add content to the Vehicle Management tab
 */
function cd_vehicles_endpoint_content() {
    echo do_shortcode('[cd_vehicle_management]');
}
add_action('woocommerce_account_vehicles_endpoint', 'cd_vehicles_endpoint_content');

/**
 * Add styles for vehicle management in My Account
 */
function cd_vehicle_management_styles() {
    if (is_account_page()) {
        ?>
        <style>
            .cd-vehicle-management {
                margin: 0;
                padding: 0;
            }
            .cd-vehicle-management .button {
                line-height: 1;
                cursor: pointer;
                border: 0;
                padding: 0.6180469716em 1.41575em;
                text-decoration: none;
                font-weight: 600;
                text-shadow: none;
                display: inline-block;
                border-radius: 3px;
            }
            .cd-vehicles-table {
                margin-bottom: 2em;
            }
            .cd-edit-vehicle-form .form-row {
                display: flex;
                gap: 1em;
                margin-bottom: 1em;
            }
            .cd-edit-vehicle-form label {
                flex: 1;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'cd_vehicle_management_styles');

/**
 * Flush rewrite rules to activate endpoints
 */
function cd_flush_account_rewrite_rules() {
    cd_add_account_endpoints();
    flush_rewrite_rules();
}

/**
 * Debug vehicle data on the vehicles endpoint
 */
function cd_debug_vehicle_data() {
    if (is_account_page() && is_wc_endpoint_url('vehicles')) {
        $user_id = get_current_user_id();
        
        echo '<div style="background: #f8f8f8; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd;">';
        echo '<h3>Debug Information</h3>';
        
        // Check database directly
        global $wpdb;
        $vehicles = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cd_vehicles 
            WHERE user_id = %d",
            $user_id
        ));
        
        echo '<p>Vehicles in database: ' . count($vehicles) . '</p>';
        
        // Check user meta for vehicle data
        $registration = get_user_meta($user_id, 'car-reg', true);
        $make = get_user_meta($user_id, 'car-make', true);
        $model = get_user_meta($user_id, 'car-model', true);
        $color = get_user_meta($user_id, 'car-color', true);
        
        echo '<p>Meta data found:</p>';
        echo '<ul>';
        echo '<li>Registration: ' . ($registration ? $registration : 'Not found') . '</li>';
        echo '<li>Make: ' . ($make ? $make : 'Not found') . '</li>';
        echo '<li>Model: ' . ($model ? $model : 'Not found') . '</li>';
        echo '<li>Color: ' . ($color ? $color : 'Not found') . '</li>';
        echo '</ul>';
        
        // Check order meta for vehicle data
        $orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        
        if (!empty($orders)) {
            $order = $orders[0];
            $order_id = $order->get_id();
            
            echo '<p>Order meta data:</p>';
            echo '<ul>';
            echo '<li>Order ID: ' . $order_id . '</li>';
            echo '<li>Registration: ' . get_post_meta($order_id, '_cd_car-reg', true) . '</li>';
            echo '<li>Make: ' . get_post_meta($order_id, '_cd_car-make', true) . '</li>';
            echo '<li>Model: ' . get_post_meta($order_id, '_cd_car-model', true) . '</li>';
            echo '<li>Color: ' . get_post_meta($order_id, '_cd_car-color', true) . '</li>';
            echo '</ul>';
        } else {
            echo '<p>No orders found for this user.</p>';
        }
        
        echo '</div>';
    }
}
add_action('woocommerce_account_vehicles_endpoint', 'cd_debug_vehicle_data', 5);

/**
 * Add test vehicle for specific user
 */
function cd_add_test_vehicle() {
    // Only run this once
    if (get_option('cd_test_vehicle_added')) {
        return;
    }
    
    // Find the test user
    $test_user = get_user_by('login', 'test_user_2000');
    if (!$test_user) {
        return;
    }
    
    $user_id = $test_user->ID;
    
    // Add a test vehicle
    global $wpdb;
    $result = $wpdb->insert(
        $wpdb->prefix . 'cd_vehicles',
        array(
            'user_id' => $user_id,
            'registration_number' => 'TEST123',
            'make' => 'Test Make',
            'model' => 'Test Model',
            'color' => 'Test Color',
            'active' => 1
        ),
        array('%d', '%s', '%s', '%s', '%s', '%d')
    );
    
    if ($result) {
        update_option('cd_test_vehicle_added', true);
    }
}
add_action('init', 'cd_add_test_vehicle');

/**
 * Process vehicle data from order meta after order completion
 */
function cd_process_vehicle_data_after_order_completion($order_id) {
    if (!$order_id) {
        return;
    }
    
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    $user_id = $order->get_customer_id();
    if (!$user_id) {
        return;
    }
    
    // Get vehicle data from order meta
    $registration = get_post_meta($order_id, '_cd_car-reg', true);
    $make = get_post_meta($order_id, '_cd_car-make', true);
    $model = get_post_meta($order_id, '_cd_car-model', true);
    $color = get_post_meta($order_id, '_cd_car-color', true);
    
    // If any of these are missing, try user meta
    if (!$registration || !$make || !$model || !$color) {
        $registration = $registration ?: get_user_meta($user_id, 'car-reg', true);
        $make = $make ?: get_user_meta($user_id, 'car-make', true);
        $model = $model ?: get_user_meta($user_id, 'car-model', true);
        $color = $color ?: get_user_meta($user_id, 'car-color', true);
    }
    
    // If we have the vehicle data, add it to the cd_vehicles table
    if ($registration && $make && $model && $color) {
        // Check if vehicle already exists
        global $wpdb;
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cd_vehicles 
             WHERE user_id = %d AND registration_number = %s AND active = 1",
            $user_id, $registration
        ));
        
        if (!$exists) {
            // Add the vehicle
            $wpdb->insert(
                $wpdb->prefix . 'cd_vehicles',
                array(
                    'user_id' => $user_id,
                    'registration_number' => strtoupper(sanitize_text_field($registration)),
                    'make' => sanitize_text_field($make),
                    'model' => sanitize_text_field($model),
                    'color' => sanitize_text_field($color),
                    'active' => 1
                ),
                array('%d', '%s', '%s', '%s', '%s', '%d')
            );
            
            // Log success
            error_log("Added vehicle for user $user_id: $registration, $make, $model, $color");
        }
    }
}
add_action('woocommerce_order_status_completed', 'cd_process_vehicle_data_after_order_completion');
add_action('woocommerce_order_status_processing', 'cd_process_vehicle_data_after_order_completion');

/**
 * Import vehicle data from order meta
 */
function cd_import_vehicle_from_orders() {
    if (is_account_page() && is_wc_endpoint_url('vehicles')) {
        $user_id = get_current_user_id();
        
        // Get vehicle count for user
        global $wpdb;
        $existing_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cd_vehicles 
            WHERE user_id = %d AND active = 1",
            $user_id
        ));
        
        // Only proceed if user has no vehicles yet
        if ($existing_count == 0) {
            // Get user's orders
            $orders = wc_get_orders(array(
                'customer_id' => $user_id,
                'status' => array('completed', 'processing'),
                'limit' => -1,
            ));
            
            $vehicle_found = false;
            
            foreach ($orders as $order) {
                $order_id = $order->get_id();
                
                // Try to get vehicle data from order meta
                $reg = get_post_meta($order_id, '_cd_car-reg', true);
                $make = get_post_meta($order_id, '_cd_car-make', true);
                $model = get_post_meta($order_id, '_cd_car-model', true);
                $color = get_post_meta($order_id, '_cd_car-color', true);
                
                // If no vehicle data in order meta, try sessionStorage data saved in cookie
                if (empty($reg) && isset($_COOKIE['cd_registration_data'])) {
                    $registration_data = json_decode(stripslashes($_COOKIE['cd_registration_data']), true);
                    if (is_array($registration_data)) {
                        $reg = isset($registration_data['car-reg']) ? $registration_data['car-reg'] : '';
                        $make = isset($registration_data['car-make']) ? $registration_data['car-make'] : '';
                        $model = isset($registration_data['car-model']) ? $registration_data['car-model'] : '';
                        $color = isset($registration_data['car-color']) ? $registration_data['car-color'] : '';
                    }
                }
                
                // As a fallback, check for data in user meta
                if (empty($reg)) {
                    $reg = get_user_meta($user_id, 'car-reg', true);
                    $make = get_user_meta($user_id, 'car-make', true);
                    $model = get_user_meta($user_id, 'car-model', true);
                    $color = get_user_meta($user_id, 'car-color', true);
                }
                
                // If we found vehicle data, add it to the database
                if (!empty($reg) && !empty($make) && !empty($model) && !empty($color)) {
                    $wpdb->insert(
                        $wpdb->prefix . 'cd_vehicles',
                        array(
                            'user_id' => $user_id,
                            'registration_number' => strtoupper(sanitize_text_field($reg)),
                            'make' => sanitize_text_field($make),
                            'model' => sanitize_text_field($model),
                            'color' => sanitize_text_field($color),
                            'active' => 1
                        ),
                        array('%d', '%s', '%s', '%s', '%s', '%d')
                    );
                    
                    $vehicle_found = true;
                    break;  // Stop after adding one vehicle
                }
            }
            
            if ($vehicle_found) {
                // Redirect to refresh the page after adding the vehicle
                wp_redirect(wc_get_account_endpoint_url('vehicles'));
                exit;
            }
        }
    }
}
add_action('template_redirect', 'cd_import_vehicle_from_orders', 5);

/**
 * Store vehicle data during checkout
 */
function cd_store_vehicle_data_on_checkout($order_id) {
    if (!$order_id) return;
    
    // Check for data in session storage via JavaScript
    ?>
    <script>
    (function() {
        var registrationData = sessionStorage.getItem('cd_registration_data');
        if (registrationData) {
            // Create a form to post the data back to the server
            var form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cd_vehicle_data';
            input.value = registrationData;
            
            var orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'cd_order_id';
            orderInput.value = '<?php echo $order_id; ?>';
            
            form.appendChild(input);
            form.appendChild(orderInput);
            document.body.appendChild(form);
            
            form.submit();
        }
    })();
    </script>
    <?php
}
add_action('woocommerce_thankyou', 'cd_store_vehicle_data_on_checkout');

/**
 * Process vehicle data submission
 */
function cd_process_vehicle_data_submission() {
    if (isset($_POST['cd_vehicle_data']) && isset($_POST['cd_order_id'])) {
        $order_id = intval($_POST['cd_order_id']);
        $vehicle_data = json_decode(stripslashes($_POST['cd_vehicle_data']), true);
        
        if (is_array($vehicle_data)) {
            // Store vehicle data in order meta
            update_post_meta($order_id, '_cd_car-reg', sanitize_text_field($vehicle_data['car-reg']));
            update_post_meta($order_id, '_cd_car-make', sanitize_text_field($vehicle_data['car-make']));
            update_post_meta($order_id, '_cd_car-model', sanitize_text_field($vehicle_data['car-model']));
            update_post_meta($order_id, '_cd_car-color', sanitize_text_field($vehicle_data['car-color']));
            
            // Also store in user meta for good measure
            $order = wc_get_order($order_id);
            if ($order) {
                $user_id = $order->get_customer_id();
                if ($user_id) {
                    update_user_meta($user_id, 'car-reg', sanitize_text_field($vehicle_data['car-reg']));
                    update_user_meta($user_id, 'car-make', sanitize_text_field($vehicle_data['car-make']));
                    update_user_meta($user_id, 'car-model', sanitize_text_field($vehicle_data['car-model']));
                    update_user_meta($user_id, 'car-color', sanitize_text_field($vehicle_data['car-color']));
                    
                    // Add to vehicles table directly
                    global $wpdb;
                    $wpdb->insert(
                        $wpdb->prefix . 'cd_vehicles',
                        array(
                            'user_id' => $user_id,
                            'registration_number' => strtoupper(sanitize_text_field($vehicle_data['car-reg'])),
                            'make' => sanitize_text_field($vehicle_data['car-make']),
                            'model' => sanitize_text_field($vehicle_data['car-model']),
                            'color' => sanitize_text_field($vehicle_data['car-color']),
                            'active' => 1
                        ),
                        array('%d', '%s', '%s', '%s', '%s', '%d')
                    );
                }
            }
        }
    }
}
add_action('init', 'cd_process_vehicle_data_submission');

/**
 * Diagnose subscription detection issues
 */
function cd_diagnose_subscription_status() {
    if (is_account_page() && is_wc_endpoint_url('vehicles')) {
        $user_id = get_current_user_id();
        
        echo '<div style="background: #f8f8f8; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd;">';
        echo '<h3>Subscription Status Diagnosis</h3>';
        
        // Get subscription product IDs
        $basic_id = get_option('cd_basic_subscription_id', '846');
        $premium_id = get_option('cd_premium_subscription_id', '847');
        
        echo '<p>Basic Subscription ID Setting: ' . $basic_id . '</p>';
        echo '<p>Premium Subscription ID Setting: ' . $premium_id . '</p>';
        
        // Show user's orders and products
        $orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'status' => array('completed', 'processing'),
        ));
        
        echo '<p>Orders found: ' . count($orders) . '</p>';
        
        if (count($orders) > 0) {
            echo '<ul>';
            foreach ($orders as $order) {
                $order_id = $order->get_id();
                $status = $order->get_status();
                
                echo '<li>Order #' . $order_id . ' (Status: ' . $status . ')';
                
                // Show order items
                $items = $order->get_items();
                if (count($items) > 0) {
                    echo '<ul>';
                    foreach ($items as $item) {
                        $product_id = $item->get_product_id();
                        $product_name = $item->get_name();
                        
                        echo '<li>Product: ' . $product_name . ' (ID: ' . $product_id . ')</li>';
                    }
                    echo '</ul>';
                }
                
                echo '</li>';
            }
            echo '</ul>';
        }
        
        // Check if existing debug functions are working
        echo '<p>Checking if debug functions are active...</p>';
        
        // Check the vehicle manager's methods directly
        if (class_exists('CD_Vehicle_Manager')) {
            $vehicle_manager = new CD_Vehicle_Manager();
            
            echo '<p>Vehicle Manager class found!</p>';
            echo '<p>is_premium_subscriber result: ' . ($vehicle_manager->is_premium_subscriber($user_id) ? 'Yes' : 'No') . '</p>';
            echo '<p>is_basic_subscriber result: ' . ($vehicle_manager->is_basic_subscriber($user_id) ? 'Yes' : 'No') . '</p>';
            echo '<p>get_subscription_status result: ' . $vehicle_manager->get_subscription_status($user_id) . '</p>';
            echo '<p>can_add_more_vehicles result: ' . ($vehicle_manager->can_add_more_vehicles($user_id) ? 'Yes' : 'No') . '</p>';
            
            // Show the function code
            echo '<p>Function implementations in vehicle manager:</p>';
            
            $reflection = new ReflectionMethod('CD_Vehicle_Manager', 'is_premium_subscriber');
            $filename = $reflection->getFileName();
            $start_line = $reflection->getStartLine();
            $end_line = $reflection->getEndLine();
            
            $file = new SplFileObject($filename);
            $file->seek($start_line - 1);
            
            echo '<pre style="background: #eee; padding: 10px; overflow: auto; max-height: 300px;">';
            for ($i = 0; $i < $end_line - $start_line + 1; $i++) {
                echo htmlspecialchars($file->current());
                $file->next();
            }
            echo '</pre>';
        } else {
            echo '<p>Vehicle Manager class not found!</p>';
        }
        
        // Check where shortcode content appears
        echo '<p>Shortcode execution point: ' . did_action('woocommerce_account_vehicles_endpoint') . '</p>';
        
        echo '</div>';
    }
}
add_action('woocommerce_account_vehicles_endpoint', 'cd_diagnose_subscription_status', 1);

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
