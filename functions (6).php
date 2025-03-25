<?php

require_once get_stylesheet_directory() . '/debug-flow.php';

function cd_debug_sessions_init() {
    if (isset($_GET['add-to-cart']) || is_checkout()) {
        $log_file = WP_CONTENT_DIR . '/debug_sessions.txt';
        $message = date('[Y-m-d H:i:s] [INIT] ') . 'URL: ' . $_SERVER['REQUEST_URI'] . "\n";
        
        if (isset($_GET['add-to-cart'])) {
            $message .= 'GET add-to-cart: ' . $_GET['add-to-cart'] . "\n";
        }
        
        if (function_exists('WC') && WC()->session) {
            $message .= 'WC SESSION: YES' . "\n";
            $message .= 'SESSION ID: ' . WC()->session->get_customer_id() . "\n";
        } else {
            $message .= 'WC SESSION: NO' . "\n";
        }
        
        $message .= '-----------------' . "\n";
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}
add_action('init', 'cd_debug_sessions_init', 999);

function cd_debug_sessions_wp() {
    if (isset($_GET['add-to-cart']) || is_checkout()) {
        $log_file = WP_CONTENT_DIR . '/debug_sessions.txt';
        $message = date('[Y-m-d H:i:s] [WP] ') . 'URL: ' . $_SERVER['REQUEST_URI'] . "\n";
        
        if (function_exists('WC') && WC()->session) {
            $message .= 'WC SESSION: YES' . "\n";
            $message .= 'SESSION ID: ' . WC()->session->get_customer_id() . "\n";
            $message .= 'CART COUNT: ' . (function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 'N/A') . "\n";
        } else {
            $message .= 'WC SESSION: NO' . "\n";
        }
        
        $message .= '-----------------' . "\n";
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}
add_action('wp', 'cd_debug_sessions_wp', 999);

function cd_debug_sessions_template() {
    if (isset($_GET['add-to-cart']) || is_checkout()) {
        $log_file = WP_CONTENT_DIR . '/debug_sessions.txt';
        $message = date('[Y-m-d H:i:s] [TEMPLATE] ') . 'URL: ' . $_SERVER['REQUEST_URI'] . "\n";
        
        if (function_exists('WC') && WC()->session) {
            $message .= 'WC SESSION: YES' . "\n";
            $message .= 'SESSION ID: ' . WC()->session->get_customer_id() . "\n";
            $message .= 'CART COUNT: ' . (function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 'N/A') . "\n";
        } else {
            $message .= 'WC SESSION: NO' . "\n";
        }
        
        $message .= '-----------------' . "\n";
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}
add_action('template_redirect', 'cd_debug_sessions_template', 999);

function cd_debug_sessions_footer() {
    if (isset($_GET['add-to-cart']) || is_checkout()) {
        $log_file = WP_CONTENT_DIR . '/debug_sessions.txt';
        $message = date('[Y-m-d H:i:s] [FOOTER] ') . 'URL: ' . $_SERVER['REQUEST_URI'] . "\n";
        
        if (function_exists('WC') && WC()->session) {
            $message .= 'WC SESSION: YES' . "\n";
            $message .= 'SESSION ID: ' . WC()->session->get_customer_id() . "\n";
            $message .= 'CART COUNT: ' . (function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 'N/A') . "\n";
        } else {
            $message .= 'WC SESSION: NO' . "\n";
        }
        
        $message .= '-----------------' . "\n";
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}
add_action('wp_footer', 'cd_debug_sessions_footer', 999);

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
    // Don't clear session if we're trying to add to cart
    if (isset($_GET['add-to-cart']) || isset($_POST['add-to-cart'])) {
        return;
    }
    
    // Don't clear session if there's an active cart
    if (function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) {
        return;
    }
    
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

function debug_template_being_used() {
    if (is_front_page()) {
        global $template;
        echo '<!-- Current template being used: ' . $template . ' -->';
    }
}
add_action('wp_head', 'debug_template_being_used');

/**
 * Add JavaScript to hide the added-to-cart message on checkout
 */
function cd_hide_added_to_cart_message() {
    if (is_checkout() && isset($_GET['add-to-cart'])) {
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Find and remove any success messages
            var notices = document.querySelectorAll('.woocommerce-message');
            notices.forEach(function(notice) {
                if (notice.textContent.indexOf('has been added to your basket') !== -1) {
                    notice.style.display = 'none';
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'cd_hide_added_to_cart_message');

// Include WooCommerce account customizations
require_once get_stylesheet_directory() . '/includes/woocommerce-account-customizations.php';

// Register activation hook for flushing rewrite rules
function cd_theme_activation() {
    require_once get_stylesheet_directory() . '/includes/woocommerce-account-customizations.php';
    cd_flush_account_rewrite_rules();
}
add_action('after_switch_theme', 'cd_theme_activation');

// Add a debug info shortcode
function debug_info_shortcode() {
    ob_start();
    ?>
    <div style="max-width: 800px; margin: 40px auto; padding: 20px; background: #fff; border: 1px solid #ddd;">
        <h1>Debug Information</h1>
        
        <?php
        // Basic WordPress info
        echo '<h2>Basic Info</h2>';
        echo '<p>User ID: ' . get_current_user_id() . '</p>';
        
        // Check if Vehicle Manager class exists
        echo '<h2>Classes</h2>';
        echo '<p>Vehicle Manager exists: ' . (class_exists('CD_Vehicle_Manager') ? 'Yes' : 'No') . '</p>';
        
        // Check WooCommerce
        echo '<h2>WooCommerce</h2>';
        echo '<p>WooCommerce active: ' . (class_exists('WooCommerce') ? 'Yes' : 'No') . '</p>';
        
        // Check database tables
        echo '<h2>Database Tables</h2>';
        global $wpdb;
        $table = $wpdb->prefix . 'cd_vehicles';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        echo '<p>' . $table . ' exists: ' . ($table_exists ? 'Yes' : 'No') . '</p>';
        
        // Direct database query
        if ($table_exists) {
            $user_id = get_current_user_id();
            $vehicles = $wpdb->get_results("SELECT * FROM $table WHERE user_id = $user_id");
            echo '<p>Vehicles found: ' . count($vehicles) . '</p>';
        }
        
        // Subscription product IDs
        echo '<h2>Settings</h2>';
        echo '<p>Basic subscription ID: ' . get_option('cd_basic_subscription_id', 'Not set') . '</p>';
        echo '<p>Premium subscription ID: ' . get_option('cd_premium_subscription_id', 'Not set') . '</p>';
        
        if (function_exists('wcs_get_users_subscriptions')) {
            $user_id = get_current_user_id();
            $subscriptions = wcs_get_users_subscriptions($user_id);
            
            echo '<h2>WooCommerce Subscriptions</h2>';
            echo '<p>Subscriptions found: ' . count($subscriptions) . '</p>';
            
            if (!empty($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    echo '<p>Subscription #' . $subscription->get_id() . ' - Status: ' . $subscription->get_status() . '</p>';
                    echo '<p>Products:</p><ul>';
                    foreach ($subscription->get_items() as $item) {
                        echo '<li>' . $item->get_name() . ' (ID: ' . $item->get_product_id() . ')</li>';
                    }
                    echo '</ul>';
                }
            }
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('debug_info', 'debug_info_shortcode');

function cd_hide_database_errors() {
    if (!current_user_can('manage_options') && !is_admin()) {
        global $wpdb;
        $wpdb->hide_errors();
    }
}
add_action('init', 'cd_hide_database_errors');

/**
 * Remove Order Notes field from checkout
 */
function cd_remove_order_notes($fields) {
    unset($fields['order']['order_comments']);
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cd_remove_order_notes', 10);

/**
 * Ensure payment save checkbox is visible
 */
function cd_ensure_payment_save_visible() {
    if (is_checkout()) {
        ?>
        <style>
            /* Make sure the payment checkbox is visible */
            .woocommerce-SavedPaymentMethods-saveNew,
            #wc-stripe-new-payment-method,
            #wc-stripe-new-payment-method + label,
            .wc-payment-form label {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'cd_ensure_payment_save_visible');

/**
 * Debug checkout errors
 */
function cd_log_checkout_errors($errors) {
    if (!empty($errors->errors)) {
        error_log('WooCommerce Checkout Errors: ' . print_r($errors->errors, true));
    }
    return $errors;
}
add_filter('woocommerce_checkout_validation_errors', 'cd_log_checkout_errors');

/**
 * Add terms and agency checkboxes right before the place order button
 */
function cd_add_checkout_checkboxes() {
    // Terms and conditions checkbox
    woocommerce_form_field('terms_download', array(
        'type'          => 'checkbox',
        'class'         => array('form-row-wide custom-checkout-checkbox'),
        'label'         => 'I confirm that I have read the <a href="' . get_permalink(get_page_by_path('terms-&-conditions')) . '" target="_blank">terms and conditions</a>',
        'required'      => true,
    ));
    
    // Agency authorization checkbox
    woocommerce_form_field('agency_authorization', array(
        'type'          => 'checkbox',
        'class'         => array('form-row-wide custom-checkout-checkbox'),
        'label'         => 'I authorise Consent Denied Ltd as my agent for the sole purpose of sending communications to parking operators during the period of my subscription. <a href="' . get_permalink(get_page_by_path('faqs')) . '#agency-authorization" target="_blank">Why is this needed?</a>',
        'required'      => true,
    ));
}
add_action('woocommerce_review_order_before_submit', 'cd_add_checkout_checkboxes', 10);

/**
 * Validate the custom checkout fields
 */
function cd_validate_checkout_fields() {
    if (!isset($_POST['terms_download']) || empty($_POST['terms_download'])) {
        wc_add_notice('Please confirm that you have read the terms and conditions.', 'error');
    }
    
    if (!isset($_POST['agency_authorization']) || empty($_POST['agency_authorization'])) {
        wc_add_notice('Please authorize Consent Denied Ltd as your agent for communications.', 'error');
    }
}
add_action('woocommerce_checkout_process', 'cd_validate_checkout_fields');

/**
 * Add custom CSS for checkout elements
 */
function cd_checkout_custom_css() {
    if (is_checkout()) {
        ?>
        <style>
            /* Custom checkout checkboxes styling */
            .custom-checkout-checkbox {
                margin: 15px 0;
                padding: 10px;
                background-color: #f8f8f8;
                border-left: 3px solid #d40000;
            }
            
            .custom-checkout-checkbox label,
            .woocommerce-checkout .custom-checkout-checkbox label {
                font-size: 0.8em !important; 
                font-weight: normal !important;
            }
            
            .custom-checkout-checkbox a {
                color: #d40000;
                text-decoration: underline;
            }
            
            /* Better checkbox styling */
            .custom-checkout-checkbox input[type="checkbox"] {
                margin-right: 8px;
                transform: scale(1.2);
            }
            
            /* Ensure payment method checkbox is visible and properly styled */
            .woocommerce-SavedPaymentMethods-saveNew label,
            #wc-stripe-new-payment-method + label,
            .wc-payment-form label {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                font-size: 0.8em !important;
                font-weight: normal !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'cd_checkout_custom_css');

/**
 * Change the Place Order button text
 */
function cd_custom_order_button_text($button_text) {
    return 'Pay for Subscription';
}
add_filter('woocommerce_order_button_text', 'cd_custom_order_button_text');

/**
 * Modify the "Save payment information" text
 */
function cd_modify_save_payment_text($text) {
    return 'Allow Consent Denied Ltd to auto-renew my subscription on expiry in accordance with their then current Terms & Conditions and for the payment provider to save my card details for this purpose.';
}
add_filter('woocommerce_save_to_account_text', 'cd_modify_save_payment_text');

/**
 * Ensure payment save text appears and is styled correctly
 */
function cd_ensure_payment_text() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to modify the payment text
            function updatePaymentText() {
                // Target Stripe and other gateways
                $('input[id$="save-new-payment-method"]').each(function() {
                    var label = $(this).next('label');
                    if (label.length > 0) {
                        label.html('Allow Consent Denied Ltd to auto-renew my subscription on expiry in accordance with their then current Terms & Conditions and for the payment provider to save my card details for this purpose.');
                        label.css({
                            'font-size': '0.8em',
                            'font-weight': 'normal'
                        });
                    }
                });
            }
            
            // Run on page load
            updatePaymentText();
            
            // Run again after a short delay to catch elements loaded via AJAX
            setTimeout(updatePaymentText, 1000);
            
            // Also run when payment methods change
            $(document.body).on('updated_checkout payment_method_selected', function() {
                setTimeout(updatePaymentText, 500);
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'cd_ensure_payment_text');

/**
 * Target WooPayments checkout field
 */
function cd_woopayments_save_text_fix() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to modify WooPayments text
            function updateWoopaymentsText() {
                // Target WooPayments specific selectors
                $('input#wc-woocommerce_payments-new-payment-method, input.woocommerce-SavedPaymentMethods-saveNew').each(function() {
                    var checkbox = $(this);
                    
                    // Show the checkbox
                    checkbox.show();
                    
                    // Find label - try different methods since WooPayments can structure differently
                    var label = $('label[for="' + checkbox.attr('id') + '"]');
                    if (label.length === 0) {
                        label = checkbox.next('label');
                    }
                    if (label.length === 0) {
                        label = checkbox.parent().find('label');
                    }
                    
                    if (label.length > 0) {
                        label.html('Allow Consent Denied Ltd to auto-renew my subscription on expiry in accordance with their then current Terms & Conditions and for the payment provider to save my card details for this purpose.');
                        label.css({
                            'display': 'block',
                            'visibility': 'visible',
                            'font-size': '0.8em',
                            'font-weight': 'normal'
                        });
                    }
                });
            }
            
            // Run on page load
            setTimeout(updateWoopaymentsText, 1000);
            
            // Also run when checkout is updated
            $(document.body).on('updated_checkout payment_method_selected', function() {
                setTimeout(updateWoopaymentsText, 500);
            });
            
            // Also watch for dynamic changes to the payment area
            var targetNode = document.getElementById('payment');
            if (targetNode) {
                var observer = new MutationObserver(function() {
                    updateWoopaymentsText();
                });
                observer.observe(targetNode, { childList: true, subtree: true });
            }
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'cd_woopayments_save_text_fix');

/**
 * Filter for WooPayments save text
 */
function cd_woopayments_save_text($text) {
    return 'Allow Consent Denied Ltd to auto-renew my subscription on expiry in accordance with their then current Terms & Conditions and for the payment provider to save my card details for this purpose.';
}
add_filter('woocommerce_payments_save_payment_method_text', 'cd_woopayments_save_text');
add_filter('woocommerce_gateway_woocommerce_payments_save_payment_method_text', 'cd_woopayments_save_text');

/**
 * Comprehensive checkout field diagnostics
 */
function cd_checkout_field_diagnostics() {
    if (!is_checkout()) {
        return;
    }
    
    // Log information to the browser console
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        console.log('=== CHECKOUT FIELD DIAGNOSTICS ===');
        
        // Function to log all checkout form fields
        function logCheckoutFields() {
            console.log('Checkout form fields at ' + new Date().toISOString());
            
            // Log all input elements in the checkout form
            var checkoutInputs = $('form.checkout').find('input, select, textarea');
            console.log('Total form elements found: ' + checkoutInputs.length);
            
            // Log all checkboxes specifically
            var checkboxes = $('form.checkout').find('input[type="checkbox"]');
            console.log('Checkboxes found: ' + checkboxes.length);
            
            // Log details of each checkbox
            checkboxes.each(function(index) {
                var checkbox = $(this);
                var id = checkbox.attr('id') || 'no-id';
                var name = checkbox.attr('name') || 'no-name';
                var isVisible = checkbox.is(':visible');
                var label = $('label[for="' + id + '"]');
                var labelText = label.length ? label.text().trim() : 'No label found';
                
                console.log('Checkbox #' + index + ':');
                console.log('  ID: ' + id);
                console.log('  Name: ' + name);
                console.log('  Visible: ' + isVisible);
                console.log('  Label text: ' + labelText);
                console.log('  Parent elements:');
                
                // Log parent structure to diagnose potential hiding
                var parents = checkbox.parents().slice(0, 5); // First 5 parents
                parents.each(function(i) {
                    var el = $(this);
                    console.log('    Parent ' + i + ': ' + el.prop('tagName') + 
                                ' (class="' + el.attr('class') + '", ' +
                                'display=' + el.css('display') + ', ' +
                                'visibility=' + el.css('visibility') + ')');
                });
            });
        }
        
        // Initial log
        setTimeout(logCheckoutFields, 2000);
        
        // Log again after checkout updates
        $(document.body).on('updated_checkout', function() {
            console.log('=== CHECKOUT UPDATED ===');
            setTimeout(logCheckoutFields, 1000);
        });
    });
    </script>
    <?php
    
    // Server-side logging
    if (WP_DEBUG) {
        error_log('Checkout page loaded at ' . date('Y-m-d H:i:s'));
        
        // Log applied filters
        $save_text_filters = [
            'woocommerce_save_to_account_text',
            'wc_stripe_save_to_account_text',
            'woocommerce_gateway_stripe_save_payment_method_text',
            'woocommerce_payments_save_payment_method_text',
            'woocommerce_gateway_woocommerce_payments_save_payment_method_text'
        ];
        
        foreach ($save_text_filters as $filter) {
            $callbacks = has_filter($filter) ? 'Has callbacks' : 'No callbacks';
            error_log("Filter: $filter - $callbacks");
        }
    }
}
add_action('wp_footer', 'cd_checkout_field_diagnostics');

/**
 * Restore payment method save checkbox
 */
function cd_restore_payment_save_checkbox() {
    // We'll only run this on checkout
    if (!is_checkout()) {
        return;
    }
    
    // Remove any potential filters that might interfere
    remove_all_filters('woocommerce_payment_gateways_settings');
    
    // Ensure proper hooks are running
    add_filter('woocommerce_payment_gateways_settings', function($settings) {
        if (WP_DEBUG) {
            error_log('Payment gateway settings: ' . print_r($settings, true));
        }
        return $settings;
    });
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Wait for page to fully load
        setTimeout(function() {
            console.log("Attempting to restore payment save checkbox...");
            
            // Find the payment methods container
            var paymentMethods = $('.woocommerce-checkout-payment');
            console.log("Payment methods found:", paymentMethods.length);
            
            // Check if there are script elements that might be modifying it
            var scripts = paymentMethods.find('script');
            console.log("Scripts in payment area:", scripts.length);
            
            // Look specifically for the save checkbox that might be hidden
            var saveCheckboxes = $('input[name="wc-woocommerce_payments-new-payment-method"], ' + 
                                   'input[name="wc-payment-method-new-payment-method"]');
            console.log("Potential save checkboxes found:", saveCheckboxes.length);
            
            // Try to find any hidden checkboxes
            $('form.checkout').find('input[type="checkbox"]').each(function() {
                var checkbox = $(this);
                var isVisible = checkbox.is(':visible');
                var isHidden = checkbox.css('display') === 'none' || 
                               checkbox.parent().css('display') === 'none' ||
                               checkbox.css('visibility') === 'hidden';
                
                console.log("Found checkbox:", checkbox.attr('name'), 
                            "Visible:", isVisible, 
                            "Hidden:", isHidden);
                
                // Is this possibly our missing payment save checkbox?
                if (!isVisible || isHidden) {
                    if (checkbox.attr('name') && (
                        checkbox.attr('name').includes('payment-method') || 
                        checkbox.attr('name').includes('save')
                    )) {
                        console.log("Found potentially hidden payment save checkbox:", checkbox.attr('name'));
                        
                        // Make it visible
                        checkbox.css({
                            'display': 'inline-block',
                            'visibility': 'visible',
                            'opacity': '1'
                        });
                        
                        checkbox.parent().css({
                            'display': 'block',
                            'visibility': 'visible',
                            'opacity': '1'
                        });
                        
                        // Remove any inline styles that might hide it
                        checkbox.removeAttr('style');
                        checkbox.parent().removeAttr('style');
                        
                        console.log("Attempted to restore visibility");
                    }
                }
            });
        }, 1500);
    });
    </script>
    <?php
}
add_action('wp_footer', 'cd_restore_payment_save_checkbox');

/**
 * Add custom payment save checkbox
 */
function cd_add_custom_payment_save_checkbox() {
    // Add the checkbox right before the Place Order button
    woocommerce_form_field('custom_payment_save', array(
        'type'          => 'checkbox',
        'class'         => array('form-row-wide custom-payment-checkbox'),
        'label'         => 'Allow Consent Denied Ltd to auto-renew my subscription on expiry in accordance with their then current Terms & Conditions and for the payment provider to save my card details for this purpose.',
        'required'      => false,
    ));
}
add_action('woocommerce_review_order_before_submit', 'cd_add_custom_payment_save_checkbox', 9);

/**
 * Add custom styling for the payment checkbox
 */
function cd_custom_payment_checkbox_css() {
    if (is_checkout()) {
        ?>
        <style>
            .custom-payment-checkbox {
                margin: 15px 0;
                padding: 10px;
                background-color: #f8f8f8;
                border-left: 3px solid #d40000;
            }
            
            .custom-payment-checkbox label {
                font-size: 0.8em !important; 
                font-weight: normal !important;
            }
            
            .custom-payment-checkbox input[type="checkbox"] {
                margin-right: 8px;
                transform: scale(1.2);
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'cd_custom_payment_checkbox_css');

/**
 * Remove Additional Information heading from checkout
 */
function cd_remove_additional_info_heading($fields) {
    // Remove the heading by adding an empty string as the heading
    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = '';
    }
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cd_remove_additional_info_heading', 11);

/**
 * Hide Additional Information heading with CSS as well
 */
function cd_hide_additional_info_heading() {
    if (is_checkout()) {
        ?>
        <style>
            /* Hide the Additional Information heading */
            .woocommerce-additional-fields h3,
            .woocommerce-additional-fields__field-wrapper > h3 {
                display: none !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'cd_hide_additional_info_heading');

/**
 * Customize login form labels - alternative approach
 */
function cd_custom_login_labels() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Change the label for the username field
            var userLabel = document.querySelector('label[for="user_login"]');
            if (userLabel) {
                userLabel.innerHTML = userLabel.innerHTML.replace('Username or Email Address', 'Email Address');
            }
            
            // Change the placeholder text
            var userInput = document.getElementById('user_login');
            if (userInput) {
                userInput.placeholder = 'Email Address';
            }
        });
    </script>
    <?php
}
add_action('login_head', 'cd_custom_login_labels');

/**
 * Add instructions above the login form
 */
function cd_login_message($message) {
    if (empty($message)) {
        return '<p class="message">Please log in with your email address.</p>';
    }
    return $message;
}
add_filter('login_message', 'cd_login_message');

/**
 * Customize login form with CSS
 */
function cd_custom_login_css() {
    ?>
    <style type="text/css">
        /* Hide the original label text */
        label[for="user_login"] {
            position: relative;
            font-size: 0 !important; /* Make original text invisible */
        }
        
        /* Add our own label text */
        label[for="user_login"]::before {
            content: 'Email Address';
            font-size: 14px !important; /* Restore normal font size */
            position: absolute;
            left: 0;
            top: 0;
        }
        
        /* Change the placeholder text */
        #user_login::placeholder {
            content: 'Email Address';
        }
    </style>
    <?php
}
add_action('login_head', 'cd_custom_login_css');

function cd_login_test() {
    echo "<!-- Login customization code is running -->";
}
add_action('login_head', 'cd_login_test');