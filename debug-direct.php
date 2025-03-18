<?php
/**
 * Template Name: Direct Debug
 */

// Disable output buffering to ensure we see output
if (ob_get_level()) ob_end_clean();

// Set content type to plain text for easier reading
header('Content-Type: text/plain');

echo "DEBUG INFORMATION\n";
echo "=================\n\n";

// Basic WordPress info
echo "BASIC INFO\n";
echo "User ID: " . get_current_user_id() . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n\n";

// Check if Vehicle Manager class exists
echo "CLASSES\n";
echo "Vehicle Manager exists: " . (class_exists('CD_Vehicle_Manager') ? 'Yes' : 'No') . "\n";
if (class_exists('CD_Vehicle_Manager')) {
    echo "Creating Vehicle Manager instance...\n";
    $vehicle_manager = new CD_Vehicle_Manager();
    echo "Instance created.\n";
    $user_id = get_current_user_id();
    echo "Getting subscription status for user ID $user_id...\n";
    $status = $vehicle_manager->get_subscription_status($user_id);
    echo "Subscription status: $status\n\n";
}

// Check WooCommerce
echo "WOOCOMMERCE\n";
echo "WooCommerce active: " . (class_exists('WooCommerce') ? 'Yes' : 'No') . "\n";
if (class_exists('WooCommerce')) {
    echo "WooCommerce version: " . WC()->version . "\n";
}
echo "\n";

// Check database tables
echo "DATABASE TABLES\n";
global $wpdb;
$table = $wpdb->prefix . 'cd_vehicles';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
echo "$table exists: " . ($table_exists ? 'Yes' : 'No') . "\n";

// Direct database query
if ($table_exists) {
    $user_id = get_current_user_id();
    $vehicles = $wpdb->get_results("SELECT * FROM $table WHERE user_id = $user_id");
    echo "Vehicles found: " . count($vehicles) . "\n";
    if (count($vehicles) > 0) {
        foreach ($vehicles as $v) {
            echo "- {$v->registration_number} - {$v->make} {$v->model} ({$v->color})\n";
        }
    }
}
echo "\n";

// Subscription product IDs
echo "SETTINGS\n";
echo "Basic subscription ID: " . get_option('cd_basic_subscription_id', 'Not set') . "\n";
echo "Premium subscription ID: " . get_option('cd_premium_subscription_id', 'Not set') . "\n\n";

// WooCommerce Subscriptions
echo "WOOCOMMERCE SUBSCRIPTIONS\n";
if (function_exists('wcs_get_users_subscriptions')) {
    $user_id = get_current_user_id();
    echo "Checking subscriptions for user ID: $user_id\n";
    $subscriptions = wcs_get_users_subscriptions($user_id);
    echo "Subscriptions found: " . count($subscriptions) . "\n";
    
    if (!empty($subscriptions)) {
        foreach ($subscriptions as $subscription) {
            echo "- Subscription #" . $subscription->get_id() . " - Status: " . $subscription->get_status() . "\n";
            echo "  Products:\n";
            foreach ($subscription->get_items() as $item) {
                echo "  - " . $item->get_name() . " (ID: " . $item->get_product_id() . ")\n";
            }
        }
    }
} else {
    echo "WooCommerce Subscriptions function not available\n";
}

// Check is_premium_subscriber method
if (class_exists('CD_Vehicle_Manager')) {
    echo "\nSUBSCRIPTION CHECKS\n";
    $vm = new CD_Vehicle_Manager();
    $user_id = get_current_user_id();
    
    echo "is_premium_subscriber: " . ($vm->is_premium_subscriber($user_id) ? 'Yes' : 'No') . "\n";
    echo "is_basic_subscriber: " . ($vm->is_basic_subscriber($user_id) ? 'Yes' : 'No') . "\n";
    echo "can_add_more_vehicles: " . ($vm->can_add_more_vehicles($user_id) ? 'Yes' : 'No') . "\n";
    
    // If wcs_user_has_subscription exists, try it directly
    if (function_exists('wcs_user_has_subscription')) {
        $basic_id = get_option('cd_basic_subscription_id', '');
        $premium_id = get_option('cd_premium_subscription_id', '');
        
        echo "\nDIRECT SUBSCRIPTION CHECKS\n";
        echo "Basic ID: $basic_id, Premium ID: $premium_id\n";
        echo "wcs_user_has_subscription (basic): " . (wcs_user_has_subscription($user_id, $basic_id, 'active') ? 'Yes' : 'No') . "\n";
        echo "wcs_user_has_subscription (premium): " . (wcs_user_has_subscription($user_id, $premium_id, 'active') ? 'Yes' : 'No') . "\n";
    }
}

// Exit to prevent WordPress from adding any additional output
exit;