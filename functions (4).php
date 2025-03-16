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
?>