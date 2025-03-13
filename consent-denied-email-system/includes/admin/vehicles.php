<?php
if (!defined('ABSPATH')) {
    exit;
}

function cd_vehicles_page() {
    $vehicle_manager = new CD_Vehicle_Manager();
    $message = '';
    $error = '';
    $user_id = get_current_user_id();

    // Handle form submission
    if (isset($_POST['submit_vehicle'])) {
        if ($vehicle_manager->can_add_more_vehicles($user_id)) {
            $result = $vehicle_manager->add_vehicle($user_id, $_POST);
            if ($result) {
                $message = 'Vehicle added successfully.';
            } else {
                $error = 'Error adding vehicle. Please try again.';
            }
        } else {
            $error = 'You cannot add more vehicles with your current subscription.';
        }
    }

    // Handle vehicle deletion
    if (isset($_POST['delete_vehicle']) && isset($_POST['vehicle_id'])) {
        $vehicle_id = intval($_POST['vehicle_id']);
        if ($vehicle_manager->delete_vehicle($vehicle_id, $user_id)) {
            $message = 'Vehicle removed successfully.';
        } else {
            $error = 'Error removing vehicle.';
        }
    }

    // Get user's vehicles
    $vehicles = $vehicle_manager->get_user_vehicles($user_id);
    
    // Make variables available to the template
    global $wpdb;

    // Include template
    include(plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/admin/vehicles.php');
}

function cd_enqueue_vehicles_scripts() {
    if (isset($_GET['page']) && $_GET['page'] === 'consent-denied-vehicles') {
        wp_enqueue_style('cd-admin-vehicles', 
            plugins_url('/assets/css/admin-vehicles.css', dirname(__FILE__)),
            array(),
            '1.0.0'
        );
    }
}
add_action('admin_enqueue_scripts', 'cd_enqueue_vehicles_scripts');
