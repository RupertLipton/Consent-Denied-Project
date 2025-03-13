<?php
if (!defined('ABSPATH')) {
    exit;
}

class CD_Vehicle_Manager {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function get_user_vehicles($user_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->wpdb->prefix}cd_vehicles 
            WHERE user_id = %d AND active = 1 
            ORDER BY date_added DESC",
            $user_id
        ));
    }
    
    public function add_vehicle($user_id, $data) {
        return $this->wpdb->insert(
            $this->wpdb->prefix . 'cd_vehicles',
            array(
                'user_id' => $user_id,
                'registration_number' => strtoupper(sanitize_text_field($data['registration_number'])),
                'make' => sanitize_text_field($data['make']),
                'model' => sanitize_text_field($data['model']),
                'color' => sanitize_text_field($data['color']),
                'active' => 1
            ),
            array('%d', '%s', '%s', '%s', '%s', '%d')
        );
    }
    
    public function delete_vehicle($vehicle_id, $user_id) {
        return $this->wpdb->update(
            $this->wpdb->prefix . 'cd_vehicles',
            array('active' => 0),
            array('id' => $vehicle_id, 'user_id' => $user_id),
            array('%d'),
            array('%d', '%d')
        );
    }
    
    public function get_vehicle_count($user_id) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->wpdb->prefix}cd_vehicles 
            WHERE user_id = %d AND active = 1",
            $user_id
        ));
    }
    
    public function is_premium_subscriber($user_id) {
        // Check if WooCommerce Subscriptions is active
        if (!function_exists('wcs_user_has_subscription')) {
            return false;
        }

        // Get subscription IDs
        $basic_subscription_id = get_option('cd_basic_subscription_id', '');
        $premium_subscription_id = get_option('cd_premium_subscription_id', '');
        
        // Check if user has either subscription active
        $has_basic = wcs_user_has_subscription($user_id, $basic_subscription_id, 'active');
        $has_premium = wcs_user_has_subscription($user_id, $premium_subscription_id, 'active');
        
        // Premium subscribers get unlimited vehicles
        return $has_premium;
    }
    
    public function is_basic_subscriber($user_id) {
        // Check if WooCommerce Subscriptions is active
        if (!function_exists('wcs_user_has_subscription')) {
            return false;
        }

        // Get basic subscription ID
        $basic_subscription_id = get_option('cd_basic_subscription_id', '');
        
        // Check if user has basic subscription active
        return wcs_user_has_subscription($user_id, $basic_subscription_id, 'active');
    }
    
    public function can_add_more_vehicles($user_id) {
        $count = $this->get_vehicle_count($user_id);
        
        // Premium subscribers get unlimited vehicles
        if ($this->is_premium_subscriber($user_id)) {
            return true;
        }
        
        // Basic subscribers get one vehicle
        if ($this->is_basic_subscriber($user_id)) {
            return $count < 1;
        }
        
        // No subscription = no vehicles
        return false;
    }

    public function get_subscription_status($user_id) {
        if ($this->is_premium_subscriber($user_id)) {
            return 'premium';
        }
        if ($this->is_basic_subscriber($user_id)) {
            return 'basic';
        }
        return 'none';
    }
}