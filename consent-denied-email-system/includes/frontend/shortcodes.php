<?php
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode for vehicle management
add_shortcode('cd_vehicle_management', 'cd_vehicle_management_shortcode');

add_shortcode('cd_public_registration', 'cd_public_registration_shortcode');

function cd_vehicle_management_shortcode() {
    // Existing implementation remains exactly the same - no changes
    ob_start();
    
    if (!is_user_logged_in()) {
        return '<p>Please <a href="' . esc_url(wp_login_url()) . '">log in</a> to manage your vehicles.</p>';
    }
    
    $vehicle_manager = new CD_Vehicle_Manager();
    $user_id = get_current_user_id();
    $message = '';
    
    if (isset($_POST['submit_vehicle'])) {
        if ($vehicle_manager->can_add_more_vehicles($user_id)) {
            $result = $vehicle_manager->add_vehicle($user_id, $_POST);
            if ($result) {
                $message = '<div class="cd-notice success">Vehicle added successfully.</div>';
            } else {
                $message = '<div class="cd-notice error">Error adding vehicle. Please try again.</div>';
            }
        } else {
            $message = '<div class="cd-notice error">You cannot add more vehicles with your current subscription.</div>';
        }
    }

    if (isset($_POST['delete_vehicle']) && isset($_POST['vehicle_id'])) {
        $vehicle_id = intval($_POST['vehicle_id']);
        if ($vehicle_manager->delete_vehicle($vehicle_id, $user_id)) {
            $message = '<div class="cd-notice success">Vehicle removed successfully.</div>';
        } else {
            $message = '<div class="cd-notice error">Error removing vehicle.</div>';
        }
    }
    
    $vehicles = $vehicle_manager->get_user_vehicles($user_id);
    
    echo '<style>
        .cd-vehicle-management { max-width: 800px; margin: 2em auto; }
        .cd-notice { padding: 1em; margin: 1em 0; border-radius: 4px; }
        .cd-notice.success { background: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; }
        .cd-notice.error { background: #f2dede; border: 1px solid #ebccd1; color: #a94442; }
        .cd-vehicles-table { width: 100%; border-collapse: collapse; margin: 1em 0; }
        .cd-vehicles-table th, .cd-vehicles-table td { padding: 0.5em; border: 1px solid #ddd; text-align: left; }
        .cd-vehicles-table th { background: #f5f5f5; }
        .cd-add-vehicle form label { display: block; margin-top: 1em; }
        .cd-add-vehicle form input[type="text"] { width: 100%; padding: 0.5em; }
        .cd-add-vehicle form input[type="submit"] { margin-top: 1em; }
        .cd-subscription-notice { background: #f8f9fa; padding: 1em; margin: 1em 0; border: 1px solid #ddd; }
    </style>';
    
    include(plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/frontend/vehicle-management.php');
    
    return ob_get_clean();
}

// Add shortcode for subscription status
add_shortcode('cd_subscription_status', 'cd_subscription_status_shortcode');

function cd_subscription_status_shortcode() {
    // Existing implementation remains exactly the same - no changes
    if (!is_user_logged_in()) {
        return '<p>Please <a href="' . esc_url(wp_login_url()) . '">log in</a> to view your subscription status.</p>';
    }
    
    $vehicle_manager = new CD_Vehicle_Manager();
    $user_id = get_current_user_id();
    $status = $vehicle_manager->get_subscription_status($user_id);
    
    $output = '<div class="cd-subscription-status">';
    switch ($status) {
        case 'premium':
            $output .= '<p>You have an active Premium subscription. You can add unlimited vehicles.</p>';
            break;
        case 'basic':
            $output .= '<p>You have an active Basic subscription. You can add one vehicle.</p>';
            break;
        default:
            $output .= '<p>You don\'t have an active subscription. <a href="' . 
                      esc_url(get_permalink(wc_get_page_id('shop'))) . 
                      '">Subscribe now</a> to add vehicles.</p>';
    }
    $output .= '</div>';
    
    return $output;
}

// Public registration shortcode
function cd_public_registration_shortcode() {
    ob_start();
    ?>
    <div class="cd-public-registration-form">
        <form method="post" action="">
            <div class="form-section">
                <h3>Your Details</h3>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="billing_first_name">First Name *</label>
                        <input type="text" name="billing_first_name" id="billing_first_name" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="billing_last_name">Last Name *</label>
                        <input type="text" name="billing_last_name" id="billing_last_name" required>
                    </div>
                </div>
                
                <div class="form-field">
                    <label for="user_email">Email *</label>
                    <input type="email" name="user_email" id="user_email" required>
                </div>
                
                <div class="form-field">
                    <label for="mobile">Mobile Number *</label>
                    <input type="tel" name="mobile" id="mobile" required>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Postal Address</h3>
                
                <div class="form-field">
                    <label for="address_street">Street Address *</label>
                    <input type="text" name="address_street" id="address_street" required>
                </div>
                
                <div class="form-field">
                    <label for="address_line2">Address Line 2 (Optional)</label>
                    <input type="text" name="address_line2" id="address_line2">
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="address_city">City/Town *</label>
                        <input type="text" name="address_city" id="address_city" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="address_postcode">Postcode *</label>
                        <input type="text" name="address_postcode" id="address_postcode" required 
                               pattern="[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}" 
                               title="Please enter a valid UK postcode">
                    </div>
                </div>
                
            </div>
            
            <div class="form-section">
                <h3>Vehicle Details</h3>
                <h6>(Enhanced subscribers will be able to add vehicles later)</h6>
                
                <div class="form-field">
                    <label for="registration_number">Registration Number *</label>
                    <input type="text" name="registration_number" id="registration_number" required>
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="make">Make *</label>
                        <input type="text" name="make" id="make" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="model">Model *</label>
                        <input type="text" name="model" id="model" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="color">Colour *</label>
                        <input type="text" name="color" id="color" required>
                    </div>
                </div>
            </div>
            
        </form>
        
        <style>
        .cd-public-registration-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-field {
            flex: 1;
            margin-bottom: 15px;
        }
        .form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-field input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        /* Existing button styles remain, but button itself is removed */
        .form-submit {
            margin-top: 20px;
        }
        .action-button {
            display: inline-block;
            background-color: #d40000;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .action-button:hover {
            background-color: #b30000;
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        </style>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('cd_public_registration', 'cd_public_registration_shortcode');