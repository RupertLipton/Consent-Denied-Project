<?php
/**
 * Astra Child Theme functions and definitions
 */

// Enqueue child theme styles
function astra_child_enqueue_styles() {
    wp_enqueue_style(
        'astra-child-theme-css',
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-theme-css'),
        wp_get_theme()->get('Version') . '.' . time() // Force cache refresh
    );
}
add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles', 15);

/**
 * Add custom styles for Consent Denied home page
 */
function consent_denied_custom_styles() {
    if (is_front_page() || is_home()) {
        echo '<style>
            /* Enhanced typography */
            .entry-content h1 {
                font-size: 2.5rem;
                color: #d40000;
                margin-bottom: 1.5rem;
                border-bottom: 3px solid #d40000;
                padding-bottom: 10px;
            }
            
            /* Highlight the NO CONSENT message */
            .entry-content p strong {
                color: #d40000;
            }
            
            /* Create a prominent message box */
            .no-consent-message {
                font-size: 1.4rem;
                font-weight: bold;
                background-color: #f8f0f0;
                padding: 15px;
                border-left: 5px solid #d40000;
                margin-bottom: 25px;
            }
            
            /* Enhance bullet points */
            .entry-content ul {
                background-color: #f5f5f5;
                padding: 20px 20px 20px 40px;
                border-radius: 8px;
                margin-bottom: 30px;
            }
            
            .entry-content ul li {
                margin-bottom: 12px;
                position: relative;
                list-style-type: none;
            }
            
            .entry-content ul li:before {
                content: "✓";
                color: #d40000;
                font-weight: bold;
                position: absolute;
                left: -25px;
            }
            
            /* Style the form container */
            .form-container {
                background-color: #f0f4f8;
                padding: 25px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin: 30px 0;
            }
            
            .form-field {
                margin-bottom: 15px;
            }
            
            .form-field label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            .form-field input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            
            /* Style the subscription options */
            .subscription-options {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin: 30px 0;
            }
            
            .subscription-option {
                flex .subscription-option {
                flex: 1;
                min-width: 250px;
                background-color: #fff;
                border: 2px solid #1a365d;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .subscription-option:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }
            
            .subscription-option.core {
                border-color: #3182ce;
            }
            
            .subscription-option.enhanced {
                border-color: #d40000;
            }
            
            .subscription-option.selected {
                background-color: #1a365d;
                color: white;
            }
            
            .subscription-option h3 {
                margin-top: 0;
                color: #1a365d;
                font-size: 1.3rem;
            }
            
            .subscription-option.selected h3 {
                color: white;
            }
            
            /* Style the paragraphs in the explainer section */
            .explainer-section p {
                line-height: 1.8;
                margin-bottom: 1.2rem;
            }
            
            /* Call to action button */
            .action-button {
                display: inline-block;
                background-color: #d40000;
                color: white !important;
                font-weight: bold;
                padding: 12px 30px;
                border-radius: 5px;
                text-decoration: none;
                margin-top: 20px;
                text-align: center;
                transition: background-color 0.3s ease;
                border: none;
                font-size: 1rem;
                cursor: pointer;
            }
            
            .action-button:hover {
                background-color: #b30000;
            }
            
            /* Style for disabled buttons */
            .action-button.disabled, .subscribe-button.disabled {
                background-color: #cccccc !important;
                color: #666666 !important;
                cursor: not-allowed;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .subscription-options {
                    flex-direction: column;
                }
                
                .no-consent-message {
                    font-size: 1.2rem;
                }
            }
        </style>';
    }
}
add_action('wp_head', 'consent_denied_custom_styles');

// Prevent caching for logged-in users
function disable_cache_for_logged_in() {
    if (is_user_logged_in()) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
    }
}
add_action('send_headers', 'disable_cache_for_logged_in');

// Helper function for debugging
function cd_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

// Enqueue off-canvas menu
function cd_enqueue_off_canvas_menu() {
    // Enqueue CSS
    wp_enqueue_style(
        'cd-off-canvas-menu-css',
        get_stylesheet_directory_uri() . '/off-canvas-menu.css',
        [],
        wp_get_theme()->get('Version')
    );

    // Enqueue JavaScript
    wp_enqueue_script(
        'cd-off-canvas-menu-js',
        get_stylesheet_directory_uri() . '/off-canvas-menu.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'cd_enqueue_off_canvas_menu');

// Enqueue form validation script
function cd_enqueue_form_validation_script() {
    // Only enqueue on the front page
    if (is_front_page()) {
        wp_enqueue_script(
            'cd-form-validation',
            get_stylesheet_directory_uri() . '/form-validation.js',
            [],
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'cd_enqueue_form_validation_script');

// Remove WooCommerce notices on the checkout page
function remove_wc_notices_on_checkout() {
    if (is_checkout()) {
        remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
    }
}
add_action('wp', 'remove_wc_notices_on_checkout');

/**
 * Clear WooCommerce session and cart data for non-logged-in users on the front page
 */
function clear_woocommerce_session_on_front_page() {
    if (is_front_page() && !is_user_logged_in()) {
        // Clear WooCommerce session data
        if (function_exists('WC') && WC()->session) {
            WC()->session->destroy_session();
        }
        
        // Clear WooCommerce cart
        if (function_exists('WC') && WC()->cart) {
            WC()->cart->empty_cart();
        }
        
        // Unset WooCommerce cart cookies
        if (isset($_COOKIE['woocommerce_cart_hash'])) {
            unset($_COOKIE['woocommerce_cart_hash']);
            setcookie('woocommerce_cart_hash', '', time() - 3600, '/');
        }
        
        if (isset($_COOKIE['woocommerce_items_in_cart'])) {
            unset($_COOKIE['woocommerce_items_in_cart']);
            setcookie('woocommerce_items_in_cart', '', time() - 3600, '/');
        }
    }
}
add_action('wp', 'clear_woocommerce_session_on_front_page');

/**
 * Handle form data on checkout page
 * This function will retrieve registration data from JavaScript and save it to WooCommerce order meta
 */
function cd_save_registration_data_to_order($order_id) {
    // Only proceed if we have registration data in the cookie
    if (isset($_COOKIE['cd_registration_data'])) {
        $registration_data = json_decode(stripslashes($_COOKIE['cd_registration_data']), true);
        
        if (!empty($registration_data)) {
            // Save registration data to order meta
            foreach ($registration_data as $key => $value) {
                update_post_meta($order_id, '_cd_' . $key, sanitize_text_field($value));
            }
            
            // Also save a flag indicating this order includes registration data
            update_post_meta($order_id, '_cd_has_registration', 'yes');
            
            // Clear the cookie
            setcookie('cd_registration_data', '', time() - 3600, '/');
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'cd_save_registration_data_to_order');

/**
 * Add JavaScript to checkout page to retrieve session data
 */
function cd_add_registration_data_to_checkout() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                // Check if we have registration data in session storage
                const registrationData = sessionStorage.getItem('cd_registration_data');
                if (registrationData) {
                    console.log('Registration data found in session storage');
                    
                    // Create a cookie with the registration data
                    document.cookie = "cd_registration_data=" + registrationData + "; path=/";
                    
                    // Parse the data
                    const data = JSON.parse(registrationData);
                    
                    // Pre-fill checkout fields if possible
                    if (data.name && data.name.includes(' ')) {
                        const nameParts = data.name.split(' ');
                        const firstName = nameParts.shift();
                        const lastName = nameParts.join(' ');
                        
                        // Set billing name fields
                        const billingFirstName = document.getElementById('billing_first_name');
                        const billingLastName = document.getElementById('billing_last_name');
                        
                        if (billingFirstName) billingFirstName.value = firstName;
                        if (billingLastName) billingLastName.value = lastName;
                    }
                    
                    // Direct field mappings
                    const fieldMappings = {
                        'email': 'billing_email',
                        'address1': 'billing_address_1',
                        'address2': 'billing_address_2',
                        'city': 'billing_city',
                        'state': 'billing_state',
                        'postcode': 'billing_postcode',
                        'mobile': 'billing_phone'
                    };
                    
                    // Apply mappings
                    for (const [sourceField, targetField] of Object.entries(fieldMappings)) {
                        if (data[sourceField]) {
                            const targetElement = document.getElementById(targetField);
                            if (targetElement) targetElement.value = data[sourceField];
                        }
                    }
                    
                    // Set country to UK
                    const countryField = document.getElementById('billing_country');
                    if (countryField) countryField.value = 'GB';
                    
                    // Trigger change events to ensure WooCommerce updates its state
                    document.querySelectorAll('input, select').forEach(element => {
                        const event = new Event('change', { bubbles: true });
                        element.dispatchEvent(event);
                    });
                    
                    console.log('Checkout fields pre-filled with registration data');
                }
            });
        </script>
        <?php
    }
}
add_action('wp_head', 'cd_add_registration_data_to_checkout');

/**
 * Process the registration data after order is completed
 */
function cd_process_registration_after_order($order_id) {
    // Check if this order has registration data
    $has_registration = get_post_meta($order_id, '_cd_has_registration', true);
    
    if ($has_registration !== 'yes') {
        return;
    }
    
    // Get the order
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    
    // Get customer ID
    $user_id = $order->get_customer_id();
    
    // If no user exists yet, we may need to create one
    if (!$user_id) {
        $user_email = $order->get_billing_email();
        
        // Check if user exists with this email
        $user = get_user_by('email', $user_email);
        
        if ($user) {
            $user_id = $user->ID;
        } else {
            // Create new user
            $username = sanitize_user(current(explode('@', $user_email)), true);
            $counter = 1;
            $new_username = $username;
            
            // Ensure username is unique
            while (username_exists($new_username)) {
                $new_username = $username . $counter;
                $counter++;
            }
            
            // Generate random password
            $random_password = wp_generate_password(12, false);
            
            // Create user
            $user_id = wp_create_user($new_username, $random_password, $user_email);
            
            if (!is_wp_error($user_id)) {
                // Set role
                $user = new WP_User($user_id);
                $user->set_role('customer');
                
                // Send notification
                wp_new_user_notification($user_id, null, 'user');
            } else {
                // Log error
                error_log('Failed to create user: ' . $user_id->get_error_message());
                return;
            }
        }
    }
    
    // Save address meta data to custom user fields
    update_user_meta($user_id, 'address_line1', get_post_meta($order_id, '_cd_address1', true));
    update_user_meta($user_id, 'address_line2', get_post_meta($order_id, '_cd_address2', true));
    update_user_meta($user_id, 'address_city', get_post_meta($order_id, '_cd_city', true));
    update_user_meta($user_id, 'address_state', get_post_meta($order_id, '_cd_state', true));
    update_user_meta($user_id, 'address_postcode', get_post_meta($order_id, '_cd_postcode', true));
    
    // Copy to standard WooCommerce fields as well
    update_user_meta($user_id, 'billing_address_1', get_post_meta($order_id, '_cd_address1', true));
    update_user_meta($user_id, 'billing_address_2', get_post_meta($order_id, '_cd_address2', true));
    update_user_meta($user_id, 'billing_city', get_post_meta($order_id, '_cd_city', true));
    update_user_meta($user_id, 'billing_state', get_post_meta($order_id, '_cd_state', true));
    update_user_meta($user_id, 'billing_postcode', get_post_meta($order_id, '_cd_postcode', true));
    update_user_meta($user_id, 'billing_country', 'GB');
    update_user_meta($user_id, 'billing_phone', get_post_meta($order_id, '_cd_mobile', true));
    
    // Get vehicle details from order meta
    $registration = get_post_meta($order_id, '_cd_car-reg', true);
    $make = get_post_meta($order_id, '_cd_car-make', true);
    $model = get_post_meta($order_id, '_cd_car-model', true);
    $color = get_post_meta($order_id, '_cd_car-color', true);
    
    // Save user meta
    if (!empty($registration) && !empty($make) && !empty($model) && !empty($color)) {
        // Add the vehicle using the vehicle manager
        if (class_exists('CD_Vehicle_Manager')) {
            $vehicle_manager = new CD_Vehicle_Manager();
            
            $vehicle_data = array(
                'registration_number' => $registration,
                'make' => $make,
                'model' => $model,
                'color' => $color
            );
            
            $vehicle_manager->add_vehicle($user_id, $vehicle_data);
            
            // Send notices to parking operators
            if (function_exists('cd_get_email_manager')) {
                // Get the vehicle ID
                global $wpdb;
                $vehicle_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}cd_vehicles 
                     WHERE user_id = %d AND registration_number = %s",
                    $user_id, $registration
                ));
                
                if ($vehicle_id) {
                    $email_manager = cd_get_email_manager();
                    $email_manager->send_notices_to_all_operators($user_id, $vehicle_id);
                }
            }
        }
    }
    
    // Mark registration as processed
    update_post_meta($order_id, '_cd_registration_processed', 'yes');
}

add_action('woocommerce_order_status_completed', 'cd_process_registration_after_order');
// Also trigger on processing for paid orders
add_action('woocommerce_order_status_processing', 'cd_process_registration_after_order');

function debug_template_being_used() {
    if (is_front_page()) {
        global $template;
        echo '<!-- Current template being used: ' . $template . ' -->';
    }
}
add_action('wp_head', 'debug_template_being_used');